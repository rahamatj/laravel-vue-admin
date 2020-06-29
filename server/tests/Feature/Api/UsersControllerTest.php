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
        $this->withoutExceptionHandling();

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
}
