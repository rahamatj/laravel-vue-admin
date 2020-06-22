<?php

namespace Tests\Feature\Otp\Types;

use App\Otp\Exceptions\NullStoredPinException;
use App\Otp\Types\Pin;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PinTest extends TestCase
{
    /** @test */
    public function checks_pin()
    {
        $user = factory(User::class)->create();

        $pin = new Pin($user);

        $user->{config('otp.pin_column_name')} = Hash::make('1234');
        $user->save();

        $this->assertTrue($pin->check('1234'));
        $this->assertFalse($pin->check('1235'));
    }

    /** @test */
    public function throws_exception_checking_pin_if_stored_pin_is_null()
    {
        $user = factory(User::class)->create();

        $pin = new Pin($user);

        $this->expectException(NullStoredPinException::class);

        $pin->check('1234');
    }
}
