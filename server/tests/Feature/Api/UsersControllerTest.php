<?php

namespace Tests\Feature\Api;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UsersControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = factory(User::class)->create();
        Passport::actingAs($user);
    }

    /** @test */
    public function returns_all_users_paginated()
    {
        factory(User::class, 10)->create();

        $response = $this->json('get', '/api/users', [], [
            'Fingerprint' => 'test'
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'meta',
            'links'
        ]);

        $this->assertCount(11, json_decode($response->getContent())->data);
    }

    /** @test */
    public function shows_user()
    {
        $user = factory(User::class)->create();

        $response = $this->json('get', '/api/users/' . $user->id, [], [
           'Fingerprint' => 'test'
        ]);

        $response->assertOk();
        $response->assertJson([
            'data' => $user->toArray()
        ]);
    }

    /** @test */
    public function stores_user()
    {
        $response = $this->json('post', '/api/users', [
            'name' => 'test',
            'email' => 'test@email.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'mobile_number' => '012345678',
            'is_otp_verification_enabled_at_login' => false,
            'is_client_lock_enabled' => false,
            'is_ip_lock_enabled' => false
        ], [
            'Fingerprint' => 'test'
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['message', 'data']);
        $response->assertJsonFragment([
           'message' => 'User created successfully!'
        ]);

        $this->assertCount(2, User::all());
    }

    /** @test */
    public function updates_user()
    {
        $user = factory(User::class)->create();

        $response = $this->json('patch', '/api/users/' . $user->id, [
            'name' => 'test',
            'email' => 'test@email.com',
            'password' => '12345678',
            'mobile_number' => '012345678'
        ], [
            'Fingerprint' => 'test'
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['message', 'data']);
        $response->assertJsonFragment([
            'message' => 'User updated successfully!'
        ]);
    }

    /** @test */
    public function destroys_user()
    {
        $user = factory(User::class)->create();

        $response = $this->json('delete', '/api/users/' . $user->id, [], [
            'Fingerprint' => 'test'
        ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'User deleted successfully!'
        ]);
        $this->assertCount(1, User::all());
    }

    /** @test */
    public function updates_password()
    {
        $user = factory(User::class)->create();

        $response = $this->json('patch', '/api/users/' . $user->id . '/password', [
            'password' => '12345678',
            'password_confirmation' => '12345678'
        ], [
            'Fingerprint' => 'test'
        ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Password updated successfully!'
        ]);
    }

    /** @test */
    public function updates_pin()
    {
        $user = factory(User::class)->create();

        $response = $this->json('patch', '/api/users/' . $user->id . '/pin', [
            'pin' => '12345678',
        ], [
            'Fingerprint' => 'test'
        ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'PIN updated successfully!'
        ]);
    }
}
