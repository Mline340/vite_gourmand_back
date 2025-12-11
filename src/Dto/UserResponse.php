<?php

namespace App\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\State\UserInfoProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/users/{id}',
            provider: UserInfoProvider::class
        ),
        new GetCollection(
            uriTemplate: '/users',
            provider: UserInfoProvider::class
        )
    ]
)]
class UserResponse
{
    public ?int $id = null;
    public ?string $email = null;
    public ?string $nom = null;
    public ?string $prenom = null;
    public ?string $tel = null;
    public ?string $adresse = null;
    public ?string $codeP = null;
    public ?string $ville = null;
}