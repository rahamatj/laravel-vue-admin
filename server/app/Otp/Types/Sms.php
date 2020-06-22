<?php

namespace App\Otp\Types;

class Sms extends OtpType
{
    public function send()
    {
        $this->generate();
        $this->store();
        \App\Sms\Facades\Sms::to($this->user->{config('otp.mobile_number_column_name')})
            ->message('Your OTP is ' . $this->generatedOtp . ' - ' . config('app.name'))
            ->queue();
    }
}