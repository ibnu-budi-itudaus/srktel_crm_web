<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sources')->upsert([
            ['name' => 'Incoming Call'],
            ['name' => 'Chat'],
            ['name' => 'Store Visit'],
            ['name' => 'Visiting'],
        ], ['name']);
    }
}
