<?php

namespace App\DataFixtures;

use App\Entity\Regime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RegimeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $regimes = [
            'Tous',
            'Végétarien',
            'Vegan',
            'Sans gluten',
            'Classique',
            'Flexitarien',
            'Paléo'
        ];

        foreach ($regimes as $libelle) {
            $regime = new Regime();
            $regime->setLibelle($libelle); 
            $manager->persist($regime);
        }

        $manager->flush();
    }
}