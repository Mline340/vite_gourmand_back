<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class LoginRateLimiter
{
    private const MAX_ATTEMPTS = 5;
    private const WINDOW_SECONDS = 900;

    public function __construct(private RequestStack $requestStack) {}

    public function check(string $identifier): void
    {
        $session = $this->requestStack->getSession();
        $key = 'login_attempts_' . md5($identifier);
        
        $data = $session->get($key, ['count' => 0, 'first_attempt' => time()]);

        if (time() - $data['first_attempt'] > self::WINDOW_SECONDS) {
            $data = ['count' => 0, 'first_attempt' => time()];
        }

        if ($data['count'] >= self::MAX_ATTEMPTS) {
            $remainingTime = self::WINDOW_SECONDS - (time() - $data['first_attempt']);
            throw new TooManyRequestsHttpException(
                $remainingTime,
                sprintf('Trop de tentatives. Réessayez dans %d minutes', ceil($remainingTime / 60))
            );
        }

        $data['count']++;
        $session->set($key, $data);
    }

    public function reset(string $identifier): void
    {
        $session = $this->requestStack->getSession();
        $session->remove('login_attempts_' . md5($identifier));
    }
}