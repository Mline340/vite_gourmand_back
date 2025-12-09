<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $numero_commande = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_commande = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_prestation = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $heure_liv = null;

    #[ORM\Column]
    private ?float $prix_menu = null;

    #[ORM\Column(nullable: true)]
    private ?int $nombre_personne = null;

    #[ORM\Column(nullable: true)]
    private ?float $prix_liv = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(nullable: true)]
    private ?bool $pret_mat = null;

    #[ORM\Column(nullable: true)]
    private ?bool $retour_mat = null;

    /**
     * @var Collection<int, Menu>
     */
    #[ORM\ManyToMany(targetEntity: Menu::class, mappedBy: 'commande')]
    private Collection $menus;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?User $USer = null;

    public function __construct()
    {
        $this->menus = new ArrayCollection();
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

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
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

    public function getUSer(): ?User
    {
        return $this->USer;
    }

    public function setUSer(?User $USer): static
    {
        $this->USer = $USer;

        return $this;
    }
}
