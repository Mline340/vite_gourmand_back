<?php

namespace App\State;

use App\Entity\Commande;
use App\Entity\Menu;
use App\Enum\StatutCommande;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CommandeProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof Commande) {
            throw new BadRequestHttpException('Les données fournies ne sont pas valides.');
        }

        $user = $this->security->getUser();
        
        if (!$user) {
            throw new AccessDeniedHttpException('Vous devez être connecté pour effectuer cette action.');
        }

        // Déterminer si c'est une création
        $isCreation = $data->getId() === null;

        if ($isCreation) {
            // ===== CRÉATION D'UNE NOUVELLE COMMANDE =====
            
            $data->setNumeroCommande($this->genererNumeroCommande());
            $data->setDateCommande(new \DateTime());
            $data->setUser($user);
            $data->setStatut(StatutCommande::EN_ATTENTE);
            
            if ($data->isRetourMat() === null) {
                $data->setRetourMat(false);
            }
            
            $this->calculerPrixCommande($data);
            $this->calculerPrixLivraison($data);
            $this->validerCommande($data);
            
        } else {
            // ===== MODIFICATION D'UNE COMMANDE EXISTANTE =====
            
            if (!$this->security->isGranted('ROLE_ADMIN') && 
                !$this->security->isGranted('ROLE_EMPLOYE') &&
                $data->getUser() !== $user) {
                throw new AccessDeniedHttpException('Vous n\'avez pas le droit de modifier cette commande.');
            }
            
            $previousData = $context['previous_data'] ?? null;
            if ($previousData instanceof Commande) {
                $this->validerTransitionStatut($data, $previousData);
                
                if ($data->getNombrePersonne() !== $previousData->getNombrePersonne() ||
                    $data->getMenus()->count() !== $previousData->getMenus()->count()) {
                    $this->calculerPrixCommande($data);
                    $this->calculerPrixLivraison($data);
                }
            }
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }

    private function genererNumeroCommande(): string
    {
        $date = (new \DateTime())->format('Ymd');
        $random = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        return "CMD-{$date}-{$random}";
    }

    private function calculerPrixCommande(Commande $commande): void
    {
        $prixTotal = 0;
        $nombrePersonnes = $commande->getNombrePersonne() ?? 1;

        foreach ($commande->getMenus() as $menu) {
            if ($menu instanceof Menu) {
                $prixTotal += $menu->getPrixParPersonne() * $nombrePersonnes;
            }
        }

        $commande->setPrixMenu($prixTotal);
    }

    private function calculerPrixLivraison(Commande $commande): void
    {
        $prixMenu = $commande->getPrixMenu();
        
        if ($prixMenu >= 100) {
            $commande->setPrixLiv(0);
        } else {
            $commande->setPrixLiv(10.0);
        }
        
        if ($commande->isPretMat()) {
            $fraisMateriels = 15.0;
            $commande->setPrixLiv($commande->getPrixLiv() + $fraisMateriels);
        }
    }

    private function validerCommande(Commande $commande): void
    {
        if ($commande->getMenus()->isEmpty()) {
            throw new BadRequestHttpException('La commande doit contenir au moins un menu.');
        }

        $datePrestation = $commande->getDatePrestation();
        $maintenant = new \DateTime();
        $maintenant->setTime(0, 0, 0);
        
        if ($datePrestation < $maintenant) {
            throw new BadRequestHttpException('La date de prestation doit être dans le futur.');
        }

        $delaiMinimum = (new \DateTime())->modify('+2 days');
        $delaiMinimum->setTime(0, 0, 0);
        
        if ($datePrestation < $delaiMinimum) {
            throw new BadRequestHttpException('La commande doit être passée au moins 2 jours avant la date de prestation.');
        }

        if ($commande->getNombrePersonne() === null || $commande->getNombrePersonne() < 1) {
            throw new BadRequestHttpException('Le nombre de personnes doit être au moins 1.');
        }

        foreach ($commande->getMenus() as $menu) {
            if ($menu instanceof Menu) {
                if ($commande->getNombrePersonne() < $menu->getNombrePersonneMini()) {
                    throw new BadRequestHttpException(
                        sprintf(
                            'Le menu "%s" nécessite au moins %d personnes.',
                            $menu->getTitre(),
                            $menu->getNombrePersonneMini()
                        )
                    );
                }
            }
        }

        if ($commande->getHeureLiv() === null) {
            throw new BadRequestHttpException('L\'heure de livraison est obligatoire.');
        }

        $heureLiv = $commande->getHeureLiv();
        $heureDebut = \DateTime::createFromFormat('H:i', '08:00');
        $heureFin = \DateTime::createFromFormat('H:i', '20:00');
        
        if ($heureLiv < $heureDebut || $heureLiv > $heureFin) {
            throw new BadRequestHttpException('L\'heure de livraison doit être entre 8h00 et 20h00.');
        }
    }

    private function validerTransitionStatut(Commande $commande, ?Commande $previousCommande = null): void
    {
        if ($previousCommande && $previousCommande->getStatut() !== $commande->getStatut()) {
            $ancienStatut = $previousCommande->getStatut();
            $nouveauStatut = $commande->getStatut();
            
            if (!$ancienStatut->canTransitionTo($nouveauStatut)) {
                throw new BadRequestHttpException(
                    sprintf(
                        'La transition de "%s" vers "%s" n\'est pas autorisée.',
                        $ancienStatut->value,
                        $nouveauStatut->value
                    )
                );
            }
            
            if ($nouveauStatut === StatutCommande::EN_ATTENTE_RETOUR_MATERIEL) {
                if (!$commande->isPretMat()) {
                    throw new BadRequestHttpException(
                        'Le statut "En attente du retour de matériel" ne peut être appliqué que si du matériel a été prêté.'
                    );
                }
            }
            
            if ($nouveauStatut === StatutCommande::TERMINE && $commande->isPretMat()) {
                $commande->setRetourMat(true);
            }
        }
    }
}