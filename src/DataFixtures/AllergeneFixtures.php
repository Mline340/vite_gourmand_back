<?php

namespace App\DataFixtures;

use App\Entity\Allergene;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AllergeneFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $allergenes = [
            'Arachide',
            'Céleri',
            'Crustacés',
            'Gluten',
            'Fruits à coque',
            'Lait',
            'Lupin',
            'Oeuf',
            'Poisson',
            'Mollusques',
            'Moutarde',
            'Sésame',
            'Soja',
            'Sulfites'
        ];

        foreach ($allergenes as $libelle) {
            $allergene = new Allergene();
            $allergene->setLibelle($libelle); 
            $manager->persist($allergene);
        }

        $manager->flush();
    }
}