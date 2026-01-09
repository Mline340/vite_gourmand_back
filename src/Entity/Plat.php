<?php

namespace App\Entity;

use ApiPlatform\Metadata\Patch;
use Symfony\Component\Serializer\Attribute\Groups;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\PlatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlatRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['plat:read']],  
    denormalizationContext: ['groups' => ['plat:write']],
    operations: [
        new Get(
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new GetCollection(
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new Post(
            security: "is_granted('ROLE_EMPLOYE') or is_granted('ROLE_ADMIN')"
        ),
        new Put(
            security: "is_granted('ROLE_EMPLOYE') or is_granted('ROLE_ADMIN')"
        ),
        new Patch(security: "is_granted('ROLE_EMPLOYE') or is_granted('ROLE_ADMIN')"
        ),
        new Delete(
            security: "is_granted('ROLE_EMPLOYE') or is_granted('ROLE_ADMIN')"
        )
    ]
)]
class Plat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['plat:read', 'menu:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['plat:read', 'plat:write', 'menu:read'])]
    private ?string $titre_plat = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['plat:read', 'plat:write', 'menu:read'])]
    private ?string $photo = null;

    #[ORM\ManyToOne(inversedBy: 'plats')]
    #[Groups(['plat:read', 'plat:write'])] 
    private ?Menu $menu = null;

    /**
     * @var Collection<int, Allergene>
     */
    #[ORM\ManyToMany(targetEntity: Allergene::class, inversedBy: 'plats')]
    private Collection $allergenes;

    public function __construct()
    {
        $this->allergenes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitrePlat(): ?string
    {
        return $this->titre_plat;
    }

    public function setTitrePlat(?string $titre_plat): static
    {
        $this->titre_plat = $titre_plat;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): static
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * @return Collection<int, Allergene>
     */
    public function getAllergenes(): Collection
    {
        return $this->allergenes;
    }

    public function addAllergene(Allergene $allergene): static
    {
        if (!$this->allergenes->contains($allergene)) {
            $this->allergenes->add($allergene);
        }

        return $this;
    }

    public function removeAllergene(Allergene $allergene): static
    {
        $this->allergenes->removeElement($allergene);

        return $this;
    }
}
