<?php

namespace App\Otp\Types;

class Sms extends OtpType
{
    protected $mobileNumberColumnName = 'mobile_number';

    public function send()
    {
        $this->generate();
        $this->store();
        \App\Sms\Facades\Sms::to($this->user->{$this->mobileNumberColumnName})
            ->message('Your OTP is ' . $this->generatedOtp . ' - ' . config('app.name'))
            ->queue();
    }
}