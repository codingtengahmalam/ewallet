<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Generate random user
        User::factory()
            ->count(5)
            ->create();

        for ($i = 0; $i < 2; $i++){
            User::create([
                'name' => 'User '.$i,
                'username' => 'User_'.$i,
                'email' => 'user'.$i.'@example.com',
                'password' => Hash::make('example'),
            ]);
        }
    }
}
