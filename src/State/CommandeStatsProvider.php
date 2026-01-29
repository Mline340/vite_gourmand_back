<?php
namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Service\CommandeStatsService;
use App\ApiResource\CommandeStats;

class CommandeStatsProvider implements ProviderInterface
{
    public function __construct(private CommandeStatsService $statsService) {}

   public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
{
    $stats = $this->statsService->getStats();
    
    return array_map(function($item) {
        $stat = new CommandeStats();
        $stat->menuId = (string)$item->_id;
        $stat->menuLibelle = $item->menu_titre;
        $stat->nombreCommandes = $item->total_commandes;
        $stat->chiffreAffaires = $item->chiffre_affaires ?? 0;
        $stat->prixParPersonne = $item->prix_par_personne ?? 0;
        return $stat;
    }, $stats);
}
}