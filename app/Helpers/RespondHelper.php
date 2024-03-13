<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class RespondHelper
{
    public static function respondJson(string $status, array $data = [], string $message = '', int $code = null): JsonResponse
    {
        $responseData = ['status' => $status];

        if (!empty($data)) {
            $responseData['data'] = $data;
        }

        $responseData['message'] = $message;

        return response()->json($responseData, $code);
    }

}
