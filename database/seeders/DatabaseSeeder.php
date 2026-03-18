<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()
            ->where('email', '!=', 'viveknair97k@gmail.com')
            ->update(['is_admin' => false]);

        User::updateOrCreate([
            'email' => 'viveknair97k@gmail.com',
        ], [
            'name' => 'Vivek Nair',
            'password' => '12345678',
            'is_admin' => true,
        ]);
    }
}
