<?php

namespace Tests\Feature\Datatable;

use App\Datatable\Datatable;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DatatableTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        factory(User::class)->create(['name' => 'A', 'email' => 'a@email.com']);
        factory(User::class)->create(['name' => 'B', 'email' => 'b@email.com']);
        factory(User::class)->create(['name' => 'C', 'email' => 'c@email.com']);
        factory(User::class)->create(['name' => 'D', 'email' => 'd@email.com']);
        factory(User::class)->create(['name' => 'E', 'email' => 'bb@email.com']);
    }

    /** @test */
    public function sorts_data()
    {
        request()['sort_by'] = 'name';

        $datatable = new Datatable(User::query());

        $data = User::orderBy('name')->paginate(25);

        $this->assertEquals($data, $datatable->get());
    }

    /** @test */
    public function sorts_data_in_descending_order()
    {
        request()['sort_by'] = 'name';
        request()['sort_desc'] = 1;

        $datatable = new Datatable(User::query());

        $data = User::orderByDesc('name')->paginate(25);

        $this->assertEquals($data, $datatable->get());
    }

    /** @test */
    public function filters_data()
    {
        request()['filter'] = 'B';

        $datatable = new Datatable(User::query());
        $datatable->filterBy(['name', 'email']);

        $data = User::where('name', 'like', '%B%')
            ->orWhere('email', 'like', '%B%')
            ->paginate(25);

        $this->assertEquals($data, $datatable->get());
    }

    /** @test */
    public function gets_latest_data()
    {
        $datatable = new Datatable(User::query());
        $datatable->latest();

        $data = User::latest()
            ->paginate(25);

        $this->assertEquals($data, $datatable->get());
    }

    /** @test */
    public function gets_ordered_data()
    {
        $datatable = new Datatable(User::query());
        $datatable->orderBy('id', 'desc');

        $data = User::orderBy('id', 'desc')
            ->paginate(25);

        $this->assertEquals($data, $datatable->get());
    }
}
