<?php

namespace App\Otp\Traits;

use App\Client;
use App\Otp\Exceptions\ClientNotFoundException;

trait DatabaseVerification
{
    public function verify($otp, $fingerprint)
    {
        if (! $this->check($otp))
            return false;

        $this->setVerifiedAtLogin($fingerprint);
        return true;
    }

    public function setVerifiedAtLogin($fingerprint)
    {
        $client = Client::where([
            ['user_id', $this->user->id],
            ['fingerprint', $fingerprint]
        ])->first();

        if (! $client)
            throw new ClientNotFoundException('Client not found.');

        $client->is_otp_verified_at_login = true;
        $client->save();
    }

    public function isVerifiedAtLogin($fingerprint)
    {
        $client = Client::where([
            ['user_id', $this->user->id],
            ['fingerprint', $fingerprint]
        ])->first();

        if (! $client)
            throw new ClientNotFoundException('Client not found.');

        return $client->is_otp_verified_at_login;
    }

    public function logout($fingerprint)
    {
        $client = Client::where([
            ['user_id', $this->user->id],
            ['fingerprint', $fingerprint]
        ])->first();

        if (! $client)
            throw new ClientNotFoundException('Client not found.');

        $client->is_otp_verified_at_login = false;
        $client->save();
    }
}