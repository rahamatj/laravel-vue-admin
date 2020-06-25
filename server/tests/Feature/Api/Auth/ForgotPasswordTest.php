<?php

namespace Tests\Feature\Api\Auth;

use App\Client;
use App\Notifications\ResetPassword;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_receives_an_email_with_a_password_reset_link()
    {
        Notification::fake();

        $user = factory(User::class)->create();

        $client = factory(Client::class)->create([
            'user_id' => $user->id
        ]);

        $response = $this->json('post', '/api/password/email', [
            'email' => $user->email
        ], [
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'We have emailed your password reset link!'
        ]);

        $token = DB::table('password_resets')->where('email', $user->email)->first();

        $this->assertNotNull($token);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification, $channels) use ($token) {
            return Hash::check($notification->token, $token->token) === true;
        });
    }

    /**
     * @test
     */
    public function user_can_not_submit_password_reset_form_without_email()
    {
        $user = factory(User::class)->create();

        $client = factory(Client::class)->create([
            'user_id' => $user->id
        ]);

        $response = $this->json('post', '/api/password/email', [
            'email' => ''
        ], [
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'email' => ['The email field is required.']
            ]
        ]);
    }

    /**
     * @test
     */
    public function user_can_not_submit_password_reset_form_without_valid_email()
    {
        $user = factory(User::class)->create();

        $client = factory(Client::class)->create([
            'user_id' => $user->id
        ]);

        $response = $this->json('post', '/api/password/email', [
            'email' => 'admin@email.com'
        ], [
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'email' => ['We can\'t find a user with that email address.']
            ]
        ]);
    }
}
