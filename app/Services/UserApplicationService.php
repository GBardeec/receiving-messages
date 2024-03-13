<?php

namespace App\Services;

use App\Helpers\RespondHelper;
use App\Jobs\ProcessSendingReplyToMail;
use App\Mail\SendingReplyEmail;
use App\Models\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class UserApplicationService
{
    public function createUserApplication(int $userId, string $message): Application
    {
        return Application::create([
            'user_id' => $userId,
            'message' => $message,
        ]);
    }

    public function getApplication(?bool $isNotActive, ?bool $orderByDeskDate): JsonResponse
    {
        try {
            $query = Application::query();

            $query->where('status', $isNotActive ? Application::RESOLVED : Application::ACTIVE);

            $query->orderBy('created_at', $orderByDeskDate ? 'desc' : 'asc');

            $applications = $query->with('user')->get();

            return RespondHelper::respondJson(status: 'success', data: ['application' => $applications], code: 200);
        } catch (\Exception $error) {
            return RespondHelper::respondJson(status: 'error',  message: $error->getMessage());
        }
    }

    public function updateApplication(int $id, string $comment): JsonResponse
    {
        try {
            $application = Application::find($id);

            if ($application->status === Application::RESOLVED) {
                return RespondHelper::respondJson(status: 'error', message: 'Заявка уже рассмотрена', code: 422);
            }

            $application->comment = $comment;
            $application->status = Application::RESOLVED;

            if ($application->save()) {
                ProcessSendingReplyToMail::dispatch($application);
            }

            return RespondHelper::respondJson(status: 'success', message: 'Комментарий успешно добавлен', code: 200);
        } catch (\Exception $error) {
            return RespondHelper::respondJson(status: 'error', message: $error->getMessage());
        }
    }
}
