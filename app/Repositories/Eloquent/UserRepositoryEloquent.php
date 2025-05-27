<?php

namespace App\Repositories\Eloquent;

use App\Models\Users;
use App\Repositories\Interface\UserRepositoryInterface;


class UserRepositoryEloquent implements UserRepositoryInterface
{
    public function login(string $username): ?Users
    {
        return Users::where('username', $username)->first();
    }
}
