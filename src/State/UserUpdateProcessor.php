<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\UserUpdateRequest;
use App\Dto\UserResponse;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UserUpdateProcessor implements ProcessorInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em
    ) {}

    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): UserResponse {
        // Récupérer l'utilisateur
        $user = $this->userRepository->find($uriVariables['id'] ?? null);

        if (!$user) {
            throw new NotFoundHttpException('Utilisateur introuvable');
        }

        // Stocker les infos avant modification
        $userId = $user->getId();
        $userEmail = $user->getEmail();
        
        // Stocker les anciennes valeurs au cas où le DTO n'aurait pas tout
        $oldTel = $user->getTel();
        $oldAdresse = $user->getAdresse();
        $oldCodeP = $user->getCodeP();
        $oldVille = $user->getVille();

        // Mettre à jour les champs depuis le DTO
        if ($data instanceof UserUpdateRequest) {
            if ($data->tel !== null) {
                $user->setTel($data->tel);
            }
            if ($data->adresse !== null) {
                $user->setAdresse($data->adresse);
            }
            if ($data->codeP !== null) {
                $user->setCodeP($data->codeP);
            }
            if ($data->ville !== null) {
                $user->setVille($data->ville);
            }
        }

        // Sauvegarder les modifications
        $this->em->flush();
        
        // IMPORTANT : Détacher toutes les entités pour éviter le lazy loading des relations
        $this->em->clear();

        // Créer la réponse avec les nouvelles valeurs
        $response = new UserResponse();
        $response->id = $userId;
        $response->email = $userEmail;
        $response->tel = $data->tel ?? $oldTel;
        $response->adresse = $data->adresse ?? $oldAdresse;
        $response->codeP = $data->codeP ?? $oldCodeP;
        $response->ville = $data->ville ?? $oldVille;

        return $response;
    }
}
