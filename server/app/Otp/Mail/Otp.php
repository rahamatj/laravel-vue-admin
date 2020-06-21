<?php

namespace App\Otp\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Otp extends Mailable
{
    use Queueable, SerializesModels;

    public $generatedOtp;

    public function __construct($generatedOtp)
    {
        $this->generatedOtp = $generatedOtp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(config('app.name') . ' OTP')
            ->markdown('otp::emails.otp');
    }
}
