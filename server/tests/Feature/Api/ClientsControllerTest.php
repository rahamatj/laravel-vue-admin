<?php

namespace Tests\Feature\Api;

use App\Client;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ClientsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = factory(User::class)->create();
        Passport::actingAs($user);
    }

    /** @test */
    public function returns_all_clients_paginated()
    {
        factory(Client::class, 10)->create([
            'user_id' => 1
        ]);

        $response = $this->json('get', '/api/clients', [], [
            'Fingerprint' => 'test'
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'meta',
            'links'
        ]);

        $this->assertCount(10, json_decode($response->getContent())->data);
    }

    /** @test */
    public function changes_enabled_status()
    {
        $client = factory(Client::class)->create([
            'user_id' => 1
        ]);

        $response = $this->json('patch', '/api/clients/' . $client->id . '/enabled', [], [
            'Fingerprint' => 'test'
        ]);

        $response->assertOk();
        $response->assertJson([
           'message' => 'Enabled status changed successfully!'
        ]);
    }

    /** @test */
    public function destroys_client()
    {
        $client = factory(Client::class)->create([
            'user_id' => 1
        ]);

        $response = $this->json('delete', '/api/clients/' . $client->id, [], [
            'Fingerprint' => 'test'
        ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Client deleted successfully!'
        ]);
        $this->assertCount(0, Client::all());
    }
}
