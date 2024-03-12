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

    public function registrationOrAuthentication (AuthRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::query()->where('email', $data['email'])->first() ?? $this->authService->createUser($data['email'], $data['password']);

        abort_if(!password_verify($data['password'], $user->password), 403, 'Вы ввели неправельный пароль.');

        Auth::login($user);

        $token = $user->createToken($user->email)->plainTextToken;

        return response()->json(['success' => true, 'token' => $token]);
    }
}
