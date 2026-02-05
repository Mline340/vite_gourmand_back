<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadController extends AbstractController
{
    // Taille max : 5 Mo
    private const MAX_FILE_SIZE = 5 * 1024 * 1024;
    
    // Types MIME autorisés
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

        // 1. Vérification de la taille
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            return $this->json(['error' => 'Fichier trop volumineux (max 5 Mo)'], 400);
        }

        // 2. Vérification du type MIME réel
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
            return $this->json(['error' => 'Format non autorisé. Formats acceptés : JPG, PNG, WEBP'], 400);
        }

        // 3. Extension basée sur le MIME (plus sûr que guessExtension)
        $extension = match($mimeType) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => null
        };

        if (!$extension) {
            return $this->json(['error' => 'Type de fichier invalide'], 400);
        }

        // 4. Génération du nom de fichier sécurisé
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$extension;

        try {
            $uploadDir = $this->getParameter('kernel.project_dir').'/public/uploads/photos';
            
            // 5. Créer le dossier s'il n'existe pas
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $file->move($uploadDir, $newFilename);
        } catch (FileException $e) {
            return $this->json(['error' => 'Erreur lors de l\'upload'], 500);
        }

        return $this->json([
            'path' => '/uploads/photos/'.$newFilename
        ]);
    }
}