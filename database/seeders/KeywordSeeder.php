<?php

namespace Database\Seeders;

use App\Models\Keyword;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KeywordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect(['laravel','php','backend','api','vue'])
            ->each(function ($name) {
                Keyword::create(['name' => $name]);
            });
    }
}
