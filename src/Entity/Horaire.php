<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\HoraireRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: HoraireRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(security: "is_granted('PUBLIC_ACCESS')"),
        new Get(security: "is_granted('PUBLIC_ACCESS')"),
        new Post(security: "is_granted('ROLE_EMPLOYE')"),
        new Put(security: "is_granted('ROLE_EMPLOYE')"),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ],
    normalizationContext: ['groups' => ['horaire:read']],
    denormalizationContext: ['groups' => ['horaire:write']]
)]
class Horaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['horaire:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['horaire:read', 'horaire:write'])]
    private ?string $jour = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['horaire:read', 'horaire:write'])]
    private ?string $heure_ouverture = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['horaire:read', 'horaire:write'])]
    private ?string $heure_fermeture = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJour(): ?string
    {
        return $this->jour;
    }

    public function setJour(?string $jour): static
    {
        $this->jour = $jour;

        return $this;
    }

    public function getHeureOuverture(): ?string
    {
        return $this->heure_ouverture;
    }

    public function setHeureOuverture(?string $heure_ouverture): static
    {
        $this->heure_ouverture = $heure_ouverture;

        return $this;
    }

    public function getHeureFermeture(): ?string
    {
        return $this->heure_fermeture;
    }

    public function setHeureFermeture(?string $heure_fermeture): static
    {
        $this->heure_fermeture = $heure_fermeture;

        return $this;
    }
}
