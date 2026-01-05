<?php

namespace App\Entity;

use Symfony\Component\Serializer\Attribute\Groups;
use ApiPlatform\Metadata\ApiProperty;
use App\State\MenuProcessor;
use App\State\MenuProvider;
use App\Repository\MenuRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('PUBLIC_ACCESS')",
            normalizationContext: ['groups' => ['menu:read']]
        ),
        new GetCollection(
            security: "is_granted('PUBLIC_ACCESS')",
            normalizationContext: ['groups' => ['menu:read']]
        ),
        new Post(
            processor: MenuProcessor::class,
            security: "is_granted('ROLE_EMPLOYE') or is_granted('ROLE_ADMIN')",
            denormalizationContext: ['groups' => ['menu:write']]
        ),
        new Put(
            processor: MenuProcessor::class,
            security: "is_granted('ROLE_EMPLOYE') or is_granted('ROLE_ADMIN')",
            denormalizationContext: ['groups' => ['menu:write']]
        ),
        new Delete(
            processor: MenuProcessor::class,
            security: "is_granted('ROLE_EMPLOYE') or is_granted('ROLE_ADMIN')"
        )
    ],
    provider: MenuProvider::class
)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['menu:read', 'menu:write'])]
    private ?string $titre = null;

    #[ORM\Column]
    #[Groups(['menu:read', 'menu:write'])]
    private ?int $nombre_personne_mini = null;

    #[ORM\Column]
    #[Groups(['menu:read', 'menu:write'])]
    private ?float $prix_par_personne = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['menu:read', 'menu:write'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['menu:read', 'menu:write'])]
    private ?int $quantite_restante = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['menu:read', 'menu:write'])]
    private ?string $conditions = null;

    /**
     * @var Collection<int, Commande>
     */
    #[ORM\ManyToMany(targetEntity: Commande::class, inversedBy: 'menus')]
    #[Groups(['menu:read'])] 
    private Collection $commande;

    #[ORM\ManyToOne(inversedBy: 'menus')]
    #[Groups(['menu:read', 'menu:write'])]
    private ?Regime $regime = null;

    #[ORM\ManyToOne(inversedBy: 'menus')]
    #[Groups(['menu:read'])] 
    private ?Theme $theme = null;

    /**
     * @var Collection<int, Plat>
     */
    #[ORM\OneToMany(targetEntity: Plat::class, mappedBy: 'menu', cascade: ['persist', 'remove'])]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(['menu:read'])] 
    private Collection $plats;

    public function __construct()
    {
        $this->commande = new ArrayCollection();
        $this->plats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getNombrePersonneMini(): ?int
    {
        return $this->nombre_personne_mini;
    }

    public function setNombrePersonneMini(int $nombre_personne_mini): static
    {
        $this->nombre_personne_mini = $nombre_personne_mini;

        return $this;
    }

    public function getPrixParPersonne(): ?float
    {
        return $this->prix_par_personne;
    }

    public function setPrixParPersonne(float $prix_par_personne): static
    {
        $this->prix_par_personne = $prix_par_personne;

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

    public function getQuantiteRestante(): ?int
    {
        return $this->quantite_restante;
    }

    public function setQuantiteRestante(?int $quantite_restante): static
    {
        $this->quantite_restante = $quantite_restante;

        return $this;
    }

    public function getConditions(): ?string
    {
        return $this->conditions;
    }

    public function setConditions(?string $conditions): static
    {
     $this->conditions = $conditions;

     return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommande(): Collection
    {
        return $this->commande;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commande->contains($commande)) {
            $this->commande->add($commande);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        $this->commande->removeElement($commande);

        return $this;
    }

    public function getRegime(): ?Regime
    {
        return $this->regime;
    }

    public function setRegime(?Regime $regime): static
    {
        $this->regime = $regime;
        return $this;
    }

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(?Theme $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * @return Collection<int, Plat>
     */
    public function getPlats(): Collection
    {
        return $this->plats;
    }

    public function addPlat(Plat $plat): static
    {
        if (!$this->plats->contains($plat)) {
            $this->plats->add($plat);
            $plat->setMenu($this);
        }

        return $this;
    }

    public function removePlat(Plat $plat): static
    {
        if ($this->plats->removeElement($plat)) {
            // set the owning side to null (unless already changed)
            if ($plat->getMenu() === $this) {
                $plat->setMenu(null);
            }
        }

        return $this;
    }
}
