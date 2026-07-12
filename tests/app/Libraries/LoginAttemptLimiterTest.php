<?php

namespace App\Tests\Libraries;

use App\Libraries\LoginAttemptLimiter;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Throttle\ThrottlerInterface;

final class LoginAttemptLimiterTest extends CIUnitTestCase
{
    public function testKeyDoesNotExposeRawUser(): void
    {
        $limiter = new LoginAttemptLimiter(new FakeThrottler());

        $key = $limiter->keyFor('127.0.0.1', ' ADMIN ');

        $this->assertSame($key, $limiter->keyFor('127.0.0.1', 'admin'));
        $this->assertStringStartsWith('login_', $key);
        $this->assertStringNotContainsString('admin', $key);
    }

    public function testLimiterUsesExpectedCapacityAndWindow(): void
    {
        $throttler = new FakeThrottler(false, 37);
        $limiter = new LoginAttemptLimiter($throttler);

        $this->assertFalse($limiter->canAttempt('10.0.0.1', 'usuario'));
        $this->assertSame(LoginAttemptLimiter::MAX_ATTEMPTS, $throttler->lastCapacity);
        $this->assertSame(LoginAttemptLimiter::WINDOW_SECONDS, $throttler->lastSeconds);
        $this->assertSame(1, $throttler->lastCost);
        $this->assertSame(37, $limiter->retryAfterSeconds());
    }
}

final class FakeThrottler implements ThrottlerInterface
{
    public ?int $lastCapacity = null;
    public ?int $lastSeconds = null;
    public ?int $lastCost = null;

    public function __construct(
        private bool $allowed = true,
        private int $tokenTime = 0
    ) {
    }

    public function check(string $key, int $capacity, int $seconds, int $cost = 1)
    {
        $this->lastCapacity = $capacity;
        $this->lastSeconds = $seconds;
        $this->lastCost = $cost;

        return $this->allowed;
    }

    public function getTokenTime(): int
    {
        return $this->tokenTime;
    }
}
