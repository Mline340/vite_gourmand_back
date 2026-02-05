<?php

namespace App\State;

use App\Entity\Commande;
use App\Entity\Menu;
use App\Enum\StatutCommande;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CommandeProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security,
        private MailerInterface $mailer,
        private string $emailFrom,
        private string $urlSite
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

            // Sauvegarder d'abord pour avoir l'ID
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    
        // Envoyer l'email de confirmation
        try {
            $this->envoyerEmailConfirmation($data);
            error_log('✅ Email de confirmation envoyé');
        } catch (\Exception $e) {
            error_log('❌ Erreur envoi email confirmation: ' . $e->getMessage());
        }
    
        
    } else {
        // ===== MODIFICATION D'UNE COMMANDE EXISTANTE =====
        
        if (!$this->security->isGranted('ROLE_ADMIN') && 
            !$this->security->isGranted('ROLE_EMPLOYE') &&
            $data->getUser() !== $user) {
            throw new AccessDeniedHttpException('Vous n\'avez pas le droit de modifier cette commande.');
        }
        
        // Enregistrer qui a modifié et quand
        $data->setModifiedBy($user);
        $data->setModifiedAt(new \DateTime());
        
        // Ne pas écraser modifiedBy et modifiedAt si c'est juste un dépôt d'avis
    if ($context['previous_data'] ?? null) {
        $previousData = $context['previous_data'];
        if ($data->isAvisDepose() !== $previousData->isAvisDepose() && 
            $data->getStatut() === $previousData->getStatut()) {
            // C'est juste un dépôt d'avis, ne pas modifier modifiedBy/At
            $data->setModifiedBy($previousData->getModifiedBy());
            $data->setModifiedAt($previousData->getModifiedAt());
        }
    }
        // VALIDER LA TRANSITION DE STATUT seulement si le statut change
        if ($previousData instanceof Commande) {
    if ($previousData->getStatut() !== $data->getStatut()) {
        $this->validerTransitionStatut($previousData, $data);
        
        // Déduire la quantité quand la commande est acceptée
        if ($data->getStatut() === StatutCommande::ACCEPTE && 
            $previousData->getStatut() !== StatutCommande::ACCEPTE) {
            try {
                $this->deduireQuantiteMenu($data);
                error_log('✅ Quantité déduite lors de l\'acceptation de la commande');
            } catch (\Exception $e) {
                error_log('❌ Erreur déduction quantité: ' . $e->getMessage());
                throw $e;
            }
        }
    }
    
    // GARDEZ CE IF : recalcule si l'admin modifie le nombre de personnes ou les menus
    if ($data->getNombrePersonne() !== $previousData->getNombrePersonne() ||
        $data->getMenus()->count() !== $previousData->getMenus()->count()) {
        $this->calculerPrixCommande($data);
        $this->calculerPrixLivraison($data);
    }
}
        
        // Vérifier si passage à "Terminé" pour envoyer l'email
        $ancienStatut = $previousData?->getStatut();
        error_log('🔍 Ancien statut: ' . ($ancienStatut ? $ancienStatut->value : 'null'));
        error_log('🔍 Nouveau statut: ' . $data->getStatut()->value);

       if ($data->getStatut() === StatutCommande::TERMINE && $ancienStatut !== StatutCommande::TERMINE) {
            error_log('📬 Envoi email déclenché');    
            try {
                $this->envoyerEmailAvis($data);
                error_log('✅ Email envoyé avec succès');
            } catch (\Exception $e) {
                error_log('❌ Erreur envoi email: ' . $e->getMessage());
                error_log('Stack trace: ' . $e->getTraceAsString());
            }
        }
    }

            if (!$isCreation) {
            $this->entityManager->persist($data);
            error_log('💰 Prix liv JUSTE AVANT FLUSH: ' . $data->getPrixLiv());
            $this->entityManager->flush();
            error_log('💰 Prix liv JUSTE APRÈS FLUSH: ' . $data->getPrixLiv());
        }

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
    // PARTIE 1 : Si le front envoie le prix 
    if ($commande->getPrixMenu() !== null && $commande->getPrixMenu() > 0) {
        error_log('💰 Prix menu déjà fourni par le front: ' . $commande->getPrixMenu());
        return; // On arrête ici, on ne recalcule rien
    }
    
    // PARTIE 2 : Sécurité - si le front n'a PAS envoyé le prix
    error_log('⚠️ Prix menu non fourni, calcul automatique...');
    $prixTotal = 0;
    $nombrePersonnes = $commande->getNombrePersonne() ?? 1;

    foreach ($commande->getMenus() as $menu) {
        if ($menu instanceof Menu) {
            $prixTotal += $menu->getPrixParPersonne() * $nombrePersonnes;
        }
    }

    $commande->setPrixMenu($prixTotal);
    error_log('💰 Prix calculé automatiquement (sans remise): ' . $prixTotal);
}

    private function calculerPrixLivraison(Commande $commande): void
    {
          $prixLivFront = $commande->getPrixLiv();
        error_log("🔍 Prix liv reçu: " . var_export($prixLivFront, true));
    
    // Si le front a déjà calculé un prix, on le garde
    if ($prixLivFront !== null && $prixLivFront >= 0) {
        $prixArrondi = round($prixLivFront, 2);
        error_log("✅ Prix liv conservé: " . $prixArrondi);
        $commande->setPrixLiv($prixArrondi);
        return;
    }
    
    error_log("⚠️ Calcul fallback car prix_liv est null ou négatif");
    // Sinon calcul par défaut (fallback)
    $prixMenu = $commande->getPrixMenu();

    if ($prixMenu >= 100) {
        $commande->setPrixLiv(0);
    } else {
        $commande->setPrixLiv(10.0);
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

 private function validerTransitionStatut(?Commande $ancienneCommande, Commande $nouvelleCommande): void
{
    if (!$ancienneCommande) {
        return;
    }

    $ancienStatut = $ancienneCommande->getStatut();
    $nouveauStatut = $nouvelleCommande->getStatut();

    // Utilise les VALEURS string des enums, pas les enums directement
        $transitionsAutorisees = [
            'En attente' => ['Accepté', 'Annulé'],
            'Accepté' => ['En préparation', 'Annulé'],
            'En préparation' => ['En cours de livraison', 'Annulé'],
            'En cours de livraison' => ['Livré', 'Annulé'],
            'Livré' => ['En attente du retour de matériel', 'Terminé'], 
            'En attente du retour de matériel' => ['Terminé'],
            'Terminé' => [],
            'Annulé' => [],
        ];

    if (!in_array($nouveauStatut->value, $transitionsAutorisees[$ancienStatut->value] ?? [], true)) {
        throw new \InvalidArgumentException(
            sprintf('La transition de "%s" vers "%s" n\'est pas autorisée.', $ancienStatut->value, $nouveauStatut->value)
        );
    }

   if ($nouveauStatut === StatutCommande::EN_ATTENTE_RETOUR_MATERIEL) {
    if (!$nouvelleCommande->isPretMat()) {
        throw new BadRequestHttpException(
            'Le statut "En attente du retour de matériel" ne peut être appliqué que si du matériel a été prêté.'
        );
    }
    $this->envoyerEmailRetourMateriel($nouvelleCommande);
}

if ($nouveauStatut === StatutCommande::TERMINE) {
    // Si du matériel a été prêté et qu'on passe à Terminé directement depuis "Livré"
    if ($nouvelleCommande->isPretMat() && $ancienStatut->value === 'Livré') {
        throw new BadRequestHttpException(
            'Vous devez d\'abord passer par "En attente du retour de matériel" avant de terminer la commande.'
        );
    }
    
    // Si on passe à Terminé depuis "En attente du retour de matériel", marquer le retour
    if ($nouvelleCommande->isPretMat() && $ancienStatut === StatutCommande::EN_ATTENTE_RETOUR_MATERIEL) {
        $nouvelleCommande->setRetourMat(true);
    }
}
}
    private function deduireQuantiteMenu(Commande $commande): void
{
    $nombrePersonnes = $commande->getNombrePersonne();
    
    foreach ($commande->getMenus() as $menu) {
        if ($menu instanceof Menu) {
            $quantiteActuelle = $menu->getQuantiteRestante();
            $nouvelleQuantite = $quantiteActuelle - $nombrePersonnes;
            
            if ($nouvelleQuantite < 0) {
                throw new BadRequestHttpException(
                    sprintf('Stock insuffisant pour le menu "%s". Disponible: %d, Demandé: %d',
                        $menu->getTitre(),
                        $quantiteActuelle,
                        $nombrePersonnes
                    )
                );
            }
            
            $menu->setQuantiteRestante($nouvelleQuantite);
            $this->entityManager->persist($menu);
            
            error_log(sprintf('📦 Menu "%s": %d → %d', 
                $menu->getTitre(), 
                $quantiteActuelle, 
                $nouvelleQuantite
            ));
        }
    }
}
private function envoyerEmailRetourMateriel(Commande $commande): void
{
    error_log('🚀 Début envoyerEmailRetourMateriel');
    
    $client = $commande->getUser();
    if (!$client || !$client->getEmail()) {
        error_log('❌ Pas de client ou pas d\'email');
        return;
    }
    
    error_log('📧 Email client: ' . $client->getEmail());
    
    try {
        $email = (new Email())
            ->from($this->emailFrom)
            ->to($client->getEmail())
            ->subject('Retour du matériel - Commande ' . $commande->getNumeroCommande())
            ->html("
                <h2>Bonjour,</h2>
                <p>Suite à la livraison de votre commande, nous vous avons mis à disposition du matériel.</p>
                <p>Vous avez 10 jours ouvrés pour nous le restituer, sinon nous n'aurons pas le choix que de vous acquitter d'une caution de 600€.</p>
                <p>Merci de votre compréhension.</p>
                <p>L'équipe de Vite et Gourmand</p>
            ");
        
        $this->mailer->send($email);
        error_log('✅ Email retour matériel envoyé avec succès');
    } catch (\Exception $e) {
        error_log('❌ Erreur envoi email retour matériel: ' . $e->getMessage());
    }
}

    private function envoyerEmailConfirmation(Commande $commande): void
{
    error_log('🚀 Début envoyerEmailConfirmation');
    
    $client = $commande->getUser();
    if (!$client || !$client->getEmail()) {
        error_log('❌ Pas de client ou pas d\'email');
        return;
    }
    
    error_log('📧 Email client: ' . $client->getEmail());
    
    $datePrestation = $commande->getDatePrestation()->format('d/m/Y');
    $heurePrestation = $commande->getHeureLiv()->format('H:i');
    $totalCommande = $commande->getPrixMenu() + $commande->getPrixLiv();
    
    try {
        $email = (new Email())
            ->from($this->emailFrom)
            ->to($client->getEmail())
            ->subject('Confirmation de votre commande ' . $commande->getNumeroCommande())
            ->html("
                <h2>Bonjour,</h2>
                <p>Nous avons bien reçu votre commande <strong>{$commande->getNumeroCommande()}</strong>.</p>
                
                <h3>Détails de votre commande :</h3>
                <ul>
                    <li>Date de prestation : {$datePrestation}</li>
                    <li>Heure de livraison : {$heurePrestation}</li>
                    <li>Nombre de personnes : {$commande->getNombrePersonne()}</li>
                    <li>Prix menu : {$commande->getPrixMenu()} €</li>
                    <li>Frais de livraison : {$commande->getPrixLiv()} €</li>
                    <li><strong>Total : {$totalCommande} €</strong></li>
                </ul>
                
                <p>Votre commande est actuellement en attente de validation.</p>
                <p>Vous recevrez un email dès qu'elle sera acceptée.</p>
                
                <p>Merci de votre confiance !</p>
                <p>L'équipe de Vite et Gourmand</p>
            ");
        
        $this->mailer->send($email);
        error_log('✅ Email de confirmation envoyé avec succès');
    } catch (\Exception $e) {
        error_log('❌ Erreur envoi email confirmation: ' . $e->getMessage());
        throw $e;
    }
}
    
    private function envoyerEmailAvis(Commande $commande): void
{
    error_log('🚀 Début envoyerEmailAvis');
    error_log('📧 Email destinataire: ' . $commande->getUser()->getEmail());
    
    $client = $commande->getUser();
    if (!$client || !$client->getEmail()) {
        error_log('❌ Pas de client ou pas d\'email');
        return;
    }
    
    error_log('📧 Email client: ' . $client->getEmail());
    
    $lienAvis = $this->urlSite . '/mon-compte?commande=' . $commande->getId() . '#avis';
    
    try {
        $email = (new Email())
            ->from($this->emailFrom)
            ->to($client->getEmail())
            ->subject('Votre commande est terminée - Donnez votre avis')
            ->html("
                <h2>Bonjour},</h2>
                <p>Votre commande a été livrée et terminée avec succès !</p>
                <p>Vous pouvez-maintenant vous rendre sur notre site afin de nous donner votre avis.</p>
                <p>Merci de votre confiance !</p>
                <p>L'équipe de Vite et Gourmand</p>                
            ");
        
        $this->mailer->send($email);
        error_log('✅ Email envoyé avec succès');
    } catch (\Exception $e) {
        error_log('❌ Erreur envoi email: ' . $e->getMessage());
    }
    }
}