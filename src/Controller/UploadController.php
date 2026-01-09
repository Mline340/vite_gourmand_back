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
    #[Route('/api/upload/photo', name: 'upload_photo', methods: ['POST'])]
    public function uploadPhoto(Request $request, SluggerInterface $slugger): JsonResponse
    {
        $file = $request->files->get('photo');

        if (!$file) {
            return $this->json(['error' => 'Aucun fichier reçu'], 400);
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move(
                $this->getParameter('kernel.project_dir').'/public/uploads/photos',
                $newFilename
            );
        } catch (FileException $e) {
            return $this->json(['error' => 'Erreur lors de l\'upload'], 500);
        }

        return $this->json([
            'path' => '/uploads/photos/'.$newFilename
        ]);
    }
}