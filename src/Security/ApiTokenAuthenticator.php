<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

 public function supports(Request $request): ?bool
{
    $hasAuth = $request->headers->has('Authorization');
    error_log("🔍 supports() appelé - Authorization présent: " . ($hasAuth ? 'OUI' : 'NON'));
    error_log("🔍 Headers complets: " . json_encode($request->headers->all()));
    return $hasAuth;
}

public function authenticate(Request $request): Passport
{
    error_log("🔍 ApiTokenAuthenticator::authenticate() appelé");
    error_log("🔍 Headers: " . json_encode($request->headers->all()));
    
    $authHeader = $request->headers->get('Authorization');
    error_log("🔍 Authorization header reçu: " . ($authHeader ?? 'NULL'));

    if (null === $authHeader) {
        throw new CustomUserMessageAuthenticationException('No API token provided');
    }

    // Extraire le token du format "Bearer xxx"
    if (!str_starts_with($authHeader, 'Bearer ')) {
        throw new CustomUserMessageAuthenticationException('Invalid Authorization header format');
    }
    
    $apiToken = substr($authHeader, 7); // Enlever "Bearer "

    return new SelfValidatingPassport(
    new UserBadge($apiToken, function(string $token) {
        $user = $this->userRepository->findOneBy(['apiToken' => $token]);

        if (!$user) {
            error_log("❌ Aucun user trouvé avec ce token");
            throw new CustomUserMessageAuthenticationException('Invalid credentials.');
        }

        error_log("✅ User trouvé: " . $user->getEmail() . " - Actif: " . ($user->isActif() ? 'OUI' : 'NON'));

        if (!$user->isActif()) {
            error_log("❌ User désactivé");
            throw new CustomUserMessageAuthenticationException('Account disabled.');
        }

        return $user;
        })
    );
}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(
            ['message' => 'Invalid credentials.'],
            Response::HTTP_UNAUTHORIZED
        );
    }
}