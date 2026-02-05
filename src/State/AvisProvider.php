<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\AvisRepository;
use Symfony\Bundle\SecurityBundle\Security;

class AvisProvider implements ProviderInterface
{
    public function __construct(
        private AvisRepository $avisRepository,
        private Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof \ApiPlatform\Metadata\GetCollection) {
            // Si c'est la route /avis/valides, filtrer uniquement les validés
            if (str_contains($operation->getUriTemplate(), '/avis/valides')) {
                return $this->avisRepository->findBy(
                    ['statut' => 'Validé'],
                    ['dateCreation' => 'DESC']
                );
            }
            
            // Pour /api/avis (admin/employé), retourner tous les avis
            return $this->avisRepository->findBy([], ['dateCreation' => 'DESC']);
        }

        if ($operation instanceof \ApiPlatform\Metadata\Get) {
            return $this->avisRepository->find($uriVariables['id']);
        }

        return null;
    }
}