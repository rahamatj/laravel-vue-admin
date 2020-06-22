<?php

namespace Tests\Feature\Otp;

use App\Otp\Otp;
use App\Otp\Types\Pin;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OtpTest extends TestCase
{
    /** @test */
    public function returns_appropriate_otp_instance_from_users_otp_type_column()
    {
        $user = new User();

        $user->{config('otp.otp_type_column_name')} = 'pin';

        $instance = Otp::type($user);

        $this->assertTrue($instance instanceof Pin);
    }
}
