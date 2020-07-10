<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\User::class)->create([
            'email' => 'admin@email.com'
        ]);

        factory(\App\User::class)->create([
            'email' => 'postman@email.com'
        ]);

        factory(\App\User::class, 100)->create();

        Artisan::call('passport:install');
    }
}
