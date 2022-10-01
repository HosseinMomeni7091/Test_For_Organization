<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('files')->insert([
            [
                "name" => "Hossein",
                "path" => "Resume.pdf",
                "size" => 463,
                "user_id" => 2,
                "created_at" => now(),
            ]
        ]);
    }
}
