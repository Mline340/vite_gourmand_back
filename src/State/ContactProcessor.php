<?php
namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactProcessor implements ProcessorInterface
{
    public function __construct(
        private MailerInterface $mailer
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $email = (new Email())
            ->from($data->email) // email du visiteur
            ->to('noreply@viteetgourmand.fr') // ta boîte Mailtrap
            ->subject('Contact : ' . $data->sujet)
            ->html('<p><strong>De :</strong> ' . $data->nom . '</p><p>' . $data->message . '</p>');

        $this->mailer->send($email);

        return $data;
    }
}