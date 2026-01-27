<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\AvisRepository;

class AvisProvider implements ProviderInterface
{
    public function __construct(
        private AvisRepository $avisRepository
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof \ApiPlatform\Metadata\GetCollection) {
            $context['groups'] = array_merge($context['groups'] ?? [], ['avis:read', 'user:read:public']);
            return $this->avisRepository->findAll();
        }

        if ($operation instanceof \ApiPlatform\Metadata\Get) {
            return $this->avisRepository->find($uriVariables['id']);
        }

        return null;
    }
}