<?php
namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\CommandeStatsProvider;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/admin/stats/commandes-par-menu',
            provider: CommandeStatsProvider::class,
        )
    ]
)]
class CommandeStats
{
    public string $menuId;
    public string $menuLibelle;
    public int $nombreCommandes;
    public float $chiffreAffaires;
    public float $prixParPersonne;
}