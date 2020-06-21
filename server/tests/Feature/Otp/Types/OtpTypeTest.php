<?php

namespace Tests\Feature\Otp\Types;

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

    /** @test */
    public function gets_otp()
    {
        $otp = new Otp();
        $otpReflection = new \ReflectionClass($otp);
        $otpTypeColumnNameReflection = $otpReflection->getProperty('otpTypeColumnName');
        $otpTypeColumnNameReflection->setAccessible(true);
        $otpTypeColumnName = $otpTypeColumnNameReflection->getValue($otp);
        
        $user = factory(User::class)->create([
            $otpTypeColumnName => 'mail'
        ]);

        $otpType = Otp::type($user);

        $otpTypeReflection = new \ReflectionClass($otpType);

        $otpColumnNameReflection = $otpTypeReflection->getProperty('otpColumnName');
        $otpColumnNameReflection->setAccessible(true);
        $otpColumnName = $otpColumnNameReflection->getValue($otpType);

        $hashedOtp = Hash::make('1234');

        $user->{$otpColumnName} = $hashedOtp;
        $user->save();

        $getOtpReflection = $otpTypeReflection->getMethod('getOtp');
        $getOtpReflection->setAccessible(true);

        $this->assertEquals(
            $hashedOtp,
            $getOtpReflection->invoke($otpType)
        );
    }

    /** @test */
    public function checks_otp()
    {
        $otp = new Otp();
        $otpReflection = new \ReflectionClass($otp);
        $otpTypeColumnNameReflection = $otpReflection->getProperty('otpTypeColumnName');
        $otpTypeColumnNameReflection->setAccessible(true);
        $otpTypeColumnName = $otpTypeColumnNameReflection->getValue($otp);

        $user = factory(User::class)->create([
            $otpTypeColumnName => 'mail'
        ]);

        $otpType = Otp::type($user);

        $otpTypeReflection = new \ReflectionClass($otpType);

        $otpColumnNameReflection = $otpTypeReflection->getProperty('otpColumnName');
        $otpColumnNameReflection->setAccessible(true);
        $otpColumnName = $otpColumnNameReflection->getValue($otpType);

        $user->{$otpColumnName} = Hash::make('1234');
        $user->save();

        $this->assertTrue($otpType->check('1234'));
        $this->assertFalse($otpType->check('1235'));
    }

    /** @test */
    public function throws_exception_checking_otp_if_stored_otp_is_null()
    {
        $otp = new Otp();
        $otpReflection = new \ReflectionClass($otp);
        $otpTypeColumnNameReflection = $otpReflection->getProperty('otpTypeColumnName');
        $otpTypeColumnNameReflection->setAccessible(true);
        $otpTypeColumnName = $otpTypeColumnNameReflection->getValue($otp);

        $user = factory(User::class)->create([
            $otpTypeColumnName => 'mail'
        ]);

        $otpType = Otp::type($user);

        $this->expectException(NullStoredOtpException::class);

        $otpType->check('1234');
    }

    /** @test */
    public function generates_otp()
    {
        $otp = new Otp();
        $otpReflection = new \ReflectionClass($otp);
        $otpTypeColumnNameReflection = $otpReflection->getProperty('otpTypeColumnName');
        $otpTypeColumnNameReflection->setAccessible(true);
        $otpTypeColumnName = $otpTypeColumnNameReflection->getValue($otp);

        $user = new User();
        $user->{$otpTypeColumnName} = 'mail';

        $otpType = Otp::type($user);

        $otpTypeReflection = new \ReflectionClass($otpType);
        $generatedOtpLengthReflection = $otpTypeReflection->getProperty('generatedOtpLength');
        $generatedOtpLengthReflection->setAccessible(true);
        $generatedOtpLengthReflection->setValue($otpType, 8);

        $generateReflection = $otpTypeReflection->getMethod('generate');
        $generateReflection->setAccessible(true);
        $generateReflection->invoke($otpType);

        $generatedOtpReflection = $otpTypeReflection->getProperty('generatedOtp');
        $generatedOtpReflection->setAccessible(true);

        $this->assertEquals(8, strlen($generatedOtpReflection->getValue($otpType)));
    }

    /** @test */
    public function stores_otp()
    {
        $otp = new Otp();
        $otpReflection = new \ReflectionClass($otp);
        $otpTypeColumnNameReflection = $otpReflection->getProperty('otpTypeColumnName');
        $otpTypeColumnNameReflection->setAccessible(true);
        $otpTypeColumnName = $otpTypeColumnNameReflection->getValue($otp);

        $user = factory(User::class)->create([
            $otpTypeColumnName => 'mail'
        ]);

        $otpType = Otp::type($user);

        $otpTypeReflection = new \ReflectionClass($otpType);

        $generateReflection = $otpTypeReflection->getMethod('generate');
        $generateReflection->setAccessible(true);
        $generateReflection->invoke($otpType);

        $storeReflection = $otpTypeReflection->getMethod('store');
        $storeReflection->setAccessible(true);
        $storeReflection->invoke($otpType);

        $generatedOtpReflection = $otpTypeReflection->getProperty('generatedOtp');
        $generatedOtpReflection->setAccessible(true);

        $otpColumnNameReflection = $otpTypeReflection->getProperty('otpColumnName');
        $otpColumnNameReflection->setAccessible(true);


        $this->assertTrue(
            Hash::check(
                $generatedOtpReflection->getValue($otpType),
                $user->{$otpColumnNameReflection->getValue($otpType)}
            )
        );
    }

    /** @test */
    public function throws_exception_storing_otp_if_generated_otp_is_empty()
    {
        $otp = new Otp();
        $otpReflection = new \ReflectionClass($otp);
        $otpTypeColumnNameReflection = $otpReflection->getProperty('otpTypeColumnName');
        $otpTypeColumnNameReflection->setAccessible(true);
        $otpTypeColumnName = $otpTypeColumnNameReflection->getValue($otp);

        $user = factory(User::class)->create([
            $otpTypeColumnName => 'mail'
        ]);

        $otpType = Otp::type($user);

        $otpTypeReflection = new \ReflectionClass($otpType);

        $this->expectException(EmptyGeneratedOtpException::class);

        $storeReflection = $otpTypeReflection->getMethod('store');
        $storeReflection->setAccessible(true);
        $storeReflection->invoke($otpType);
    }
}
