<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\LoginRequest;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class LoginProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        if (!$data->email || !$data->password) {
            return new JsonResponse(
                ['error' => 'Email and password required'], 
                Response::HTTP_BAD_REQUEST
            );
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $data->email]);

        if (!$user) {
            return new JsonResponse(
                ['error' => 'Invalid credentials'], 
                Response::HTTP_UNAUTHORIZED
            );
        }

        if (!$this->passwordHasher->isPasswordValid($user, $data->password)) {
            return new JsonResponse(
                ['error' => 'Invalid credentials'], 
                Response::HTTP_UNAUTHORIZED
            );
        }

        return new JsonResponse([
            'user' => $user->getUserIdentifier(),
            'apiToken' => $user->getApiToken(),
            'roles' => array_map(fn($role) => strtolower(str_replace('ROLE_', '', $role)), $user->getRoles()),
            'userId' => $user->getId(),
        ]);
    }
}