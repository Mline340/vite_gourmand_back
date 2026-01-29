<?php
namespace App\Service;

use App\Repository\CommandeRepository;
use MongoDB\BSON\UTCDateTime;

class CommandeStatsService
{
    private $mongoService;
    private $commandeRepo;

    public function __construct(MongoDBService $mongoService, CommandeRepository $commandeRepo)
    {
        $this->mongoService = $mongoService;
        $this->commandeRepo = $commandeRepo;
    }

    public function synchroniserStats()
    {
    $collection = $this->mongoService->getCollection('commande_stats');
    
    // Récupère toutes les commandes avec leurs menus
    $commandes = $this->commandeRepo->createQueryBuilder('c')
        ->leftJoin('c.menus', 'm')
        ->addSelect('m')
        ->getQuery()
        ->getResult();
    
    // Compte les commandes par menu
    $stats = [];
    foreach ($commandes as $commande) {
        foreach ($commande->getMenus() as $menu) {
            $menuId = $menu->getId();
             $nombrePersonnes = $commande->getNombrePersonne() ?? 1;
            $prixMenu = $menu->getPrixParPersonne() * $nombrePersonnes;
            if (!isset($stats[$menuId])) {
                $stats[$menuId] = [
                    'menu_id' => $menuId,
                    'menu_titre' => $menu->getTitre(),
                    'total_commandes' => 0,
                    'prix_par_personne' => $menu->getPrixParPersonne(),
                    'chiffre_affaires' => 0
                ];
            }
            $stats[$menuId]['total_commandes']++;
            if ($commande->getStatut() && $commande->getStatut()->value === 'Terminé') {
            $stats[$menuId]['chiffre_affaires'] += $prixMenu;
}
        }
    }
    
    // Vide et réinsère dans MongoDB
    $collection->deleteMany([]);
    
    foreach ($stats as $stat) {
        $collection->insertOne([
            'menu_id' => $stat['menu_id'],
            'menu_titre' => $stat['menu_titre'],
            'total_commandes' => $stat['total_commandes'],
            'prix_par_personne' => $stat['prix_par_personne'],
            'chiffre_affaires' => $stat['chiffre_affaires']
            
        ]);
        }
    }

    public function getStats()
    {
        $collection = $this->mongoService->getCollection('commande_stats');
        return $collection->find([], ['sort' => ['total_commandes' => -1]])->toArray();
    }
}