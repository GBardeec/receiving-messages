<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ValidateParamsHelper
{
    public static function checkValidParams($request, $allowedParams): ?JsonResponse
    {
        $invalidParams = array_diff(array_keys($request->all()), $allowedParams);

        if (!empty($invalidParams)) {
            return RespondHelper::respondJson(status: 'error', message: 'Переданы неккоректные параметры', code: 422);
        }

        return null;
    }
}
