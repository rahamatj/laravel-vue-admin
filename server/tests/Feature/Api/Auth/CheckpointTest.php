<?php

namespace Tests\Feature\Api\Auth;

use App\Client;
use Google2FA;
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
    public function shows_error_verifying_otp_if_no_otp_is_given()
    {
        $user = factory(User::class)->create([
            'is_otp_verification_enabled_at_login' => true,
            'pin' => Hash::make('1234')
        ]);

        $client = factory(Client::class)->create([
            'user_id' => $user->id
        ]);

        Passport::actingAs($user);

        $response = $this->json('post', '/api/checkpoint', [], [
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' =>  [
                'otp' => ['The otp field is required.']
            ]
        ]);
    }

    /** @test */
    public function shows_error_verifying_otp_if_no_fingerprint_header_was_attached()
    {
        $user = factory(User::class)->create([
            'is_otp_verification_enabled_at_login' => true,
            'pin' => Hash::make('1234')
        ]);

        Passport::actingAs($user);

        $response = $this->json('post', '/api/checkpoint', [
            'otp' => '1234'
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Fingerprint header is required.',
        ]);
    }

    /** @test */
    public function shows_error_verifying_otp_if_fingerprint_does_not_match()
    {
        $user = factory(User::class)->create([
            'is_otp_verification_enabled_at_login' => true,
            'pin' => Hash::make('1234')
        ]);

        Passport::actingAs($user);

        $response = $this->json('post', '/api/checkpoint', [
            'otp' => '1234'
        ], [
            'Fingerprint' => 'test'
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Client not found.',
        ]);
    }

    /** @test */
    public function shows_error_verifying_otp_if_otp_verification_at_login_is_disabled()
    {
        $user = factory(User::class)->create([
            'pin' => Hash::make('1234')
        ]);

        Passport::actingAs($user);

        $response = $this->json('post', '/api/checkpoint', [
            'otp' => '1234'
        ], [
            'Fingerprint' => 'test'
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Forbidden.',
        ]);
    }

    /** @test */
    public function shows_error_verifying_otp_if_otp_has_already_been_verified()
    {
        $user = factory(User::class)->create([
            'is_otp_verification_enabled_at_login' => true,
            'pin' => Hash::make('1234')
        ]);

        $client = factory(Client::class)->create([
            'user_id' => $user->id,
            'is_otp_verified_at_login' => true
        ]);

        Passport::actingAs($user);

        $response = $this->json('post', '/api/checkpoint', [
            'otp' => '1234'
        ], [
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Forbidden.',
        ]);
    }

    /** @test */
    public function shows_error_verifying_otp_if_otp_type_is_google2fa_and_google2fa_has_not_been_activated()
    {
        $user = factory(User::class)->create([
            'is_otp_verification_enabled_at_login' => true,
            'otp_type' => 'google2fa'
        ]);

        $client = factory(Client::class)->create([
            'user_id' => $user->id
        ]);

        Passport::actingAs($user);

        $response = $this->json('post', '/api/checkpoint', [
            'otp' => '1234'
        ], [
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Forbidden.',
        ]);
    }

    /** @test */
    public function checkpoint_verifies_otp()
    {
        $user = factory(User::class)->create([
            'is_otp_verification_enabled_at_login' => true,
            'pin' => Hash::make('1234')
        ]);

        $client = factory(Client::class)->create([
            'user_id' => $user->id
        ]);

        Passport::actingAs($user);

        $response = $this->json('post', '/api/checkpoint', [
            'otp' =>  '1234'
        ], [
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'OTP verified successfully!'
        ]);

        $updatedClient = Client::find($client->id);
        $this->assertEquals(1, $updatedClient->is_otp_verified_at_login);
    }

    /** @test */
    public function shows_error_if_otp_does_not_match()
    {
        $user = factory(User::class)->create([
            'is_otp_verification_enabled_at_login' => true,
            'pin' => Hash::make('1234')
        ]);

        $client = factory(Client::class)->create([
            'user_id' => $user->id
        ]);

        Passport::actingAs($user);

        $response = $this->json('post', '/api/checkpoint', [
            'otp' =>  '1235'
        ], [
            'Fingerprint' => $client->fingerprint
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
            'is_otp_verification_enabled_at_login' => true,
            'otp_type' => 'mail'
        ]);

        $client = factory(Client::class)->create([
            'user_id' => $user->id
        ]);

        Passport::actingAs($user);

        $response = $this->json('get', '/api/checkpoint/resend', [], [
            'Fingerprint' => $client->fingerprint
        ]);

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
    public function activates_google2fa()
    {
        $user = factory(User::class)->create([
            'is_otp_verification_enabled_at_login' => true,
            'otp_type' => 'google2fa'
        ]);

        $client = factory(Client::class)->create([
           'user_id' => $user->id
        ]);

        Passport::actingAs($user);

        $response = $this->json('get', '/api/checkpoint/google2fa/activate', [], [
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'g2faUrl'
        ]);

        $updatedUser = User::find($user->id);
        $this->assertEquals(1, $updatedUser->is_google2fa_activated);
    }

    /** @test */
    public function shows_error_activating_google2fa_if_it_has_already_been_activated()
    {
        $user = factory(User::class)->create([
            'is_otp_verification_enabled_at_login' => true,
            'otp_type' => 'google2fa',
            'is_google2fa_activated' => true
        ]);

        $client = factory(Client::class)->create([
            'user_id' => $user->id
        ]);

        Passport::actingAs($user);

        $response = $this->json('get', '/api/checkpoint/google2fa/activate', [], [
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertForbidden();
        $response->assertJson([
            'message' => 'Forbidden.'
        ]);
    }

    /** @test */
    public function shows_error_activating_google2fa_if_otp_type_is_not_google2fa()
    {
        $user = factory(User::class)->create([
            'is_otp_verification_enabled_at_login' => true,
            'otp_type' => 'pin',
        ]);

        $client = factory(Client::class)->create([
            'user_id' => $user->id
        ]);

        Passport::actingAs($user);

        $response = $this->json('get', '/api/checkpoint/google2fa/activate', [], [
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertForbidden();
        $response->assertJson([
            'message' => 'Forbidden.'
        ]);
    }
}
