<?php

namespace App\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\RegistrationProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/registration',
            processor: RegistrationProcessor::class, 
            name: 'api_registration',
            inputFormats: ['json' => ['application/json']],
            normalizationContext: ['groups' => ['user:read']],
            openapi: new \ApiPlatform\OpenApi\Model\Operation(
                summary: 'Inscription d\'un nouvel utilisateur',
                tags: ['Security']
            )
        )
    ]
)]
class RegistrationRequest
{
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    public ?string $nom = null;

    #[Assert\NotBlank(message: 'Le prénom est obligatoire')]
    public ?string $prenom = null;

    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    #[Assert\Email(message: 'L\'email n\'est pas valide')]
    public ?string $email = null;

    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire')]
    #[Assert\Length(min: 10, minMessage: 'Le mot de passe doit contenir au moins 10 caractères')]
    public ?string $password = null;

    #[Assert\NotBlank(message: 'Le téléphone est obligatoire')]
    public ?string $tel = null;

    #[Assert\NotBlank(message: 'L\'adresse est obligatoire')]
    public ?string $adresse = null;

    #[Assert\NotBlank(message: 'Le code postal est obligatoire')]
    public ?string $codeP = null;

    #[Assert\NotBlank(message: 'La ville est obligatoire')]
    public ?string $ville = null;
}