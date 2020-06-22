<?php

namespace App\Otp\Types;

use Google2FA as G2FA;

class Google2fa extends OtpType
{
    public function check($otp)
    {
        return G2FA::verifyGoogle2FA(
            $this->user->{config('otp.google2fa_secret_column_name')},
            $otp
        );
    }
}