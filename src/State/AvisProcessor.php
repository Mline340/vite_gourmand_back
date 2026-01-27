<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Avis;
use Doctrine\ORM\EntityManagerInterface;

class AvisProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof Avis) {
            // Définir le statut par défaut lors de la création
            if ($operation instanceof \ApiPlatform\Metadata\Post && !$data->getStatut()) {
                $data->setStatut('en_attente');
            }

            $this->entityManager->persist($data);
            $this->entityManager->flush();
        }

        return $data;
    }
}