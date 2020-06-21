<?php

namespace App\Otp\Types;

use App\Otp\Mail\Otp;

class Mail extends OtpType
{
    protected $emailColumnName = 'email';

    public function send()
    {
        $this->generate();
        $this->store();
        \Illuminate\Support\Facades\Mail::to($this->user->{$this->emailColumnName})
            ->queue(new Otp($this->generatedOtp));
    }
}