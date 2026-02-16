<?php
namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Service\CommandeStatsService;
use App\ApiResource\CommandeStats;
use Symfony\Component\HttpFoundation\RequestStack;

class CommandeStatsProvider implements ProviderInterface
{
    public function __construct(
        private CommandeStatsService $statsService,
        private RequestStack $requestStack
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $request = $this->requestStack->getCurrentRequest();
        
        // Récupérer les filtres depuis l'URL
        $menuId = $request?->query->get('menuId');
        $dateDebut = $request?->query->get('dateDebut');
        $dateFin = $request?->query->get('dateFin');
        
        // Convertir les dates
        $dateDebutObj = $dateDebut ? \DateTime::createFromFormat('Y-m-d', $dateDebut) : null;
        $dateFinObj = $dateFin ? \DateTime::createFromFormat('Y-m-d', $dateFin) : null;
        
        $stats = $this->statsService->getStats($menuId, $dateDebutObj, $dateFinObj);
        
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