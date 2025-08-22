<?php

namespace App\Repositories\Interface;

use App\Models\Users;

interface UserRepositoryInterface
{
    public function login(string $username): ?Users;
    public function findById(int $id): ?Users;
}
