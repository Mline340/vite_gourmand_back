<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\Post;
use App\State\PasswordForgotProcessor;

#[Post(
    uriTemplate: '/password/forgot',
    processor: PasswordForgotProcessor::class
)]
class PasswordForgotRequest
{
    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    #[Assert\Email(message: 'L\'email n\'est pas valide')]
    public ?string $email = null;
}