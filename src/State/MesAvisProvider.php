<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\AvisRepository;
use Symfony\Bundle\SecurityBundle\Security;

class MesAvisProvider implements ProviderInterface
{
    public function __construct(
        private AvisRepository $avisRepository,
        private Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        
        if (!$user) {
            return [];
        }
        
        return $this->avisRepository->findBy(
            ['user' => $user],
            ['dateCreation' => 'DESC']
        );
    }
}