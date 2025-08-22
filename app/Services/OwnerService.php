<?php

namespace App\Services;

use App\Repositories\Interface\UserRepositoryInterface;
use App\Models\Users;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Cache;

class OwnerService
{
    protected $userRepository;
    protected const OWNER_ROLE = 'owner';

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Owner login with role validation
     */
    public function login(string $username, string $password): Collection
    {
        $user = $this->userRepository->login($username, $password);
        
        if (!$user) {
            throw new AuthenticationException('Username tidak ditemukan');
        }

        // Check if user has owner role
        if ($user->role !== self::OWNER_ROLE) {
            throw new AuthenticationException('Akses ditolak. Role tidak sesuai');
        }

        // Check password
        if (!Hash::check($password, $user->password)) {
            throw new AuthenticationException('Password salah');
        }

        // Check if user is deleted
        if ($user->delete_at) {
            throw new AuthenticationException('Akun telah dihapus');
        }

        $payload = [
            'id' => $user->id,
            'user_id' => $user->user_id,
            'username' => $user->username,
            'role' => $user->role,
            'change_password_at' => $user->change_password_at,
            'deleted_at' => $user->delete_at,
            'exp' => now()->addDays(7)->timestamp,
            'type' => 'owner'
        ];

        $token = JWT::encode($payload, config('app.jwt_secret'), 'HS256');
        
        // Store token in cache for logout functionality
        Cache::put("owner_token_{$user->id}", $token, now()->addDays(7));

        return collect([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'user_id' => $user->user_id,
                'username' => $user->username,
                'role' => $user->role,
                'change_password_at' => $user->change_password_at
            ],
            'expires_at' => now()->addDays(7)->toISOString()
        ]);
    }

    /**
     * Owner logout
     */
    public function logout(string $token): bool
    {
        try {
            $decoded = JWT::decode($token, new Key(config('app.jwt_secret'), 'HS256'));
            
            if ($decoded->type !== 'owner') {
                throw new AuthenticationException('Token tidak valid untuk owner');
            }

            // Remove token from cache
            Cache::forget("owner_token_{$decoded->id}");
            
            return true;
        } catch (\Exception $e) {
            throw new AuthenticationException('Token tidak valid');
        }
    }

    /**
     * Get owner profile from token
     */
    public function getProfile(string $token): Collection
    {
        try {
            $decoded = JWT::decode($token, new Key(config('app.jwt_secret'), 'HS256'));
            
            if ($decoded->type !== 'owner') {
                throw new AuthenticationException('Token tidak valid untuk owner');
            }

            // Check if token is in cache (not logged out)
            $cachedToken = Cache::get("owner_token_{$decoded->id}");
            if (!$cachedToken || $cachedToken !== $token) {
                throw new AuthenticationException('Token telah logout');
            }

            $user = $this->userRepository->findById($decoded->id);
            
            if (!$user || $user->role !== self::OWNER_ROLE) {
                throw new AuthenticationException('User tidak ditemukan atau role tidak sesuai');
            }

            return collect([
                'id' => $user->id,
                'user_id' => $user->user_id,
                'username' => $user->username,
                'role' => $user->role,
                'change_password_at' => $user->change_password_at
            ]);
        } catch (\Exception $e) {
            throw new AuthenticationException('Token tidak valid');
        }
    }

    /**
     * Validate owner token
     */
    public function validateToken(string $token): bool
    {
        try {
            $decoded = JWT::decode($token, new Key(config('app.jwt_secret'), 'HS256'));
            
            if ($decoded->type !== 'owner') {
                return false;
            }

            // Check if token is in cache (not logged out)
            $cachedToken = Cache::get("owner_token_{$decoded->id}");
            return $cachedToken === $token;
        } catch (\Exception $e) {
            return false;
        }
    }
}