<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'email' => env('ADMIN_USER'),
            'senha' => Hash::make(env('ADMIN_PASSWORD'),),
        ]);
        $admin->roles()->attach(Role::where('nome', 'ADMIN_ROLE')->first());
    }
}
