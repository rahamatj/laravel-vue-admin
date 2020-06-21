<?php

namespace App\Otp\Types;

class Mail extends OtpType
{
    protected $emailColumnName = 'email';

    public function send()
    {
        $this->generate();
        $this->store();
        \Illuminate\Support\Facades\Mail::to($this->user->{$this->emailColumnName})
            ->queue(new \App\Mail\Otp($this->generatedOtp));
    }
}