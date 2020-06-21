<?php

namespace Tests\Feature\Otp\Types;

use App\Otp\Types\Google2fa;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Google2FA as G2FA;

class Google2faTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function checks_google2fa()
    {
        $otp = new \App\Otp\Otp();
        $otpReflection = new \ReflectionClass($otp);
        $otpTypeColumnNameReflection = $otpReflection->getProperty('otpTypeColumnName');
        $otpTypeColumnNameReflection->setAccessible(true);
        $otpTypeColumnName = $otpTypeColumnNameReflection->getValue($otp);

        $user = factory(User::class)->create([
            $otpTypeColumnName => 'google2fa'
        ]);

        $google2fa = new Google2fa($user);

        $google2faReflection = new \ReflectionClass($google2fa);
        $google2faSecretColumnNameReflection = $google2faReflection->getProperty('google2faSecretColumnName');
        $google2faSecretColumnNameReflection->setAccessible(true);
        $google2faSecretColumnName = $google2faSecretColumnNameReflection->getValue($google2fa);

        $user->{$google2faSecretColumnName} = 'ADUMJO5634NPDEKW';
        $user->save();

        $google2faOtp = G2FA::getCurrentOtp($user->{$google2faSecretColumnName});

        $this->assertTrue($google2fa->check($google2faOtp));
    }
}
