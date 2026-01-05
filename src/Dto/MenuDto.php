<?php

namespace App\ApiResource;

use Symfony\Component\Validator\Constraints as Assert;

class MenuDto
{
    #[Assert\NotBlank(message: "Le titre est obligatoire")]
    #[Assert\Length(max: 255)]
    public ?string $titre = null;

    #[Assert\NotBlank(message: "Le nombre de personnes minimum est obligatoire")]
    #[Assert\Positive(message: "Le nombre de personnes doit être positif")]
    public ?int $nombre_personne_mini = null;

    #[Assert\NotBlank(message: "Le prix par personne est obligatoire")]
    #[Assert\Positive(message: "Le prix doit être positif")]
    public ?float $prix_par_personne = null;

    #[Assert\Length(max: 255)]
    public ?string $description = null;

    #[Assert\PositiveOrZero(message: "La quantité restante doit être positive ou nulle")]
    public ?int $quantite_restante = null;

    public ?int $themeId = null;

    /**
     * @var int[]
     */
    #[Assert\Type('array')]
    public array $regimeIds = [];

    /**
     * Tableau de plats sous forme de tableaux associatifs
     * @var array<array{titre_plat: string, photo?: string, allergeneIds?: int[]}>
     */
    #[Assert\Type('array')]
    public array $plats = [];
}