<?php

namespace App\Http\Controllers;

use App\Helpers\RespondHelper;
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

    /**
     * Проводит регистрацию или аутентификацию пользователя.
     *
     * @param AuthRequest $request Объект запроса на обновление заявки.
     *
     * @responseFile 200 storage/responses/auth/200.json
     * @responseFile 403 storage/responses/auth/403.json
     * @responseFile 500 storage/responses/auth/500.json
     *
     * @return \Illuminate\Http\JsonResponse Ответ в формате JSON.
     */
    public function registrationOrAuthentication(AuthRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $user = User::query()->where('email', $data['email'])->first() ?? $this->authService->createUser($data['email'], $data['password']);

            if (!password_verify($data['password'], $user->password)) {
                return RespondHelper::respondJson(status: 'error', message: 'Вы ввели неправильный пароль.', code: 403);
            }

            Auth::login($user);

            $token = $user->createToken($user->email)->plainTextToken;

            return RespondHelper::respondJson(status: 'success', data: ['token' => $token] ,message: 'Вход успешно выполнено', code: 200);
        } catch (\Exception $error) {
            return RespondHelper::respondJson(status: 'error', message: $error->getMessage());
        }
    }
}
