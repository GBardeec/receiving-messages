<?php

namespace App\Http\Controllers;

use App\Helpers\ValidateParamsHelper;
use App\Http\Requests\UserApplicationRequest;
use App\Services\UserApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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

            $this->userApplicationService->createUserApplication($user->id, $data['message']);

            return response()->json(['status' => 'success']);
        } catch (\Exception $error) {
            return response()->json(['status' => 'error', 'message' => $error->getMessage()]);
        }
    }


    public function index(Request $request): JsonResponse
    {
        $allowedParams = ['isNotActive', 'orderByDeskDate'];

        if ($response = ValidateParamsHelper::checkValidParams($request, $allowedParams)) {
            return $response;
        }

        $isNotActive = $request->get('isNotActive') == "true";
        $orderByDeskDate = $request->get('orderByDeskDate') == "true";

        $user = auth()->guard('sanctum')->user();

        if (Gate::allows('is_admin', $user)) {
            return $this->userApplicationService->getApplication($isNotActive, $orderByDeskDate);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'У вас нет прав для доступа к заявкам'
        ], 403);
    }

    public function show()
    {

    }
}
