<?php

namespace App\Otp\Types;

use App\Otp\Exceptions\EmptyGeneratedOtpException;
use App\Otp\Exceptions\NullStoredOtpException;
use Illuminate\Support\Facades\Hash;

abstract class OtpType
{
    protected $user;
    protected $generatedOtp = '';

    protected $config;

    public function __construct($user)
    {
        $this->user = $user;
        $this->config = require(__DIR__.'/../config/otp.php');
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
        return $this->user->{$this->config['otp_column_name']};
    }

    protected function generate()
    {
        for ($i = 0; $i < $this->config['generated_otp_length']; $i++)
            $this->generatedOtp .= rand(0, 9);
    }

    public function send()
    {

    }

    protected function store()
    {
        if (!$this->generatedOtp)
            throw new EmptyGeneratedOtpException('Generated OTP is empty.');

        $this->user->{$this->config['otp_column_name']} = Hash::make($this->generatedOtp);
        $this->user->save();
    }
}