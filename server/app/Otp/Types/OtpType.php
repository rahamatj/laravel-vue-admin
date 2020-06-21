<?php

namespace App\Otp\Types;

use App\Otp\Exceptions\EmptyGeneratedOtpException;
use App\Otp\Exceptions\NullStoredOtpException;
use Illuminate\Support\Facades\Hash;

abstract class OtpType
{
    protected $user;
    protected $generatedOtpLength = 8;
    protected $otpColumnName = 'otp';
    protected $generatedOtp = '';

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function check($otp)
    {
        $storedOtp = $this->getOtp();

        if (!$storedOtp)
            throw new NullStoredOtpException('Stored OTP is null.');

        return Hash::check($otp, $storedOtp);
    }

    protected function getOtp()
    {
        return $this->user->{$this->otpColumnName};
    }

    protected function generate()
    {
        for ($i = 0; $i < $this->generatedOtpLength; $i++)
            $this->generatedOtp .= rand(0, 9);
    }

    public function send()
    {

    }

    protected function store()
    {
        if (!$this->generatedOtp)
            throw new EmptyGeneratedOtpException('Generated OTP is empty.');

        $this->user->{$this->otpColumnName} = Hash::make($this->generatedOtp);
        $this->user->save();
    }
}