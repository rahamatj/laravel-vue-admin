<?php

namespace App\Otp\Types;

use App\Otp\Exceptions\NullStoredPinException;
use Illuminate\Support\Facades\Hash;

class Pin extends OtpType
{
    public function check($otp)
    {
        $storedPin = $this->user->{$this->config['pin_column_name']};

        if (!$storedPin)
            throw new NullStoredPinException('Stored pin is null.');

        return Hash::check($otp, $storedPin);
    }
}