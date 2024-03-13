<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class SendingReplyEmail extends Mailable
{
    private string $reply;
    private string $message;

    public function __construct(string $message, string $reply)
    {
        $this->message = $message;
        $this->reply = $reply;
    }

    public function build(): SendingReplyEmail
    {
        return $this
            ->subject("$this->message - Ваш вопрос")
            ->subject("$this->reply - ответ на вопрос")
            ->view('mail.sending-reply-email')
            ->with([
                'massage' => $this->message,
                'reply' => $this->reply
            ]);
    }
}
