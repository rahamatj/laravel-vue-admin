<?php

namespace Tests\Feature\Api\Auth;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_reset_password()
    {
        $user = factory(User::class)->create();

        $response = $this->json('post', '/api/password/reset', [
            'email' => $user->email,
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'token' => Password::broker()->createToken($user)
        ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Your password has been reset!'
        ]);
    }

    /**
     * @test
     */
    public function user_can_not_reset_password_without_required_credentials()
    {
        $response = $this->json('post', '/api/password/reset', [
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
            'token' => ''
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'token' => ['The token field is required.'],
                'email' => ['The email field is required.'],
                'password' => ['The password field is required.']
            ]
        ]);
    }

    /**
     * @test
     */
    public function user_can_not_reset_password_without_valid_email()
    {
        $user = factory(User::class)->create();

        $response = $this->json('post', '/api/password/reset', [
            'email' => 'admin@email.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'token' => Password::broker()->createToken($user)
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'email' => ['We can\'t find a user with that email address.']
            ]
        ]);
    }

    /**
     * @test
     */
    public function user_can_not_reset_password_without_password_not_being_minimum_length_of_8_characters()
    {
        $user = factory(User::class)->create();

        $response = $this->json('post', '/api/password/reset', [
            'email' => $user->email,
            'password' => '1234',
            'password_confirmation' => '1234',
            'token' => Password::broker()->createToken($user)
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'password' => ['The password must be at least 8 characters.']
            ]
        ]);
    }

    /**
     * @test
     */
    public function user_can_not_reset_password_without_matching_confirmation_password()
    {
        $user = factory(User::class)->create();

        $response = $this->json('post', '/api/password/reset', [
            'email' => $user->email,
            'password' => '12345678',
            'password_confirmation' => '123456789',
            'token' => Password::broker()->createToken($user)
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'password' => ['The password confirmation does not match.']
            ]
        ]);
    }
}
