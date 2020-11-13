<?php

namespace Aixieluo\LaravelSso\Http\Middleware;

use Aixieluo\LaravelSso\Exception\UnauthorizedException;
use Aixieluo\LaravelSso\Services\OAuthService;
use Closure;
use Exception;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Sso
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     * @throws UnauthorizedException
     */
    public function handle(Request $request, Closure $next)
    {
        $this->request = $request;
        $token = $this->getAccessToken();
        try {
            if ($token === session('access_token') && session('user')) {
                auth()->setUser(session('user'));
                return $next($request);
            }
            $user = app(OAuthService::class)->user($token);
            $this->success($user, $token);
            return $next($request);
        } catch (Exception $exception) {
            session(['token' => null, 'user' => null]);
            Log::info($exception);
        }
        if ($request->wantsJson()) {
            throw new UnauthorizedException();
        }
        return redirect()->route('oauth.code');
    }

    protected function getHeaderAccessToken()
    {
        return ltrim($this->request->header('Authorization'), 'Bearer ') ?: null;
    }

    protected function getQueryAccessToken()
    {
        return $this->request->input('access_token');
    }

    protected function getCookieAccessToken()
    {
        return $this->request->cookie('access_token');
    }

    protected function getRefreshToken()
    {
        return $this->request->cookie('refresh_token');
    }

    protected function getAccessToken()
    {
        return $this->getHeaderAccessToken() ?? $this->getQueryAccessToken() ?? $this->getCookieAccessToken();
    }

    /**
     * @param User $user
     * @param      $token
     */
    protected function success(User $user, $token): void
    {
        auth()->setUser($user);
        session(['access_token' => $token, 'user' => $user]);
    }
}
