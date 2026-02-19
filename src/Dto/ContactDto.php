<?php

namespace App\Dto;

use ApiPlatform\Metadata\Post;
use App\State\ContactProcessor;

#[Post(
    uriTemplate:'/contact',
    processor: ContactProcessor::class
)]

class ContactDto
{
    public string $nom;
    public string $email;
    public string $sujet;
    public string $message;
}