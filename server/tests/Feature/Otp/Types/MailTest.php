<?php

namespace Tests\Feature\Otp\Types;

use App\Otp\Mail\Otp;
use App\Otp\Types\Mail;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function queues_mail_with_generated_otp()
    {
        \Illuminate\Support\Facades\Mail::fake();

        $otp = new \App\Otp\Otp();
        $otpReflection = new \ReflectionClass($otp);
        $otpTypeColumnNameReflection = $otpReflection->getProperty('otpTypeColumnName');
        $otpTypeColumnNameReflection->setAccessible(true);
        $otpTypeColumnName = $otpTypeColumnNameReflection->getValue($otp);

        $user = factory(User::class)->create([
            $otpTypeColumnName => 'mail'
        ]);

        $mail = new Mail($user);
        $mail->send();

        $otpTypeReflection = new \ReflectionClass($mail);
        $generatedOtpReflection = $otpTypeReflection->getProperty('generatedOtp');
        $generatedOtpReflection->setAccessible(true);
        $generatedOtp = $generatedOtpReflection->getValue($mail);

        $emailColumnNameReflection = $otpTypeReflection->getProperty('emailColumnName');
        $emailColumnNameReflection->setAccessible(true);
        $emailColumnName = $emailColumnNameReflection->getValue($mail);

        \Illuminate\Support\Facades\Mail::assertQueued(
            Otp::class,
            function ($mail) use ($user, $emailColumnName, $generatedOtp) {
                return $mail->hasTo($user->{$emailColumnName}) &&
                    $mail->generatedOtp == $generatedOtp;
            }
        );
    }
}
