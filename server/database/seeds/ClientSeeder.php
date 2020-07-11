<?php

use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = factory(\App\User::class)->create();

        factorty(\App\Client::class, 100)->create([
           'user_id' => $user->id
        ]);
    }
}
