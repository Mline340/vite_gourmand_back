<?php

namespace App\State;

use App\Entity\Commande;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class CommandeProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $repository = $this->entityManager->getRepository(Commande::class);
        $user = $this->security->getUser();

        // 1. Cas du GET unique : /commandes/{id}
        if (isset($uriVariables['id'])) {
            $commande = $repository->find($uriVariables['id']);

            if (!$commande) {
                return null;
            }

            // Sécurité : Employés et Admins peuvent voir toutes les commandes
            if ($this->security->isGranted('ROLE_EMPLOYE') || $this->security->isGranted('ROLE_ADMIN')) {
                return $commande;
            }

            // Utilisateur peut voir uniquement ses propres commandes
            if ($commande->getUser() === $user) {
                return $commande;
            }

            // Si aucune condition n'est remplie, accès refusé
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        // 2. Cas de la Collection : /commandes
        // Si c'est un Admin ou Employé, il voit tout
        if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_EMPLOYE')) {
            return $repository->findAll();
        }

        // Sinon, on ne retourne que les commandes liées à l'utilisateur connecté
        return $repository->findBy(['user' => $user]);
    }
}