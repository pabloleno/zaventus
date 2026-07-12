<?php

namespace App\Libraries;

use CodeIgniter\Throttle\ThrottlerInterface;
use Config\Services;

class LoginAttemptLimiter
{
    public const MAX_ATTEMPTS = 5;
    public const WINDOW_SECONDS = 900;

    private ThrottlerInterface $throttler;

    public function __construct(?ThrottlerInterface $throttler = null)
    {
        $this->throttler = $throttler ?? Services::throttler();
    }

    public function canAttempt(string $ipAddress, string $usuario): bool
    {
        return $this->throttler->check(
            $this->keyFor($ipAddress, $usuario),
            self::MAX_ATTEMPTS,
            self::WINDOW_SECONDS,
            1
        );
    }

    public function retryAfterSeconds(): int
    {
        return $this->throttler->getTokenTime();
    }

    public function clear(string $ipAddress, string $usuario): void
    {
        if (method_exists($this->throttler, 'remove')) {
            $this->throttler->remove($this->keyFor($ipAddress, $usuario));
        }
    }

    public function keyFor(string $ipAddress, string $usuario): string
    {
        $usuario = strtolower(trim($usuario));
        $usuario = $usuario !== '' ? $usuario : 'anonimo';

        return 'login_' . hash('sha256', $ipAddress . '|' . $usuario);
    }
}
