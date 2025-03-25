<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use  Illuminate\Database\UniqueConstraintViolationException;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Giang',
            'email' => 'danggianghxpt2003@gmail.com',
            'password' => bcrypt('123456')
        ]);
    }
}
