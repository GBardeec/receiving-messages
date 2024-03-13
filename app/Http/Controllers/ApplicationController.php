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

class ApplicationController extends Controller
{

    public function __construct(private readonly UserApplicationService $userApplicationService)
    {
    }


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

    public function update(UpdateApplicationRequest $request): JsonResponse
    {
        if ($response = AccessHelper::checkAccess($request, ['isAdmin' => true])) {
            return $response;
        }

        return $this->userApplicationService->updateApplication($request->input('params.id'), $request->input('params.comment'));
    }
}
