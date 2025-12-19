<?php


namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateEmployeeInput
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    public ?string $password = null;

    #[Assert\NotBlank]
    public ?string $nom = null;

    #[Assert\NotBlank]
    public ?string $prenom = null;

}
