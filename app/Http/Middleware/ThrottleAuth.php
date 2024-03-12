<?php

namespace App\Http\Middleware;

use App\Services\RateLimiterService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psr\SimpleCache\InvalidArgumentException;
use RuntimeException;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleAuth extends RateLimiterService
{
    public function __construct(RateLimiter $limiter, Cache $cache)
    {
        parent::__construct($limiter, $cache);

        $this->maxAttempts = env('AUTH_MAX_ATTEMPTS', 10);
        $this->decaySeconds = env('AUTH_DECAY_TIME', 90);

        $this->cachePrefix = ':auth';
    }

    /**
     * Обработка входящий запрос.
     *
     * @param $request
     * @param Closure $next
     * @return Response
     * @throws InvalidArgumentException
     */
    public function handle($request, Closure $next): Response
    {
        $key = $this->resolveRequestSignature($request);

        $maxAttempts = $this->resolveMaxAttempts($request, $this->maxAttempts);

        if ($this->tooManyAttempts($key, $maxAttempts)) {
            return $this->buildException($key, $maxAttempts);
        }

        $this->hit($key, $this->decaySeconds);

        $response = $next($request);

        return $this->addHeaders(
            $response,
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }

    /**
     * Метод получения хэша по которому будет определяться ограничение запросов
     *
     * @param Request $request
     *
     * @return string
     * @throws RuntimeException
     */
    public function resolveRequestSignature(Request $request): string
    {
        if ($route = $request->route()) {
            if ($this->getClientIp()) {
                return sha1($this->getClientIp());
            } else {
                return sha1($request->ip());
            }
        }

        throw new RuntimeException('Unable to generate the request signature. Route unavailable.');
    }

    public function response($retryAfter, $headers): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Превышен лимит запросов к API',
            'retry_after' => $retryAfter
        ], 429)->withHeaders($headers);
    }
}
