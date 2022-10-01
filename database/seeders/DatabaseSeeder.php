<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\FileSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        DB::table('users')->insert([
            ['name' => 'admin',
            'phone' => '09193337476',
            'email' => 'admin@admin.com',
            'password' => Hash::make("123456"),
            'role' => 'admin',],
            ['name' => 'hossein',
            'phone' => '09193337476',
            'email' => 'hossein.momeni1991@gmail.com',
            'password' => Hash::make("123456"),
            'role' => 'buyer',]
        ]);

        $this->call([
            FileSeeder::class,
        ]);
    }
}
