<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\OwnerService;
use App\Traits\ApiResponse;

class OwnerAuth
{
    use ApiResponse;

    protected $ownerService;

    public function __construct(OwnerService $ownerService)
    {
        $this->ownerService = $ownerService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return $this->errorResponse('Token tidak ditemukan', 401);
        }

        if (!$this->ownerService->validateToken($token)) {
            return $this->errorResponse('Token tidak valid atau telah expired', 401);
        }

        return $next($request);
    }
}