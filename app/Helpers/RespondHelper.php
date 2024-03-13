<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class RespondHelper
{
    public static function respondJson(string $status, array $data = [], string $message = '', int $code = null): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'data' => $data,
            'message' => $message,
        ], $code);
    }
}
