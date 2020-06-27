<?php

namespace Tests\Feature\Api\Auth;

use App\Client;
use App\Exceptions\FingerprintHeaderRequiredException;
use App\Otp\Mail\Otp;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Passport\Passport;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected $requestData = [
        'email' => 'admin@email.com',
        'password' => '12345678'
    ];

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:install');
    }

    /** @test */
    public function throws_exception_if_no_fingerprint_header_is_attached()
    {
        $this->withoutExceptionHandling();
        $this->expectException(FingerprintHeaderRequiredException::class);

        $user = factory(User::class)->create();

        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => $user->email
        ]));
    }

    /** @test */
    public function user_gets_an_access_token_and_client_gets_added_to_database()
    {
        $user = factory(User::class)->create();

        $client = factory(Client::class)->make();

        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => $user->email
        ]), [
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'message', 'token', 'user'
        ]);
        $this->assertCount(1, Client::all());
    }

    /** @test */
    public function sends_otp_if_otp_verification_at_login_is_enabled()
    {
        Mail::fake();

        $user = factory(User::class)->create([
            'is_otp_verification_enabled_at_login' => true,
            'otp_type' => 'mail'
        ]);

        $client = factory(Client::class)->create([
            'user_id' => $user->id
        ]);

        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => $user->email
        ]), [
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertOk();
        Mail::assertQueued(
            Otp::class,
            function ($mail) use ($user) {
                return $mail->hasTo($user->email);
            }
        );
    }

    /** @test */
    public function user_can_access_dashboard_with_access_token()
    {
        $user = factory(User::class)->create();

        $client = factory(Client::class)->create([
            'user_id' => $user->id
        ]);

        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => $user->email
        ]), [
            'Fingerprint' => $client->fingerprint
        ]);

        $token = $response->getOriginalContent()['token'];

        $response = $this->json('get', '/api/dashboard', [], [
            'Authorization' => 'Bearer ' . $token,
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertOk();
    }

    /** @test */
    public function user_can_not_access_dashboard_with_access_token_if_otp_verification_at_login_is_enabled_and_otp_has_not_been_verified()
    {
        $user = factory(User::class)->create([
            'is_otp_verification_enabled_at_login' => true
        ]);

        $client = factory(Client::class)->create([
            'user_id' => $user->id
        ]);

        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => $user->email
        ]), [
            'Fingerprint' => $client->fingerprint
        ]);

        $token = $response->getOriginalContent()['token'];

        $response = $this->json('get', '/api/dashboard', [], [
            'Authorization' => 'Bearer ' . $token,
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function fields_are_required_to_get_access_token()
    {
        $user = factory(User::class)->create();

        $client = factory(Client::class)->create([
            'user_id' => $user->id
        ]);

        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => '',
            'password' => ''
        ]), [
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' =>  [
                'email' => ['The email field is required.'],
                'password' => ['The password field is required.']
            ]
        ]);
    }

    /** @test */
    public function user_does_not_get_access_token_without_correct_email()
    {
        $user = factory(User::class)->create();

        $client = factory(Client::class)->create([
            'user_id' => $user->id
        ]);

        $response = $this->json('post', '/api/login', $this->requestData(), [
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'email' => ['These credentials do not match our records.']
            ]
        ]);
    }

    /** @test */
    public function user_does_not_get_access_token_without_correct_password()
    {
        $user = factory(User::class)->create();

        $client = factory(Client::class)->create([
            'user_id' => $user->id
        ]);

        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => $user->email,
            'password' => '123456789'
        ]), [
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'email' => ['These credentials do not match our records.']
            ]
        ]);
    }

    /**
     * @test
     */
    public function user_does_not_get_access_token_when_client_lock_is_enabled_and_client_stored_is_equal_to_clients_allowed_and_none_of_them_matches()
    {
        $user = factory(User::class)->create([
            'is_client_lock_enabled' => 1,
            'clients_allowed' => 4
        ]);

        factory(Client::class, 4)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => $user->email
        ]), [
            'Fingerprint' => 'test'
        ]);

        $response->assertUnauthorized();
        $response->assertJson([
            'message' => 'Login from this client is not allowed.'
        ]);
        $this->assertCount(4, Client::all());
    }

    /**
     * @test
     */
    public function user_gets_access_token_when_client_lock_is_enabled_and_clients_stored_is_equal_to_clients_allowed_and_one_of_them_matches()
    {
        $user = factory(User::class)->create([
            'is_client_lock_enabled' => 1,
            'clients_allowed' => 4
        ]);

        $client = factory(Client::class)->create([
            'user_id' => $user->id,
        ]);

        factory(Client::class, 3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => $user->email
        ]), [
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'message', 'token', 'user'
        ]);
        $this->assertCount(4, Client::all());
    }

    /**
     * @test
     */
    public function user_does_not_get_access_token_when_client_lock_is_enabled_and_client_fingerprint_matches_but_the_client_is_disabled()
    {
        $user = factory(User::class)->create([
            'is_client_lock_enabled' => 1,
            'clients_allowed' => 4
        ]);

        $client = factory(Client::class)->create([
            'user_id' => $user->id,
            'is_enabled' => 0
        ]);

        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => $user->email
        ]), [
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertUnauthorized();
        $response->assertJson([
            'message' => 'Login from this client is not allowed.'
        ]);
        $this->assertCount(1, Client::all());
    }

    /**
     * @test
     */
    public function user_gets_access_token_when_client_lock_is_enabled_and_client_fingerprint_matches()
    {
        $user = factory(User::class)->create([
            'is_client_lock_enabled' => 1,
            'clients_allowed' => 4
        ]);

        $client = factory(Client::class)->create([
            'user_id' => $user->id
        ]);

        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => $user->email
        ]), [
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'message', 'token', 'user'
        ]);
        $this->assertCount(1, Client::all());
    }

    /**
     * @test
     */
    public function user_gets_access_token_when_client_lock_is_enabled_and_there_are_less_clients_stored_than_clients_allowed()
    {
        $user = factory(User::class)->create([
            'is_client_lock_enabled' => 1,
            'clients_allowed' => 4
        ]);

        factory(Client::class)->create([
            'user_id' => $user->id
        ]);

        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => $user->email
        ]), [
            'Fingerprint' => 'test'
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'message', 'token', 'user'
        ]);
        $this->assertCount(2, Client::all());
    }

    /**
     * @test
     */
    public function user_does_not_get_access_token_when_ip_lock_is_enabled_and_ip_stored_does_not_match()
    {
        $user = factory(User::class)->create([
            'is_ip_lock_enabled' => 1
        ]);

        $client = factory(Client::class)->create([
            'user_id' => $user->id
        ]);

        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => $user->email
        ]), [
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertUnauthorized();
        $response->assertJson([
            'message' => 'Login from this IP: 127.0.0.1 is not allowed.'
        ]);
    }

    /**
     * @test
     */
    public function user_gets_access_token_when_ip_lock_is_enabled_and_no_client_is_stored()
    {
        $user = factory(User::class)->create([
            'is_ip_lock_enabled' => 1
        ]);

        $client = factory(Client::class)->make();

        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => $user->email
        ]), [
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'message', 'token', 'user'
        ]);
        $this->assertCount(1, Client::all());
    }

    /**
     * @test
     */
    public function user_gets_access_token_when_ip_lock_is_enabled_and_ip_matches()
    {
        $user = factory(User::class)->create([
            'is_ip_lock_enabled' => 1
        ]);

        $client = factory(Client::class)->create([
            'user_id' => $user->id,
            'ip' => '127.0.0.1'
        ]);

        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => $user->email
        ]), [
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'message', 'token', 'user'
        ]);
        $this->assertCount(1, Client::all());
    }

    /** @test */
    public function authenticated_user_can_logout()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'is_otp_verification_enabled_at_login' => true
        ]);

        $client = factory(Client::class)->create([
           'user_id' => $user->id,
            'is_otp_verified_at_login' => true
        ]);

        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => $user->email
        ]), [
            'Fingerprint' => $client->fingerprint
        ]);

        $token = $response->getOriginalContent()['token'];

        $response = $this->json('post', '/api/logout', [], [
            'Authorization' => 'Bearer ' . $token,
            'Fingerprint' => $client->fingerprint
        ]);

        $response->assertNoContent();
        $this->assertCount(0, User::find($user->id)->tokens);

        $updatedClient = Client::where([
            ['user_id', $user->id],
            ['fingerprint', $client->fingerprint]
        ])->first();

        $this->assertEquals(0, $updatedClient->is_otp_verified_at_login);
    }

    /** @test */
    public function unauthenticated_user_can_not_logout()
    {
        $response = $this->json('post', '/api/logout');

        $response->assertUnauthorized();
    }
}
