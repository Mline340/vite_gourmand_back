<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApiTokenUserProvider implements UserProviderInterface
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    /**
     * Charge un utilisateur par son API Token
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->userRepository->findOneBy(['apiToken' => $identifier]);

        if (!$user) {
            throw new UserNotFoundException(sprintf('User with API token "%s" not found.', $identifier));
        }

        if (!$user->isActif()) {
        throw new UserNotFoundException('Compte désactivé');
        }

        return $user;
    }

    /**
     * Rafraîchit l'utilisateur depuis la base de données
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new \InvalidArgumentException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByIdentifier($user->getApiToken());
    }

    /**
     * Indique si ce provider supporte la classe User
     */
    public function supportsClass(string $class): bool
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }
}