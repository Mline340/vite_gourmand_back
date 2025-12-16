<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;


class UserUpdateRequest
{
    #[Assert\Length(max: 20)]
    public ?string $tel = null;

    #[Assert\Length(max: 255)]
    public ?string $adresse = null;

    #[Assert\Length(max: 5)]
    #[Assert\Regex(pattern: '/^[0-9]{5}$/')]
    public ?string $codeP = null;

    #[Assert\Length(max: 100)]
    public ?string $ville = null;
}