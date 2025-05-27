<?php

namespace App\Services;

use App\Repositories\Interface\UserRepositoryInterface;
use App\Models\Users;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Firebase\JWT\JWT;
use Illuminate\Auth\AuthenticationException;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(string $username, string $password): Collection
    {

        $user = $this->userRepository->login($username, $password);
        $checkPassword = Hash::check($password, $user->password);
        if (!$user || !$checkPassword) {
            throw new AuthenticationException('username atau password salah');
        }
        $payload = [
            'id' => $user->id,
            'user_id' => $user->user_id,
            'username' => $user->username,
            'role' => $user->role,
            'change_password_at' => $user->change_password_at,
            'deleted_at' => $user->deleted_at,
            'exp' => now()->addDays(7)->timestamp
        ];
        $token = JWT::encode($payload, config('app.jwt_secret'), 'HS256');
        return collect([
            'token' => $token,
            'user' => $user
        ]);
    }
}
