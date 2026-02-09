<?php

namespace App\Dto;

use Apiplatform\Metadata\Post;
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