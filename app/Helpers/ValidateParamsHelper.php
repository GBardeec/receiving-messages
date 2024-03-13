<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ValidateParamsHelper
{
    public static function checkValidParams($request, $allowedParams): ?JsonResponse
    {
        $invalidParams = array_diff(array_keys($request->all()), $allowedParams);

        if (!empty($invalidParams)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Переданы неккоректные параметры'
            ], 422);
        }

        return null;
    }
}
