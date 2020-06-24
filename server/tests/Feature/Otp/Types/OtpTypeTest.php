<?php

namespace Tests\Feature\Otp\Types;

use App\Client;
use App\Otp\Exceptions\ClientNotFoundException;
use App\Otp\Exceptions\EmptyGeneratedOtpException;
use App\Otp\Exceptions\NullStoredOtpException;
use App\Otp\Otp;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OtpTypeTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $otpType;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = factory(User::class)->create([
            config('otp.otp_type_column_name') => 'mail'
        ]);
        $this->otpType = Otp::type($this->user);
    }

    /** @test */
    public function gets_otp()
    {
        $hashedOtp = Hash::make('1234');

        $this->user->{config('otp.otp_column_name')} = $hashedOtp;
        $this->user->save();

        $otpTypeReflection = new \ReflectionClass($this->otpType);

        $getOtpReflection = $otpTypeReflection->getMethod('getOtp');
        $getOtpReflection->setAccessible(true);

        $this->assertEquals(
            $hashedOtp,
            $getOtpReflection->invoke($this->otpType)
        );
    }

    /** @test */
    public function checks_otp()
    {
        $this->user->{config('otp.otp_column_name')} = Hash::make('1234');
        $this->user->save();

        $this->assertTrue($this->otpType->check('1234'));
        $this->assertFalse($this->otpType->check('1235'));
    }

    /** @test */
    public function throws_exception_checking_otp_if_stored_otp_is_null()
    {
        $this->expectException(NullStoredOtpException::class);

        $this->otpType->check('1234');
    }

    /** @test */
    public function generates_otp()
    {
        $otpTypeReflection = new \ReflectionClass($this->otpType);

        $generateReflection = $otpTypeReflection->getMethod('generate');
        $generateReflection->setAccessible(true);
        $generateReflection->invoke($this->otpType);

        $generatedOtpReflection = $otpTypeReflection->getProperty('generatedOtp');
        $generatedOtpReflection->setAccessible(true);

        $this->assertEquals(
            config('otp.generated_otp_length'),
            strlen($generatedOtpReflection->getValue($this->otpType))
        );
    }

    /** @test */
    public function stores_otp()
    {
        $otpTypeReflection = new \ReflectionClass($this->otpType);

        $generateReflection = $otpTypeReflection->getMethod('generate');
        $generateReflection->setAccessible(true);
        $generateReflection->invoke($this->otpType);

        $storeReflection = $otpTypeReflection->getMethod('store');
        $storeReflection->setAccessible(true);
        $storeReflection->invoke($this->otpType);

        $generatedOtpReflection = $otpTypeReflection->getProperty('generatedOtp');
        $generatedOtpReflection->setAccessible(true);

        $this->assertTrue(
            Hash::check(
                $generatedOtpReflection->getValue($this->otpType),
                $this->user->{config('otp.otp_column_name')}
            )
        );
    }

    /** @test */
    public function throws_exception_storing_otp_if_generated_otp_is_empty()
    {
        $otpTypeReflection = new \ReflectionClass($this->otpType);

        $this->expectException(EmptyGeneratedOtpException::class);

        $storeReflection = $otpTypeReflection->getMethod('store');
        $storeReflection->setAccessible(true);
        $storeReflection->invoke($this->otpType);
    }

    /** @test */
    public function verifies_otp()
    {
        $this->user->{config('otp.otp_column_name')} = Hash::make('1234');
        $this->user->save();

        $client = factory(Client::class)->create([
            'user_id' => $this->user->id
        ]);

        $this->otpType->verify('1234', $client->fingerprint);

        $updatedClient = Client::where([
            ['user_id', $this->user->id],
            ['fingerprint', $client->fingerprint]
        ])->first();

        $this->assertEquals(1, $updatedClient->is_otp_verified_at_login);
    }

    /** @test */
    public function returns_true_if_otp_has_been_verified()
    {
        $client = factory(Client::class)->create([
            'user_id' => $this->user->id,
            'is_otp_verified_at_login' => true
        ]);

        $this->assertEquals(
            1,
            $this->otpType->hasBeenVerifiedAtLogin($client->fingerprint)
        );
    }

    /** @test */
    public function throws_exception_if_client_is_not_found()
    {
        $this->expectException(ClientNotFoundException::class);

        $this->otpType->hasBeenVerifiedAtLogin('test');
    }
}
