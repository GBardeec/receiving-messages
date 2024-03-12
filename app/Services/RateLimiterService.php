<?php

namespace App\Services;

use Closure;
use Illuminate\Cache\RateLimiter;
use RuntimeException;
use Illuminate\Support\Str;
use Illuminate\Support\InteractsWithTime;
use Symfony\Component\HttpFoundation\Response;

abstract class RateLimiterService
{
    use InteractsWithTime;

    protected $cache;
    protected $cachePrefix;
    protected $maxAttempts;
    protected $decaySeconds;

    /**
     * The rate limiter instance.
     */
    protected RateLimiter $limiter;

    /**
     * Create a new request throttler.
     *
     */
    protected function __construct(RateLimiter $limiter, $cache)
    {
        $this->limiter = $limiter;
        $this->cache = $cache;
    }

    protected function getClientIp(): bool|string
    {
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            if (array_key_exists($header, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$header]) as $ip) {
                    $ip = trim($ip);

                    $filter = filter_var(
                        $ip,
                        FILTER_VALIDATE_IP,
                        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
                    );

                    if ($filter !== false) {
                        return $ip;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Increment the counter for a given key for a given decay time.
     *
     * @param string $key
     * @param int $decaySeconds
     *
     * @return int
     */
    protected function hit(string $key, int $decaySeconds): int
    {
        $this->cache->add(
            $key . $this->cachePrefix,
            $this->availableAt($decaySeconds),
            $decaySeconds
        );

        $added = $this->cache->add($key, 0, $decaySeconds);

        $hits = (int)$this->cache->increment($key);

        if (!$added && $hits == 1) {
            $this->cache->put($key, 1, $decaySeconds);
        }

        return $hits;
    }

    /**
     * Determine if the given key has been "accessed" too many times.
     *
     * @param string $key
     * @param int $maxAttempts
     *
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    protected function tooManyAttempts(string $key, int $maxAttempts): bool
    {
        if ($this->attempts($key) >= $maxAttempts) {
            if ($this->cache->has($key . $this->cachePrefix)) {
                return true;
            }

            $this->resetAttempts($key);
        }

        return false;
    }

    /**
     * Get the number of attempts for the given key.
     *
     */
    protected function attempts(string $key): mixed
    {
        return $this->cache->get($key, 0);
    }

    /**
     * Reset the number of attempts for the given key.
     *
     */
    protected function resetAttempts(string $key): mixed
    {
        return $this->cache->forget($key);
    }

    /**
     * Clear the hits and lockout timer for the given key.
     */
    protected function clear(string $key): void
    {
        $this->resetAttempts($key);

        $this->cache->forget($key . $this->cachePrefix);
    }

    /**
     * Get the number of seconds until the "key" is accessible again.
     *
     */
    protected function availableIn(string $key): int
    {
        return $this->cache->get($key . $this->cachePrefix) - $this->currentTime();
    }

    /**
     * Resolve the number of attempts if the user is authenticated or not.
     */
    protected function resolveMaxAttempts(\Illuminate\Http\Request $request, int|string $maxAttempts): int
    {
        if (Str::contains($maxAttempts, '|')) {
            $maxAttempts = explode('|', $maxAttempts, 2)[$request->user() ? 1 : 0];
        }

        if (!is_numeric($maxAttempts) && $request->user()) {
            $maxAttempts = $request->user()->{$maxAttempts};
        }

        return (int)$maxAttempts;
    }

    /**
     * Resolve request signature.
     */
    abstract public function resolveRequestSignature(\Illuminate\Http\Request $request);

    /**
     * Create a 'too many attempts' exception.
     *
     * @param string $key
     * @param int $maxAttempts
     *
     * @return Response
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    protected function buildException(string $key, int $maxAttempts): Response
    {
        $retryAfter = $this->getTimeUntilNextRetry($key);

        $headers = $this->getHeaders(
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts, $retryAfter),
            $retryAfter
        );

        return $this->response($retryAfter, $headers);
    }

    /**
     * Get the number of seconds until the next retry.
     *
     * @param string $key
     *
     * @return int
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    protected function getTimeUntilNextRetry(string $key): int
    {
        return $this->availableIn($key);
    }

    /**
     * Add the limit header information to the given response.
     */
    protected function addHeaders(Response $response, int $maxAttempts, int $remainingAttempts, int|null $retryAfter = null)
    {
        $response->headers->add(
            $this->getHeaders($maxAttempts, $remainingAttempts, $retryAfter)
        );

        return $response;
    }

    /**
     * Get the limit headers information.
     */
    protected function getHeaders(int $maxAttempts, int $remainingAttempts, int|null $retryAfter = null): array
    {
        $headers = [
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ];

        if (!is_null($retryAfter)) {
            $headers['X-RateLimit-Retry'] = $retryAfter;
            $headers['X-RateLimit-Reset'] = $this->availableAt($retryAfter);
        }

        return $headers;
    }

    /**
     * Count the number of remaining attempts.
     *
     * @param string $key
     * @param int $maxAttempts
     * @param int|null $retryAfter
     *
     * @return int
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    protected function calculateRemainingAttempts(string $key, int $maxAttempts, int|null $retryAfter = null): int
    {
        if (is_null($retryAfter)) {
            $attempts = $this->attempts($key);

            return $maxAttempts - $attempts;
        }

        return 0;
    }

    abstract public function response($retryAfter, $headers);
}
