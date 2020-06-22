<?php

namespace App\Otp\Types;

use App\Otp\Mail\Otp;

class Mail extends OtpType
{
    public function send()
    {
        $this->generate();
        $this->store();
        \Illuminate\Support\Facades\Mail::to($this->user->{config('otp.mail_column_name')})
            ->queue(new Otp($this->generatedOtp));
    }
}