<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class EmployeController extends AbstractController
{
        #[Route('/api/admin/employes', name: 'get_employes', methods: ['GET'])]
        public function getEmployes(UserRepository $userRepo): JsonResponse
        {
        if (!$this->isGranted('ROLE_ADMIN')) {
        return $this->json(['error' => 'Accès refusé'], 403);
        }

        // Récupérer tous les users
        $allUsers = $userRepo->findAll();
    
        // Filtrer ceux qui ont ROLE_EMPLOYE dans leur tableau roles
        $employes = array_filter($allUsers, function($user) {
        return in_array('ROLE_EMPLOYE', $user->getRoles());
        });
    
        $data = [];
         foreach ($employes as $emp) {
        $data[] = [
            'id' => $emp->getId(),
            'nom' => $emp->getNom(),
            'prenom' => $emp->getPrenom(),
            'email' => $emp->getEmail(),
            'actif' => $emp->isActif()
        ];
    }
    
    return $this->json(array_values($data)); // array_values pour réindexer
    }
    #[Route('/api/employes/{id}/toggle', name: 'toggle_employe', methods: ['PATCH'])]
    public function toggleActif(User $user, EntityManagerInterface $em): JsonResponse
    {
        // Vérifier que c'est un admin (adapte selon ton système d'auth)
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Accès refusé'], 403);
        }

        // Vérifier que c'est bien un employé
        if (!in_array('ROLE_EMPLOYE', $user->getRoles())) {
            return $this->json(['error' => 'Pas un employé'], 400);
        }

        $user->setActif(!$user->isActif());
        $em->flush();

        return $this->json([
            'message' => 'Statut modifié',
            'actif' => $user->isActif()
        ]);
    }
}