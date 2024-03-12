<?php

namespace App\Services;

use App\Models\User;

class AuthService
{
    public function createUser(string $email, string $password): User
    {
        return User::create([
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);
    }
}
