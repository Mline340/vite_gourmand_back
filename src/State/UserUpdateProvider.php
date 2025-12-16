<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\UserUpdateRequest;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UserUpdateProvider implements ProviderInterface
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): UserUpdateRequest
    {
        // Vérifier que l'utilisateur existe
        $user = $this->userRepository->find($uriVariables['id'] ?? null);
        
        if (!$user) {
            throw new NotFoundHttpException('Utilisateur introuvable');
        }

        // Retourner un DTO vide qui sera hydraté par la désérialisation
        return new UserUpdateRequest();
    }
}