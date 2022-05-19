<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('utilizador')->insert([
            'nome' => 'admin',
            'login' => 'admin',
            'tipo' => 3,
            'password' => Hash::make('teste123'),
        ]);
    }
}
