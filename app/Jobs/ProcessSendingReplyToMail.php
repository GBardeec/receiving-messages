<?php

namespace App\Jobs;

use App\Mail\SendingReplyEmail;
use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessSendingReplyToMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Application $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function handle(): void
    {
        Mail::to($this->application->user->email)->send(new SendingReplyEmail($this->application->message, $this->application->comment));
    }
}
