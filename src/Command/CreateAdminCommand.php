<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Crée un compte administrateur',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'admmin@viteetgourmand.fr')
            ->addArgument('password', InputArgument::REQUIRED, 'Julie123!!')
            ->addArgument('nom', InputArgument::REQUIRED, 'Admin')
            ->addArgument('prenom', InputArgument::REQUIRED, 'Julie');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $nom = $input->getArgument('nom');
        $prenom = $input->getArgument('prenom');

        // Vérifier si l'utilisateur existe déjà
        $existingUser = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            $io->error('Un utilisateur avec cet email existe déjà !');
            return Command::FAILURE;
        }

        // Créer l'utilisateur
        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setTel('0600000000');
        $user->setAdresse('3 rue du pont');
        $user->setCodeP('33000');
        $user->setVille('Bordeaux');
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());

        // Hasher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);


        // Générer un token API
        $user->setApiToken(bin2hex(random_bytes(32)));


        // Sauvegarder
        $this->em->persist($user);
        $this->em->flush();

        $io->success('Administrateur créé avec succès !');
        $io->table(
            ['Champ', 'Valeur'],
            [
                ['Email', $email],
                ['Nom', $nom],
                ['Prénom', $prenom],
                ['Rôle', 'ROLE_ADMIN'],
            ]
        );

        return Command::SUCCESS;
    }
}
