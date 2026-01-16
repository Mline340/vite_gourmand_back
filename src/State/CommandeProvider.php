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

            // Sécurité : Si la commande n'appartient pas à l'utilisateur et qu'il n'est pas Admin
            if ($commande && $commande->getUSer() !== $user && !$this->security->isGranted('ROLE_ADMIN')) {
                return null; // Ou jeter une AccessDeniedException
            }

            return $commande;
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