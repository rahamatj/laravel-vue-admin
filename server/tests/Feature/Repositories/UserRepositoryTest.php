<?php

namespace Tests\Feature\Repositories;

use App\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = factory(User::class)->create([
            'name' => 'Admin',
            'email' => 'admin@email.com'
        ]);

        Passport::actingAs($user);
    }

    /** @test */
    public function gets_datatable()
    {
        factory(User::class, 10)->create();
        factory(User::class)->create([
            'name' => 'Test',
            'email' => 'test@email.com'
        ]);

        $userRepository = new UserRepository();

        $filter = 'Test';

        request()['filter'] = $filter;

        $data = User::select([
            'id',
            'name',
            'email',
            'mobile_number',
            'is_otp_verification_enabled_at_login',
            'otp_type'
        ])->latest()
            ->where(function ($query) use ($filter) {
                $query->orWhere('name', 'like', '%' . $filter .'%')
                    ->orWhere('email', 'like', '%' . $filter .'%')
                    ->orWhere('mobile_number', 'like', '%' . $filter .'%')
                    ->orWhere('otp_type', 'like', '%' . $filter .'%');
            })
            ->where('id', '!=', Auth::id())
            ->paginate(25);

        $this->assertEquals($data, $userRepository->datatable());
    }
}
