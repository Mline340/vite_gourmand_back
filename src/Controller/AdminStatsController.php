<?php
namespace App\Controller;

use App\Service\CommandeStatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class AdminStatsController extends AbstractController
{
    public function __construct(private CommandeStatsService $statsService)
    {
    }

    #[Route('/api/admin/stats/sync', name: 'admin_stats_sync', methods: ['POST'])]
    public function sync(): JsonResponse
    {
        // Vérifier que l'utilisateur est admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        try {
            $this->statsService->synchroniserStats();
            
            return new JsonResponse([
                'success' => true,
                'message' => 'Statistiques synchronisées avec succès'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage()
            ], 500);
        }
    }
}