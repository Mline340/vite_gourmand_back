<?php

namespace App\State;

use App\ApiResource\MenuDto;
use App\Entity\Menu;
use App\Entity\Plat;
use App\Entity\Regime;
use App\Entity\Theme;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MenuProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?Menu
    {
        // Gestion du DELETE
        if ($operation instanceof Delete) {
            $menu = $this->entityManager->getRepository(Menu::class)->find($uriVariables['id']);
            if (!$menu) {
                throw new NotFoundHttpException('Menu non trouvé');
            }
            $this->entityManager->remove($menu);
            $this->entityManager->flush();
            return null;
        }

        // Gestion du POST et PUT
        if (!$data instanceof MenuDto) {
            throw new \InvalidArgumentException('Expected MenuDto');
        }

        // Récupérer ou créer le menu
        if ($operation instanceof Put) {
            $menu = $this->entityManager->getRepository(Menu::class)->find($uriVariables['id']);
            if (!$menu) {
                throw new NotFoundHttpException('Menu non trouvé');
            }
        } else {
            $menu = new Menu();
        }

        // Mapper les propriétés simples
        $menu->setTitre($data->titre);
        $menu->setNombrePersonneMini($data->nombre_personne_mini);
        $menu->setPrixParPersonne($data->prix_par_personne);
        $menu->setDescription($data->description);
        $menu->setQuantiteRestante($data->quantite_restante);

        // Associer le thème
        if ($data->themeId) {
            $theme = $this->entityManager->getRepository(Theme::class)->find($data->themeId);
            if (!$theme) {
                throw new NotFoundHttpException('Thème non trouvé');
            }
            $menu->setTheme($theme);
        } else {
            $menu->setTheme(null);
        }

        // Associer les régimes
        $menu->getRegimes()->clear();
        foreach ($data->regimeIds as $regimeId) {
            $regime = $this->entityManager->getRepository(Regime::class)->find($regimeId);
            if (!$regime) {
                throw new NotFoundHttpException("Régime avec l'ID $regimeId non trouvé");
            }
            $menu->addRegime($regime);
        }

        // Gérer les plats
        if ($operation instanceof Put) {
            // En mode PUT, on supprime les anciens plats
            foreach ($menu->getPlats() as $plat) {
                $menu->removePlat($plat);
                $this->entityManager->remove($plat);
            }
        }

        // Créer les nouveaux plats
        foreach ($data->plats as $platData) {
            $plat = new Plat();
            $plat->setTitrePlat($platData->titre_plat ?? '');
            $plat->setPhoto($platData->photo ?? null);
            
            $menu->addPlat($plat);
        }

        $this->entityManager->persist($menu);
        $this->entityManager->flush();

        return $menu;
    }
}