<?php

namespace App\State;

use App\Entity\Menu;
use App\Entity\Plat;
use App\Entity\Regime;
use App\Entity\Theme;
use App\Entity\Allergene;
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

        // $data est maintenant directement un Menu
        if (!$data instanceof Menu) {
            throw new \InvalidArgumentException('Expected Menu entity');
        }

        $menu = $data;

        // Gérer les plats si présents dans les données brutes
        $requestData = $context['request']?->toArray() ?? [];
        
        if (isset($requestData['plats']) && is_array($requestData['plats'])) {
            // Supprimer les anciens plats en mode PUT
            if ($operation instanceof Put) {
                foreach ($menu->getPlats() as $plat) {
                    $menu->removePlat($plat);
                    $this->entityManager->remove($plat);
                }
            }

            // Créer les nouveaux plats
            foreach ($requestData['plats'] as $platData) {
                $plat = new Plat();
                $plat->setTitrePlat($platData['titre_plat']);
                $plat->setPhoto($platData['photo'] ?? null);
                $plat->setMenu($menu);

                // Gérer les allergènes
                if (isset($platData['allergeneIds']) && is_array($platData['allergeneIds'])) {
                    foreach ($platData['allergeneIds'] as $allergeneId) {
                        $allergene = $this->entityManager->getRepository(Allergene::class)->find($allergeneId);
                        if ($allergene) {
                            $plat->addAllergene($allergene);
                        }
                    }
                }
                
                $menu->addPlat($plat);
            }
        }

        // Gérer regimeIds si présent
        if (isset($requestData['regimeId'])) {
            $regime = $this->entityManager->getRepository(Regime::class)->find($requestData['regimeId']);
            if ($regime) {
            $menu->setRegime($regime);
             }
        }

        // Gérer themeId si présent
        if (isset($requestData['themeId'])) {
            $theme = $this->entityManager->getRepository(Theme::class)->find($requestData['themeId']);
            if ($theme) {
                $menu->setTheme($theme);
            }
        }

        $this->entityManager->persist($menu);
        $this->entityManager->flush();

        return $menu;
    }
}