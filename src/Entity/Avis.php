<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Attribute\Groups;
use ApiPlatform\Metadata\Patch;
use App\State\AvisProcessor;
use App\State\AvisProvider;
use App\Enum\StatutAvis;
use ApiPlatform\Metadata\ApiProperty;
use App\Repository\AvisRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            security: "is_granted('ROLE_USER')",
            denormalizationContext: ['groups' => ['avis:write']]
        ),
        new GetCollection(
        uriTemplate: '/avis/valides',
        security: "true",
        normalizationContext: ['groups' => ['avis:read', 'user:read:public']],
        filters: ['avis.statut_filter']
        ),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_EMPLOYE')",
            normalizationContext: ['groups' => ['avis:read', 'user:read']]
        ),
        new Get(
            security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_EMPLOYE')",
            normalizationContext: ['groups' => ['avis:read', 'user:read']]
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_EMPLOYE')",
            denormalizationContext: ['groups' => ['avis:admin']]
        )
    ]
)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['avis:read'])]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['avis:read', 'avis:write'])]
    private ?int $note = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['avis:read', 'avis:write'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['avis:read', 'avis:admin'])]
    private ?string $statut = null;

    #[ORM\ManyToOne(inversedBy: 'avis')]
    #[Groups(['avis:read', 'avis:write'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

   #[ORM\ManyToOne]
   #[ORM\JoinColumn(nullable: false)]
   #[Groups(['avis:read', 'avis:write'])]
   #[ApiProperty(readableLink: true)]
   private ?Commande $commande = null;

   #[ORM\Column(type: 'datetime', nullable: true)]
   #[Groups(['avis:read'])]
   private ?\DateTimeInterface $dateCreation = null;



    public function getStatut(): ?string
    {
    return $this->statut;
    }

    public function setStatut(?string $statut): static
    {
    $this->statut = $statut;
    return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(?int $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
    public function getCommande(): ?Commande
    {
    return $this->commande;
    }

    public function setCommande(?Commande $commande): static
    {
    $this->commande = $commande;
    return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
    return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
    $this->dateCreation = $dateCreation;
    return $this;
    }

    // Définir la date automatiquement
    #[ORM\PrePersist]
    public function setDefaultValues(): void
    {
    if ($this->statut === null) {
        $this->statut = 'en attente';
    }
    if ($this->dateCreation === null) {
        $this->dateCreation = new \DateTime();
    }
    }
}
