<?php

namespace App\Controller;

use Cloudinary\Cloudinary;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadController extends AbstractController
{
    #[Route('/api/test-token/{token}', name: 'test_token', methods: ['GET'])]
    public function testToken(string $token, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->findOneBy(['apiToken' => $token]);
        
        return new JsonResponse([
            'found' => $user !== null,
            'email' => $user?->getEmail(),
            'actif' => $user?->isActif(),
            'roles' => $user?->getRoles(),
        ]);
    }
    
    private const MAX_FILE_SIZE = 5 * 1024 * 1024;
    
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/jpg', 
        'image/png',
        'image/webp'
    ];

    #[Route('/api/upload/photo', name: 'upload_photo', methods: ['POST'])]
    public function uploadPhoto(Request $request, SluggerInterface $slugger): JsonResponse
    {
        $file = $request->files->get('photo');

        if (!$file) {
            return $this->json(['error' => 'Aucun fichier reçu'], 400);
        }

        if ($file->getSize() > self::MAX_FILE_SIZE) {
            return $this->json(['error' => 'Fichier trop volumineux (max 5 Mo)'], 400);
        }

        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
            return $this->json(['error' => 'Format non autorisé. Formats acceptés : JPG, PNG, WEBP'], 400);
        }

        try {
            // Initialisation Cloudinary (lit automatiquement CLOUDINARY_URL)
            $cloudinary = new Cloudinary();

            // Génération d'un nom propre
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename) . '-' . uniqid();

            // Upload vers Cloudinary
            $result = $cloudinary->uploadApi()->upload(
                $file->getPathname(),
                [
                    'folder' => 'vite-gourmand/plats',
                    'public_id' => $safeFilename,
                    'overwrite' => false,
                ]
            );

            return $this->json([
                'path' => $result['secure_url']  // URL HTTPS Cloudinary
            ]);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Erreur lors de l\'upload : ' . $e->getMessage()], 500);
        }
    }
}