<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for( $i = 1; $i <= 10; $i++) {
            DB::table('company_user')->insert([
                'user_id' => rand(1, 10), // Assuming you have 10 users
                'company_id' => rand(1, 10), // Assuming you have 10 companies
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
