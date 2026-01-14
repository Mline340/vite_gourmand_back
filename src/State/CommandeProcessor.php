<?php

namespace App\State;

use App\Entity\Commande;
use App\Entity\Menu;
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
        // Vérifier que $data est bien une instance de Commande
        if (!$data instanceof Commande) {
            throw new BadRequestHttpException('Les données fournies ne sont pas valides.');
        }

        $user = $this->security->getUser();
        
        if (!$user) {
            throw new AccessDeniedHttpException('Vous devez être connecté pour effectuer cette action.');
        }

        // Déterminer si c'est une création (POST) ou une modification (PUT/PATCH)
        $isCreation = $operation->getName() === '_api_/commandes_post';

        if ($isCreation) {
            // ===== CRÉATION D'UNE NOUVELLE COMMANDE =====
            
            // 1. Générer un numéro de commande unique
            $data->setNumeroCommande($this->genererNumeroCommande());
            
            // 2. Définir la date de commande à maintenant
            $data->setDateCommande(new \DateTime());
            
            // 3. Associer l'utilisateur connecté
            $data->setUser($user);
            
            // 4. Définir le statut initial
            $data->setStatut('En attente');
            
            // 5. Initialiser les valeurs par défaut
            if ($data->isRetourMat() === null) {
            $data->setRetourMat(false);
            }
            
            // 6. Calculer le prix total basé sur les menus
            $this->calculerPrixCommande($data);
            
            // 7. Calculer le prix de livraison (logique à personnaliser)
            $this->calculerPrixLivraison($data);
            
            // 8. Valider les données
            $this->validerCommande($data);
            
        } else {
            // ===== MODIFICATION D'UNE COMMANDE EXISTANTE =====
            
            // Vérifier que l'utilisateur a le droit de modifier cette commande
            if (!$this->security->isGranted('ROLE_ADMIN') && 
                !$this->security->isGranted('ROLE_EMPLOYE') &&
                $data->getUser() !== $user) {
                throw new AccessDeniedHttpException('Vous n\'avez pas le droit de modifier cette commande.');
            }
            
            // Recalculer les prix si les menus ou le nombre de personnes ont changé
            if ($context['previous_data'] ?? null) {
                $previousData = $context['previous_data'];
                
                // Si le nombre de personnes ou les menus ont changé, recalculer
                if ($data->getNombrePersonne() !== $previousData->getNombrePersonne() ||
                    $data->getMenus()->count() !== $previousData->getMenus()->count()) {
                    $this->calculerPrixCommande($data);
                    $this->calculerPrixLivraison($data);
                }
            }
        }

        // Persister les données
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }

    /**
     * Générer un numéro de commande unique
     */
    private function genererNumeroCommande(): string
    {
        // Format: CMD-YYYYMMDD-XXXXX
        $date = (new \DateTime())->format('Ymd');
        $random = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        
        return "CMD-{$date}-{$random}";
    }

    /**
     * Calculer le prix total de la commande basé sur les menus
     */
    private function calculerPrixCommande(Commande $commande): void
    {
        $prixTotal = 0;
        $nombrePersonnes = $commande->getNombrePersonne() ?? 1;

        // Parcourir tous les menus de la commande
        foreach ($commande->getMenus() as $menu) {
            if ($menu instanceof Menu) {
                // Prix du menu * nombre de personnes
                $prixTotal += $menu->getPrixParPersonne() * $nombrePersonnes;
            }
        }

        $commande->setPrixMenu($prixTotal);
    }

    /**
     * Calculer le prix de livraison
     * À personnaliser selon votre logique métier
     */
    private function calculerPrixLivraison(Commande $commande): void
    {
        // Logique exemple : 
        // - Livraison gratuite si commande > 100€
        // - Sinon 10€ de frais de livraison
        
        $prixMenu = $commande->getPrixMenu();
        
        if ($prixMenu >= 100) {
            $commande->setPrixLiv(0);
        } else {
            $commande->setPrixLiv(10.0);
        }
        
        // Vous pouvez aussi faire varier selon :
        // - Le nombre de personnes
        // - Si prêt de matériel est demandé
        // - La distance de livraison, etc.
        
        if ($commande->isPretMat()) {
        // Ajouter des frais supplémentaires pour le prêt de matériel
         $fraisMateriels = 15.0;
         $commande->setPrixLiv($commande->getPrixLiv() + $fraisMateriels);
        }
    }

    /**
     * Valider la commande avant persistence
     */
    private function validerCommande(Commande $commande): void
    {
        // Vérifier qu'il y a au moins un menu
        if ($commande->getMenus()->isEmpty()) {
            throw new BadRequestHttpException('La commande doit contenir au moins un menu.');
        }

        // Vérifier que la date de prestation est dans le futur
        $datePrestation = $commande->getDatePrestation();
        $maintenant = new \DateTime();
        $maintenant->setTime(0, 0, 0);
        
        if ($datePrestation < $maintenant) {
            throw new BadRequestHttpException('La date de prestation doit être dans le futur.');
        }

        // Vérifier que la date de prestation respecte le délai minimum
        // Exemple : au moins 2 jours à l'avance
        $delaiMinimum = (new \DateTime())->modify('+2 days');
        $delaiMinimum->setTime(0, 0, 0);
        
        if ($datePrestation < $delaiMinimum) {
            throw new BadRequestHttpException('La commande doit être passée au moins 2 jours avant la date de prestation.');
        }

        // Vérifier le nombre de personnes
        if ($commande->getNombrePersonne() === null || $commande->getNombrePersonne() < 1) {
            throw new BadRequestHttpException('Le nombre de personnes doit être au moins 1.');
        }

        // Vérifier que le nombre de personnes respecte le minimum du menu
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

        // Vérifier l'heure de livraison
        if ($commande->getHeureLiv() === null) {
            throw new BadRequestHttpException('L\'heure de livraison est obligatoire.');
        }

        // Vérifier que l'heure de livraison est pendant les heures ouvrables
        // Exemple : entre 8h et 20h
        $heureLiv = $commande->getHeureLiv();
        $heureDebut = \DateTime::createFromFormat('H:i', '08:00');
        $heureFin = \DateTime::createFromFormat('H:i', '20:00');
        
        if ($heureLiv < $heureDebut || $heureLiv > $heureFin) {
            throw new BadRequestHttpException('L\'heure de livraison doit être entre 8h00 et 20h00.');
        }
    }
}