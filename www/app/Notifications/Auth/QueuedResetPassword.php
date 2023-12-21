<?php

namespace App\Notifications\Auth;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class QueuedResetPassword extends ResetPassword implements ShouldQueue
{
    use Queueable;
}
