<?php

namespace Tests\Feature\Otp\Types;

use App\Sms\Facades\Sms;
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

        $user = factory(User::class)->create([
            config('otp.otp_type_column_name') => 'sms',
        ]);

        $sms = new \App\Otp\Types\Sms($user);

        $user->{config('otp.mobile_number_column_name')} = 'test';
        $user->save();

        $sms->send();

        $otpTypeReflection = new \ReflectionClass($sms);

        $generatedOtpReflection = $otpTypeReflection->getProperty('generatedOtp');
        $generatedOtpReflection->setAccessible(true);
        $generatedOtp = $generatedOtpReflection->getValue($sms);

        Sms::assertQueued(function ($sms) use ($generatedOtp) {
           return $sms->hasTo('test') &&
                $sms->hasMessage('Your OTP is ' . $generatedOtp . ' - ' . config('app.name'));
        });
    }
}
