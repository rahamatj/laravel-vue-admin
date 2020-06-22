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

        $otpConfig = require(__DIR__.'/../../../../app/Otp/config/otp.php');

        $user = factory(User::class)->create([
            $otpConfig['otp_type_column_name'] => 'mail'
        ]);

        $mail = new Mail($user);
        $mail->send();

        $otpTypeReflection = new \ReflectionClass($mail);
        $generatedOtpReflection = $otpTypeReflection->getProperty('generatedOtp');
        $generatedOtpReflection->setAccessible(true);
        $generatedOtp = $generatedOtpReflection->getValue($mail);

        $mailColumnName = $otpConfig['mail_column_name'];

        \Illuminate\Support\Facades\Mail::assertQueued(
            Otp::class,
            function ($mail) use ($user, $mailColumnName, $generatedOtp) {
                return $mail->hasTo($user->{$mailColumnName}) &&
                    $mail->generatedOtp == $generatedOtp;
            }
        );
    }
}
