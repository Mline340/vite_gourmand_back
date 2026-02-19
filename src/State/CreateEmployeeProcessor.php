<?php


namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\CreateEmployeeInput;
use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mime\Email;

class CreateEmployeeProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private MailerInterface $mailer,
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
        $user->setActif(true);
        $user->setCreatedAt(new \DateTimeImmutable());
        
        // Générer un token API unique
        $user->setApiToken(bin2hex(random_bytes(32)));
        
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data->password);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

       // Envoie d l'email nouvel employé
        $email = (new Email())
            ->from('noreply@viteetgourmand.fr')
            ->to($user->getEmail())
            ->subject('Bienvenue dans l\'équipe!')
            ->html('<p>Bonjour ' . $user->getPrenom() . ',</p><p>Vous pouvez maintenant accéder à votre espace en ligne.</p>');

        $this->mailer->send($email);

            return $user;
        }
}
