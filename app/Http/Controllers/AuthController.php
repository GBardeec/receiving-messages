<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService)
    {

    }

    public function registrationOrAuthentication(AuthRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $user = User::query()->where('email', $data['email'])->first() ?? $this->authService->createUser($data['email'], $data['password']);

            if (!password_verify($data['password'], $user->password)) {
                return response()->json(['status' => 'error', 'message' => 'Вы ввели неправильный пароль.'], 403);
            }

            Auth::login($user);

            $token = $user->createToken($user->email)->plainTextToken;

            return response()->json(['status' => 'success', 'token' => $token]);
        } catch (\Exception $error) {
            return response()->json(['status' => 'error', 'message' => $error->getMessage()]);
        }
    }
}
