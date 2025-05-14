<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin PLN',
            'email' => 'admin@pln.com',
            'password' => bcrypt('password123')
        ]);

        User::create([
            'name' => 'Admin Utama',
            'email' => 'dwitian2231053@itpln.ac.id',
            'password' => bcrypt('passutama123')
        ]);
    }
}