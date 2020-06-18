<?php

namespace Tests\Feature\Api\Auth;

use App\Client;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected $requestData = [
        'email' => 'admin@email.com',
        'password' => '12345678',
        'fingerprint' => 'test',
        'client' => 'test',
        'platform' => 'test',
    ];

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:install');
    }

    /** @test */
    public function user_gets_an_access_token_and_client_gets_added_to_database()
    {
        $user = factory(User::class)->create();

        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => $user->email
        ]));

        $response->assertOk();
        $response->assertJsonStructure([
            'message', 'token', 'user'
        ]);
        $this->assertCount(1, Client::all());
    }

    /** @test */
    public function user_can_access_dashboard_with_access_token()
    {
        $user = factory(User::class)->create();

        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => $user->email
        ]));

        $token = $response->getOriginalContent()['token'];

        $response = $this->json('get', '/api/dashboard', [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertOk();
        $response->assertJson($user->toArray());
    }

    /** @test */
    public function fields_are_required_to_get_access_token()
    {
        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => '',
            'password' => '',
            'fingerprint' => '',
            'client' => '',
            'platform' => '',

        ]));

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' =>  [
                'email' => ['The email field is required.'],
                'password' => ['The password field is required.'],
                'fingerprint' => ['The fingerprint field is required.'],
                'client' => ['The client field is required.'],
                'platform' => ['The platform field is required.']
            ]
        ]);
    }

    /** @test */
    public function user_does_not_get_access_token_without_correct_email()
    {
        factory(User::class)->create();

        $response = $this->json('post', '/api/login', $this->requestData());

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

        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => $user->email,
            'password' => '123456789'
        ]));

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
        ]));

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
            'email' => $user->email,
            'fingerprint' => $client->fingerprint
        ]));

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
            'email' => $user->email,
            'fingerprint' => $client->fingerprint
        ]));

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
            'email' => $user->email,
            'fingerprint' => $client->fingerprint
        ]));

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
        ]));

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

        factory(Client::class)->create([
            'user_id' => $user->id
        ]);

        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => $user->email
        ]));

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

        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => $user->email
        ]));

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
            'email' => $user->email,
            'fingerprint' => $client->fingerprint
        ]));

        $response->assertOk();
        $response->assertJsonStructure([
            'message', 'token', 'user'
        ]);
        $this->assertCount(1, Client::all());
    }

    /** @test */
    public function authenticated_user_can_logout()
    {
        $user = factory(User::class)->create();

        $response = $this->json('post', '/api/login', $this->requestData([
            'email' => $user->email
        ]));

        $token = $response->getOriginalContent()['token'];

        $response = $this->json('post', '/api/logout', [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertNoContent();
        $this->assertCount(0, User::find($user->id)->tokens);
    }

    /** @test */
    public function unauthenticated_user_can_not_logout()
    {
        $response = $this->json('post', '/api/logout');

        $response->assertUnauthorized();
    }
}
