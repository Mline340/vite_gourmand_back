<?php

namespace App\Security;

use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class LoginRateLimiter
{
    private const MAX_ATTEMPTS = 5;
    private const WINDOW_SECONDS = 900;
    private string $cacheDir;

    public function __construct(string $projectDir)
    {
        $this->cacheDir = $projectDir . '/var/rate_limiter';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    public function check(string $identifier): void
    {
        $file = $this->cacheDir . '/' . md5($identifier) . '.json';
        
        $data = ['count' => 0, 'first_attempt' => time()];
        if (file_exists($file)) {
            $content = file_get_contents($file);
            $data = json_decode($content, true) ?: $data;
        }

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
        file_put_contents($file, json_encode($data));
    }

    public function reset(string $identifier): void
    {
        $file = $this->cacheDir . '/' . md5($identifier) . '.json';
        if (file_exists($file)) {
            unlink($file);
        }
    }
}