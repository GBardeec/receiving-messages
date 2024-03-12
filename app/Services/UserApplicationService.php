<?php

namespace App\Services;

use App\Models\Application;

class UserApplicationService
{
    public function createUserApplication(int $userId, string $message): Application
    {
        return Application::create([
            'user_id' => $userId,
            'message' => $message,
        ]);
    }
}
