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
use App\Security\LoginRateLimiter;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

final class LoginProcessor implements ProcessorInterface
{
    
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
          private LoginRateLimiter $rateLimiter
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        if (!$data->email || !$data->password) {
            return new JsonResponse(
                ['error' => 'Email and password required'], 
                Response::HTTP_BAD_REQUEST
            );
        }

         try {
            $this->rateLimiter->check($data->email);
        } catch (TooManyRequestsHttpException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_TOO_MANY_REQUESTS
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
        if (!$user->isActif()) {
        return new JsonResponse(
        ['error' => 'Votre compte a été désactivé. Contactez un administrateur.'], 
        Response::HTTP_FORBIDDEN
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