<?php

namespace Tests\Feature\Otp\Types;

use App\Facades\Sms;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SmsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function queues_sms_with_generated_otp()
    {
        Sms::fake();

        $otp = new \App\Otp\Otp();
        $otpReflection = new \ReflectionClass($otp);
        $otpTypeColumnNameReflection = $otpReflection->getProperty('otpTypeColumnName');
        $otpTypeColumnNameReflection->setAccessible(true);
        $otpTypeColumnName = $otpTypeColumnNameReflection->getValue($otp);

        $user = factory(User::class)->create([
            $otpTypeColumnName => 'sms',
        ]);

        $sms = new \App\Otp\Types\Sms($user);

        $otpTypeReflection = new \ReflectionClass($sms);
        $mobileNumberColumnNameReflection = $otpTypeReflection->getProperty('mobileNumberColumnName');
        $mobileNumberColumnNameReflection->setAccessible(true);
        $mobileNumberColumnName = $mobileNumberColumnNameReflection->getValue($sms);

        $user->{$mobileNumberColumnName} = 'test';
        $user->save();

        $sms->send();

        $generatedOtpReflection = $otpTypeReflection->getProperty('generatedOtp');
        $generatedOtpReflection->setAccessible(true);
        $generatedOtp = $generatedOtpReflection->getValue($sms);

        Sms::assertQueued(function ($sms) use ($generatedOtp) {
           return $sms->hasTo('test') &&
                $sms->hasMessage('Your OTP is ' . $generatedOtp . ' - ' . config('app.name'));
        });
    }
}
