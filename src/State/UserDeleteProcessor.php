<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Processor pour gérer la suppression d'un utilisateur
 */
class UserDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * @param User $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if (!$data instanceof User) {
            throw new BadRequestHttpException('Invalid data type');
        }

        // Vérifications optionnelles avant suppression
        // Par exemple, empêcher la suppression si l'utilisateur a des commandes en cours
        /*
        $commandesEnCours = $this->entityManager
            ->getRepository(Commande::class)
            ->findBy(['user' => $data, 'statut' => 'en_cours']);
        
        if (count($commandesEnCours) > 0) {
            throw new BadRequestHttpException(
                'Impossible de supprimer ce compte : vous avez des commandes en cours.'
            );
        }
        */

        // Suppression de l'utilisateur
        // Les relations avec cascade=['remove'] seront automatiquement supprimées
        $this->entityManager->remove($data);
        $this->entityManager->flush();
        
        // Aucun retour nécessaire pour DELETE (code 204 No Content)
    }
}