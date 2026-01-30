<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\RegistrationRequest;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


final class RegistrationProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $manager,
        private UserPasswordHasherInterface $passwordHasher,
        private MailerInterface $mailer
    ) {}

    /**
     * @param RegistrationRequest $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        // Créer un nouvel utilisateur
        $user = new User();
        $user->setEmail($data->email);
        $user->setNom($data->nom);
        $user->setPrenom($data->prenom);
        $user->setTel($data->tel);
        $user->setAdresse($data->adresse);
        $user->setCodeP($data->codeP);
        $user->setVille($data->ville);

        // Hash le mot de passe
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $data->password)
        );
        
        // Définit la date de création
        $user->setCreatedAt(new DateTimeImmutable());
        
        // Définit les rôles par défaut (ROLE_USER est déjà ajouté dans getRoles())
        $user->setRoles(['ROLE_USER']);
        
        // L'apiToken est généré automatiquement dans le constructeur de User
        
        // Persiste l'utilisateur
        $this->manager->persist($user);
        $this->manager->flush();

        // Envoi de l'email de bienvenue
        $email = (new Email())
            ->from('noreply@viteetgourmand.fr')
            ->to($user->getEmail())
            ->subject('Bienvenue sur notre site !')
            ->html('<p>Bonjour ' . $user->getPrenom() . ',</p><p>Merci pour votre inscription !</p>');
        
        $this->mailer->send($email);
        
        return $user;
    }
}