<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;

class CreateUserSeeder extends Seeder
{
    public function run()
    {
        $user = User::create([
            'name' => 'user',
            'email' => 'user@gmail.com',
            'phone' => '01732379393',
            'password' => bcrypt('123456')
        ]);
    }
}
