<?php

namespace App\Entity;

use App\Enum\StatutCommande;
use Symfony\Component\Serializer\Attribute\Groups;
use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use App\State\CommandeProcessor;
use App\State\CommandeProvider;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            provider: CommandeProvider::class,
            normalizationContext: ['groups' => ['commande:read']]
        ),
        new GetCollection(
            provider: CommandeProvider::class,
            normalizationContext: ['groups' => ['commande:read']]

        ),
        new Post(
            processor: CommandeProcessor::class,
            security: "is_granted('ROLE_USER')", 
            denormalizationContext: ['groups' => ['commande:write']]
        ),
        new Put(
            processor: CommandeProcessor::class,
            security: "is_granted('ROLE_EMPLOYE') or is_granted('ROLE_ADMIN')",
            denormalizationContext: ['groups' => ['commande:write']]
        ),
        new Patch(
            processor: CommandeProcessor::class,
            security: "is_granted('ROLE_USER') and object.getUser() == user or is_granted('ROLE_EMPLOYE') or is_granted('ROLE_ADMIN')",
            denormalizationContext: ['groups' => ['commande:write']]
        ),
        new Delete(
            security: "is_granted('ROLE_EMPLOYE') or is_granted('ROLE_ADMIN')"
        )
    ],
    provider: CommandeProvider::class
)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['commande:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['commande:read', 'avis:read'])]
    private ?string $numero_commande = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['commande:read'])]
    private ?\DateTime $date_commande = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['commande:read', 'commande:write'])]
    #[Assert\NotBlank(message: "La date de prestation est obligatoire")]
    private ?\DateTime $date_prestation = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Groups(['commande:read', 'commande:write'])]
    #[Assert\NotBlank(message: "L'heure de livraison est obligatoire")]
    private ?\DateTime $heure_liv = null;

    #[ORM\Column]
    #[Groups(['commande:read', 'commande:write'])]
    #[Assert\NotBlank]
    #[Assert\Positive(message: "Le prix du menu doit être positif")]
    private ?float $prix_menu = null;

    #[ORM\Column]
    #[Groups(['commande:read', 'commande:write'])]
    #[Assert\NotBlank]
    #[Assert\Positive(message: "Le nombre de personnes doit être positif")]
    private ?int $nombre_personne = null;

    #[ORM\Column]
    #[Groups(['commande:read',  'commande:write'])]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero(message: "Le prix de livraison doit être positif ou nul")]
    private ?float $prix_liv = null;

    #[ORM\Column(length: 255, enumType: StatutCommande::class)]
    #[Groups(['commande:read', 'commande:write'])]
    #[Assert\NotBlank]
    #[ApiProperty(
     openapiContext: [
        'type' => 'string',
        'enum' => [
            'En attente', 
            'Accepté', 
            'En préparation', 
            'En cours de livraison', 
            'Livré', 
            'En attente du retour de matériel', 
            'Terminé', 
            'Annulé'
            ],
            'example' => 'En attente'
        ]
    )]
    private ?StatutCommande $statut = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['commande:read', 'commande:write'])]
    private ?bool $pret_mat = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['commande:read'])]
    private ?bool $retour_mat = null;

    /**
     * @var Collection<int, Menu>
     */
    #[ORM\ManyToMany(targetEntity: Menu::class, inversedBy: 'commande')] 
    #[ORM\JoinTable(name: 'commande_menu')]
    #[Groups(['commande:read', 'commande:write'])]
    private Collection $menus;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['commande:read', 'commande:write'])]
    #[Assert\NotBlank(message: "L'utilisateur est obligatoire")]
    private ?User $user = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['commande:read', 'commande:write'])]
    private ?string $contactMethod = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['commande:read', 'commande:write'])]
    private ?string $modificationReason = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['commande:read'])]
    private ?User $modifiedBy = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['commande:read'])]
    private ?\DateTime $ModifiedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['commande:read', 'commande:write'])]
    private ?bool $avisDepose = null;


    public function __construct()
    {
        $this->menus = new ArrayCollection();
        $this->statut = StatutCommande::EN_ATTENTE;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroCommande(): ?string
    {
        return $this->numero_commande;
    }

    public function setNumeroCommande(string $numero_commande): static
    {
        $this->numero_commande = $numero_commande;

        return $this;
    }

    public function getDateCommande(): ?\DateTime
    {
        return $this->date_commande;
    }

    public function setDateCommande(\DateTime $date_commande): static
    {
        $this->date_commande = $date_commande;

        return $this;
    }

    public function getDatePrestation(): ?\DateTime
    {
        return $this->date_prestation;
    }

    public function setDatePrestation(\DateTime $date_prestation): static
    {
        $this->date_prestation = $date_prestation;

        return $this;
    }

    public function getHeureLiv(): ?\DateTime
    {
        return $this->heure_liv;
    }

    public function setHeureLiv(\DateTime $heure_liv): static
    {
        $this->heure_liv = $heure_liv;

        return $this;
    }

    public function getPrixMenu(): ?float
    {
        return $this->prix_menu;
    }

    public function setPrixMenu(float $prix_menu): static
    {
        $this->prix_menu = $prix_menu;

        return $this;
    }

    public function getNombrePersonne(): ?int
    {
        return $this->nombre_personne;
    }

    public function setNombrePersonne(?int $nombre_personne): static
    {
        $this->nombre_personne = $nombre_personne;

        return $this;
    }

    public function getPrixLiv(): ?float
    {
        return $this->prix_liv;
    }

    public function setPrixLiv(?float $prix_liv): static
    {
        $this->prix_liv = $prix_liv;

        return $this;
    }

    public function getStatut(): ?StatutCommande
    {
        return $this->statut;
    }

    public function setStatut(StatutCommande $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function isPretMat(): ?bool
    {
        return $this->pret_mat;
    }

    public function setPretMat(?bool $pret_mat): static
    {
        $this->pret_mat = $pret_mat;

        return $this;
    }

    public function isRetourMat(): ?bool
    {
        return $this->retour_mat;
    }

    public function setRetourMat(?bool $retour_mat): static
    {
        $this->retour_mat = $retour_mat;

        return $this;
    }

    /**
     * @return Collection<int, Menu>
     */
    public function getMenus(): Collection
    {
        return $this->menus;
    }

    public function addMenu(Menu $menu): static
    {
        if (!$this->menus->contains($menu)) {
            $this->menus->add($menu);
            $menu->addCommande($this);
        }

        return $this;
    }

    public function removeMenu(Menu $menu): static
    {
        if ($this->menus->removeElement($menu)) {
            $menu->removeCommande($this);
        }

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

    public function getContactMethod(): ?string
    {
        return $this->contactMethod;
    }

    public function setContactMethod(?string $contactMethod): static
    {
        $this->contactMethod = $contactMethod;

        return $this;
    }

    public function getModificationReason(): ?string
    {
        return $this->modificationReason;
    }

    public function setModificationReason(?string $modificationReason): static
    {
        $this->modificationReason = $modificationReason;

        return $this;
    }

    public function getModifiedBy(): ?User
    {
        return $this->modifiedBy;
    }

    public function setModifiedBy(?User $modifiedBy): static
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    public function getModifiedAt(): ?\DateTime
    {
        return $this->ModifiedAt;
    }

    public function setModifiedAt(?\DateTime $ModifiedAt): static
    {
        $this->ModifiedAt = $ModifiedAt;

        return $this;
    }
    public function isAvisDepose(): ?bool
    {
        return $this->avisDepose;
    }

    public function setAvisDepose(?bool $avisDepose): static
    {
        $this->avisDepose = $avisDepose;
        return $this;
    }
}
