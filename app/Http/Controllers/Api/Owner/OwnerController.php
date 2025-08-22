<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\OwnerLoginRequest;
use App\Services\OwnerService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    use ApiResponse;
    protected $ownerService;

    public function __construct(OwnerService $ownerService)
    {
        $this->ownerService = $ownerService;
    }

    /**
     * Owner login
     */
    public function login(OwnerLoginRequest $request)
    {
        try {
            $result = $this->ownerService->login($request->username, $request->password);
            return $this->successResponse($result, 'Login berhasil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 401);
        }
    }

    /**
     * Owner logout
     */
    public function logout(Request $request)
    {
        try {
            $this->ownerService->logout($request->bearerToken());
            return $this->successResponse(null, 'Logout berhasil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get owner profile
     */
    public function profile(Request $request)
    {
        try {
            $owner = $this->ownerService->getProfile($request->bearerToken());
            return $this->successResponse($owner);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 401);
        }
    }
}