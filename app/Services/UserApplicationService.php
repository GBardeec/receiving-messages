<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Http\JsonResponse;

class UserApplicationService
{
    public function createUserApplication(int $userId, string $message): Application
    {
        return Application::create([
            'user_id' => $userId,
            'message' => $message,
        ]);
    }

    public function getApplication(?bool $isNotActive, ?bool $orderByDeskDate): JsonResponse
    {
        try {
            $query = Application::query();

            $query->where('status', $isNotActive ? Application::RESOLVED : Application::ACTIVE);

            $query->orderBy('created_at', $orderByDeskDate ? 'desc' : 'asc');

            $applications = $query->with('user')->get();

            return response()->json([
                'status' => 'success',
                'application' => $applications,
            ]);
        } catch (\Exception $error) {
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage()
            ]);
        }
    }
}
