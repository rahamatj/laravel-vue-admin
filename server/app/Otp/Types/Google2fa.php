<?php

namespace App\Otp\Types;

use Google2FA as G2FA;

class Google2fa extends OtpType
{
    protected $google2faSecretColumnName = 'google2fa_secret';

    public function check($otp)
    {
        return G2FA::verifyGoogle2FA(
            $this->user->{$this->google2faSecretColumnName},
            $otp
        );
    }
}