<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserTemporaryPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $tempPassword;

    public $isReset;

    public function __construct(User $user, string $tempPassword, bool $isReset = false)
    {
        $this->user = $user;
        $this->tempPassword = $tempPassword;
        $this->isReset = $isReset;
    }

    public function build()
    {
        return $this->subject($this->isReset ? 'Your Password Has Been Reset' : 'Welcome to '.config('company.name'))
            ->view('emails.new-user-password');
    }
}
