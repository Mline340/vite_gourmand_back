<?php

namespace App\Controller;

use App\State\CommandeStatsSyncProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class SyncStatsController extends AbstractController
{
    public function __construct(
        private CommandeStatsSyncProcessor $syncProcessor
    ) {}

    public function __invoke(): JsonResponse
    {
        $this->syncProcessor->synchronize();
        
        return new JsonResponse([
            'status' => 'success',
            'message' => 'Statistiques synchronisées avec MongoDB'
        ]);
    }
}