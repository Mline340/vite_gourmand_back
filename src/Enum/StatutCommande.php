<?php

namespace App\Enum;

enum StatutCommande: string
{
    case EN_ATTENTE = 'En attente';
    case ACCEPTE = 'Accepté';
    case EN_PREPARATION = 'En préparation';
    case EN_COURS_DE_LIVRAISON = 'En cours de livraison';
    case LIVRE = 'Livré';
    case EN_ATTENTE_RETOUR_MATERIEL = 'En attente du retour de matériel';
    case TERMINE = 'Terminé';
    case ANNULE = 'Annulé'; 

    /**
     * Récupérer tous les statuts possibles
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Vérifier si un statut est valide
     */
    public static function isValid(string $statut): bool
    {
        return in_array($statut, self::values(), true);
    }

    /**
     * Récupérer le statut par défaut
     */
    public static function default(): self
    {
        return self::EN_ATTENTE;
    }

    /**
     * Récupérer la couleur Bootstrap associée au statut (pour l'affichage)
     */
    public function getColor(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'warning',
            self::ACCEPTE => 'info',
            self::EN_PREPARATION => 'primary',
            self::EN_COURS_DE_LIVRAISON => 'secondary',
            self::LIVRE => 'success',
            self::EN_ATTENTE_RETOUR_MATERIEL => 'warning',
            self::TERMINE => 'success',
            self::ANNULE => 'danger',
        };
    }

    /**
     * Récupérer l'icône Bootstrap Icons associée au statut
     */
    public function getIcon(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'clock',
            self::ACCEPTE => 'check-circle',
            self::EN_PREPARATION => 'gear',
            self::EN_COURS_DE_LIVRAISON => 'truck',
            self::LIVRE => 'box-seam',
            self::EN_ATTENTE_RETOUR_MATERIEL => 'arrow-return-left',
            self::TERMINE => 'check2-circle',
            self::ANNULE => 'x-circle',
        };
    }

    /**
     * Vérifier si la commande est dans un état final
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::TERMINE, self::ANNULE]);
    }

    /**
     * Vérifier si la commande peut être modifiée
     */
    public function isModifiable(): bool
    {
        return in_array($this, [self::EN_ATTENTE, self::ACCEPTE]);
    }

    /**
     * Récupérer les transitions possibles depuis ce statut
     */
    public function getPossibleTransitions(): array
    {
        return match($this) {
            self::EN_ATTENTE => [self::ACCEPTE, self::ANNULE],
            self::ACCEPTE => [self::EN_PREPARATION, self::ANNULE],
            self::EN_PREPARATION => [self::EN_COURS_DE_LIVRAISON],
            self::EN_COURS_DE_LIVRAISON => [self::LIVRE],
            self::LIVRE => [self::EN_ATTENTE_RETOUR_MATERIEL, self::TERMINE],
            self::EN_ATTENTE_RETOUR_MATERIEL => [self::TERMINE],
            self::TERMINE => [],
            self::ANNULE => [],
        };
    }

    /**
     * Vérifier si la transition vers un autre statut est autorisée
     */
    public function canTransitionTo(StatutCommande $newStatut): bool
    {
        return in_array($newStatut, $this->getPossibleTransitions());
    }

    /**
     * Récupérer le label court pour l'affichage
     */
    public function getShortLabel(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'Attente',
            self::ACCEPTE => 'Accepté',
            self::EN_PREPARATION => 'Préparation',
            self::EN_COURS_DE_LIVRAISON => 'Livraison',
            self::LIVRE => 'Livré',
            self::EN_ATTENTE_RETOUR_MATERIEL => 'Retour matériel',
            self::TERMINE => 'Terminé',
            self::ANNULE => 'Annulé',
        };
    }
}