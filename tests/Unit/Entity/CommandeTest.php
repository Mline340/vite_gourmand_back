<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Commande;
use App\Entity\User;
use App\Enum\StatutCommande;
use PHPUnit\Framework\TestCase;

class CommandeTest extends TestCase
{
    public function testCommandeCreation(): void
    {
        $commande = new Commande();
        $dateCommande = new \DateTime();
        
        $commande->setDateCommande($dateCommande);
        $commande->setStatut(StatutCommande::EN_ATTENTE);
        $commande->setPrixMenu(35.50);
        $commande->setPrixLiv(5.00);
        
        $this->assertEquals($dateCommande, $commande->getDateCommande());
        $this->assertEquals(StatutCommande::EN_ATTENTE, $commande->getStatut());
        $this->assertEquals(35.50, $commande->getPrixMenu());
        $this->assertEquals(5.00, $commande->getPrixLiv());
    }

    public function testCommandeTotalCalculation(): void
    {
        $commande = new Commande();
        $commande->setPrixMenu(50.00);
        $commande->setPrixLiv(10.00);
        
        $totalCommande = $commande->getPrixMenu() + $commande->getPrixLiv();
        
        $this->assertEquals(60.00, $totalCommande);
    }

}