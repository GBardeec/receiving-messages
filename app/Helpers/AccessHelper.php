<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AccessHelper
{
    public static function checkAccess(Request $request, array $parameters): ?JsonResponse
    {
        $user = auth()->guard('sanctum')->user();

        foreach ($parameters as $param => $value) {
            switch ($param) {
                case 'isAdmin':
                    if ($value && !Gate::allows('is_admin', $user)) {
                        return RespondHelper::respondJson(status: 'error', message: 'Недостаточно прав доступа', code: 403);
                    }
                    break;
            }
        }

        return null;
    }
}
