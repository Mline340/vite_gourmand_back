<?php

namespace App\State;

use App\Entity\Menu;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\ORM\EntityManagerInterface;

class MenuProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $menuRepository = $this->entityManager->getRepository(Menu::class);

        // Pour GET /menus/{id}
        if (isset($uriVariables['id'])) {
            return $menuRepository->find($uriVariables['id']);
        }

        // Pour GET /menus (collection)
        return $menuRepository->findAll();
    }
}