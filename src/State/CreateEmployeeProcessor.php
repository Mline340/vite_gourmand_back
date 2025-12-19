<?php


namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\CreateEmployeeInput;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateEmployeeProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?User
    {
        if (!$data instanceof CreateEmployeeInput) {
            return null;
        }

        $user = new User();
        $user->setEmail($data->email);
        $user->setNom($data->nom);
        $user->setPrenom($data->prenom);
        $user->setRoles(['ROLE_EMPLOYE']);
        
        // Générer automatiquement createdAt
        $user->setCreatedAt(new \DateTimeImmutable());
        
        // Générer un token API unique
        $user->setApiToken(bin2hex(random_bytes(32)));
        
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data->password);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}