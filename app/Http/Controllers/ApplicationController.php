<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserApplicationRequest;
use App\Services\UserApplicationService;
use Illuminate\Http\JsonResponse;

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
            // Изначально еще думал обернуть все в транзакцию, но подумал, что будет излишне
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


    public function getApplication()
    {

    }
}
