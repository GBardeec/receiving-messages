<?php

namespace App\Http\Controllers;

use App\Helpers\AccessHelper;
use App\Helpers\RespondHelper;
use App\Helpers\ValidateParamsHelper;
use App\Http\Requests\UpdateApplicationRequest;
use App\Http\Requests\UserApplicationRequest;
use App\Jobs\ProcessUserApplication;
use App\Services\UserApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Заявка
 * @authenticated
 */
class ApplicationController extends Controller
{

    public function __construct(private readonly UserApplicationService $userApplicationService)
    {
    }

    /**
     * Сохраняет заявку пользователя.
     *
     * @param UserApplicationRequest $request Объект запроса.
     *
     * @responseFile 200 storage/responses/application/store/200.json
     *
     * @responseFile 500 storage/responses/application/store/500.json
     *
     * @return \Illuminate\Http\JsonResponse Ответ в формате JSON.
     */
    public function store(UserApplicationRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = auth()->guard('sanctum')->user();

        try {
            // Изначально еще хотел обернуть все в транзакцию, но подумал, что будет излишне
            if (!$user->name) {
                $user->name = $data['name'];
                $user->save();
            }

            ProcessUserApplication::dispatch($user->id, $data['message']);

            return RespondHelper::respondJson(status: 'success', message: 'Заявка успешно отправлена', code: 200);
        } catch (\Exception $error) {
            return RespondHelper::respondJson(status: 'error', message: $error->getMessage());
        }
    }

    /**
     * Получает заявки пользователей.
     *
     * @param Request $request Объект запроса.
     *
     * @responseFile 200 storage/responses/application/index/200.json
     * @responseFile 400 storage/responses/application/index/400.json
     * @responseFile 403 storage/responses/application/index/403.json
     * @responseFile 500 storage/responses/application/index/500.json
     *
     * @return \Illuminate\Http\JsonResponse Ответ в формате JSON.
     */
    public function index(Request $request): JsonResponse
    {
        $allowedParams = ['isNotActive', 'orderByDeskDate'];

        if ($response = ValidateParamsHelper::checkValidParams($request, $allowedParams)) {
            return $response;
        }

        if ($response = AccessHelper::checkAccess($request, ['isAdmin' => true])) {
            return $response;
        }

        $isNotActive = $request->get('isNotActive') === "true";
        $orderByDeskDate = $request->get('orderByDeskDate') === "true";

        return $this->userApplicationService->getApplication($isNotActive, $orderByDeskDate);
    }

    /**
     * Добавляет ответ на сообщение пользователя.
     *
     * @authenticated
     *
     * @param UpdateApplicationRequest $request Объект запроса на обновление заявки.
     *
     * @responseFile 200 storage/responses/application/update/200.json
     * @responseFile 403 storage/responses/application/update/403.json
     * @responseFile 422 storage/responses/application/update/422.json
     * @responseFile 500 storage/responses/application/update/500.json
     *
     * @return \Illuminate\Http\JsonResponse Ответ в формате JSON.
     */
    public function update(UpdateApplicationRequest $request): JsonResponse
    {
        if ($response = AccessHelper::checkAccess($request, ['isAdmin' => true])) {
            return $response;
        }

        return $this->userApplicationService->updateApplication($request->input('params.id'), $request->input('params.comment'));
    }
}
