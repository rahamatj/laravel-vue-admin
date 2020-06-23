<?php

namespace Tests\Feature\Api\Auth;

use App\Otp\Mail\Otp;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CheckpointTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:install');
    }

    /** @test */
    public function shows_error_if_token_does_not_have_proper_scope()
    {
        $user = factory(User::class)->create([
            'pin' => Hash::make('1234')
        ]);

        Passport::actingAs($user);

        $response = $this->json('post', '/api/checkpoint', [
            'otp' =>  '1234'
        ]);

        $response->assertForbidden();
        $response->assertJsonFragment([
            'message' => 'Invalid scope(s) provided.'
        ]);
    }

    /** @test */
    public function validates_otp()
    {
        $user = factory(User::class)->create([
            'pin' => Hash::make('1234')
        ]);

        Passport::actingAs($user, ['verify-otp-at-login']);

        $response = $this->json('post', '/api/checkpoint', [
            'otp' =>  ''
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' =>  [
                'otp' => ['The otp field is required.'],
            ]
        ]);
    }

    /** @test */
    public function checks_otp_and_returns_a_token_if_otp_matches()
    {
        $user = factory(User::class)->create([
            'pin' => Hash::make('1234')
        ]);

        Passport::actingAs($user, ['verify-otp-at-login']);

        $response = $this->json('post', '/api/checkpoint', [
           'otp' =>  '1234'
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'message',
            'token'
        ]);
    }

    /** @test */
    public function checks_otp_and_does_not_return_a_token_if_otp_does_not_match()
    {
        $user = factory(User::class)->create([
            'pin' => Hash::make('1234')
        ]);

        Passport::actingAs($user, ['verify-otp-at-login']);

        $response = $this->json('post', '/api/checkpoint', [
            'otp' =>  '1235'
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Your OTP didn\'t match.'
        ]);
    }

    /** @test */
    public function resends_otp()
    {
        Mail::fake();

        $user = factory(User::class)->create([
            'otp_type' => 'mail'
        ]);

        Passport::actingAs($user, ['verify-otp-at-login']);

        $response = $this->json('get', '/api/checkpoint/resend');

        $response->assertOk();
        $response->assertJson([
           'message' => 'OTP was resent.'
        ]);
        Mail::assertQueued(
            Otp::class,
            function ($mail) use ($user) {
                return $mail->hasTo($user->email);
            }
        );
    }

    /** @test */
    public function enables_google2fa()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, ['activate-google2fa']);

        $response = $this->json('get', '/api/checkpoint/google2fa/activate');

        $response->assertOk();
        $response->assertJsonStructure([
            'g2faUrl',
            'token'
        ]);
    }
}
