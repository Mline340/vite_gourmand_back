<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

#[AsEventListener(event: LoginSuccessEvent::class)]
class CheckUserActiveListener
{
    public function __invoke(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        
        if ($user instanceof User && !$user->isActif()) {
            throw new CustomUserMessageAuthenticationException('Votre compte a été désactivé. Contactez un administrateur.');
        }
    }
}