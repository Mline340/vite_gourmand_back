<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\UserResponse;
use App\Repository\UserRepository;

class UserInfoProvider implements ProviderInterface
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupération d'un utilisateur par ID
        if (isset($uriVariables['id'])) {
            $user = $this->userRepository->find($uriVariables['id']);
            
            if (!$user) {
                return null; // Retournera une 404
            }
            
            return $this->mapToDto($user);
        }
        
        // Récupération de tous les utilisateurs (GetCollection)
        $users = $this->userRepository->findAll();
        
        return array_map(
            fn($user) => $this->mapToDto($user),
            $users
        );
    }
    
    private function mapToDto($user): UserResponse
    {
        $dto = new UserResponse();
        $dto->id = $user->getId();
        $dto->email = $user->getEmail();
        $dto->nom = $user->getNom() ?? null;
        $dto->prenom = $user->getPrenom() ?? null;
        $dto->tel = $user->getTel() ?? null;
        $dto->adresse = $user->getAdresse() ?? null;
        $dto->codeP = $user->getCodeP() ?? null;
        $dto->ville = $user->getVille() ?? null;
    
        return $dto;
    }
}