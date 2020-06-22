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
        $user = factory(User::class)->create([
            config('otp.otp_type_column_name') => 'google2fa'
        ]);

        $google2fa = new Google2fa($user);

        $google2faSecretColumnName = config('otp.google2fa_secret_column_name');

        $user->{$google2faSecretColumnName} = 'ADUMJO5634NPDEKW';
        $user->save();

        $google2faOtp = G2FA::getCurrentOtp($user->{$google2faSecretColumnName});

        $this->assertTrue($google2fa->check($google2faOtp));
    }
}
