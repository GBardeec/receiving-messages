<?php

namespace App\Jobs;

use App\Services\UserApplicationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessUserApplication implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $userId;
    protected string $message;

    public function __construct(int $userId,string $message)
    {
        $this->userId = $userId;
        $this->message = $message;
    }

    public function handle(UserApplicationService $userApplicationService): void
    {
        $userApplicationService->createUserApplication($this->userId, $this->message);
    }
}
