<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\PasswordForgotRequest;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class PasswordForgotProcessor implements ProcessorInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private MailerInterface $mailer
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        /** @var PasswordForgotRequest $data */
        
        // Vérifier si l'utilisateur existe
        $user = $this->userRepository->findOneBy(['email' => $data->email]);
        
        if (!$user) {
            throw new NotFoundHttpException('Aucun compte associé à cet email');
        }

        // Token fictif pour le devoir
        $resetToken = bin2hex(random_bytes(32));
        
        // Envoi de l'email
        $email = (new Email())
            ->from('noreply@viteetgourmand.fr')
            ->to($user->getEmail())
            ->subject('Réinitialisation de votre mot de passe')
            ->html(
                '<p>Bonjour ' . $user->getPrenom() . ',</p>' .
                '<p>Vous avez demandé à réinitialiser votre mot de passe.</p>' .
                '<p>Cliquez sur le lien suivant :</p>' .
                '<p><a href="http://127.0.0.1:5500/reset-password.html">Réinitialiser mon mot de passe</a></p>'.
                '<p>Si vous n\'êtes pas à l\'origine de cette demande, ignorez cet email.</p>'
            );
        
        $this->mailer->send($email);
        
        return ['message' => 'Email envoyé avec succès'];
    }
}