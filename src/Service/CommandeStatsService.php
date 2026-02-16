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
     $collection->deleteMany([]);
    
    foreach ($commandes as $commande) {
        if ($commande->getStatut() && $commande->getStatut()->value === 'Terminé') {
            $prixCommande = (float) $commande->getPrixMenu();
            $dateTerminee = $commande->getModifiedAt(); 
            
             // Si pas de date, skip
                if (!$dateTerminee) continue;
                
            foreach ($commande->getMenus() as $menu) {
                $collection->insertOne([
                    'menu_id' => $menu->getId(),
                    'menu_titre' => $menu->getTitre(),
                    'prix_par_personne' => (float) $menu->getPrixParPersonne(),
                    'prix_commande' => $prixCommande,
                    'date_terminee' => new UTCDateTime($dateTerminee->getTimestamp() * 1000),
                ]);
            }
            
        }
    }
    
}

    public function getStats(?string $menuId = null, ?\DateTime $dateDebut = null, ?\DateTime $dateFin = null)
{
    $collection = $this->mongoService->getCollection('commande_stats');
    
    $filter = [];
    
    if ($menuId) {
        $filter['menu_id'] = (int) $menuId;
    }
    
    if ($dateDebut || $dateFin) {
        $filter['date_terminee'] = [];
        if ($dateDebut) {
            $filter['date_terminee']['$gte'] = new UTCDateTime($dateDebut->getTimestamp() * 1000);
        }
        if ($dateFin) {
            $filter['date_terminee']['$lte'] = new UTCDateTime($dateFin->modify('+1 day')->getTimestamp() * 1000);
        }
    }
    
    // Pipeline sans $match si filtre vide
    $pipeline = [];
    
    if (!empty($filter)) {
        $pipeline[] = ['$match' => $filter];
    }
    
    $pipeline[] = ['$group' => [
        '_id' => '$menu_id',
        'menu_titre' => ['$first' => '$menu_titre'],
        'total_commandes' => ['$sum' => 1],
        'prix_par_personne' => ['$first' => '$prix_par_personne'],
        'chiffre_affaires' => ['$sum' => '$prix_commande']
    ]];
    
    $pipeline[] = ['$sort' => ['total_commandes' => -1]];
    
    return $collection->aggregate($pipeline)->toArray();
}
}