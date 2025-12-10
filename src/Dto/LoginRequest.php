<?php

namespace App\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\LoginProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/login',
            processor: LoginProcessor::class,
            name: 'api_login',
            inputFormats: ['json' => ['application/json']],
            openapi: new \ApiPlatform\OpenApi\Model\Operation(
                summary: 'Connexion d\'un utilisateur',
                tags: ['Security']
            )
        )
    ]
)]
class LoginRequest
{
    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    #[Assert\Email(message: 'L\'email n\'est pas valide')]
    public ?string $email = null;

    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire')]
    public ?string $password = null;
}