<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class TokenRefresh
{
    protected $auth;

    public function __construct()
    {
        $this->auth = Auth::guard('api');
    }

    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next): mixed
    {
        if ($this->auth->getToken() && !$this->auth->user()) {
            $refreshed = $this->auth->refresh();
            $this->auth->setToken($refreshed)->getUser();
        }

        $response = $next($request);

        if (isset($refreshed)) {
            $response->headers->set('Authorization', 'Bearer ' . $refreshed);
        }

        return $response;
    }
}
