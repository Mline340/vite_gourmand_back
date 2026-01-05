<?php

namespace App\DataFixtures;

use App\Entity\Theme;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ThemeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $themes = [
            'Tous',
            'Repas de fête',
            'Cuisine du monde',
            'Pour recevoir',
            'Entre amis'
        ];

        foreach ($themes as $libelle) {
            $theme = new Theme();
            $theme->setLibelle($libelle);
            $manager->persist($theme);
        }

        $manager->flush();
    }
}