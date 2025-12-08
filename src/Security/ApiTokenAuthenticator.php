<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    private UserProviderInterface $userProvider;

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function supports(Request $request): ?bool
    {
        // On active cet authenticator uniquement si le header X-AUTH-TOKEN est présent
        return $request->headers->has('X-AUTH-TOKEN');
    }

    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->headers->get('X-AUTH-TOKEN');
        if (null === $apiToken) {
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        // Utilisation de UserBadge avec une closure pour charger l’utilisateur par token
        return new SelfValidatingPassport(new UserBadge($apiToken, function(string $token) {
            $user = $this->userProvider->loadUserByIdentifier($token);
            if (!$user) {
                throw new UserNotFoundException();
            }
            return $user;
        }));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Pas d'action particulière, continuer la requête
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(
            ['message' => strtr($exception->getMessageKey(), $exception->getMessageData())],
            Response::HTTP_UNAUTHORIZED
        );
    }

    //    public function start(Request $request, ?AuthenticationException $authException = null): Response
    //    {
    //        /*
    //         * If you would like this class to control what happens when an anonymous user accesses a
    //         * protected page (e.g. redirect to /login), uncomment this method and make this class
    //         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
    //         *
    //         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
    //         */
    //    }
}
