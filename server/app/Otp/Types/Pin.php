<?php

namespace App\Otp\Types;

use App\Otp\Exceptions\NullStoredPinException;
use Illuminate\Support\Facades\Hash;

class Pin extends OtpType
{
    protected $pinColumnName = 'pin';

    public function check($otp)
    {
        $storedPin = $this->user->{$this->pinColumnName};

        if (!$storedPin)
            throw new NullStoredPinException('Stored pin is null.');

        return Hash::check($otp, $storedPin);
    }
}