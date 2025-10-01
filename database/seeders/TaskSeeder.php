<?php

namespace Database\Seeders;

use App\Models\Keyword;
use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $task = Task::create([
            'title' => 'Implement Login Feature',
            'is_done' => false,
        ]);
        $keywords = Keyword::whereIn('name', ['laravel', 'php', 'backend'])->get();
        $task->keywords()->attach($keywords->pluck('id')->toArray());
        $task2 = Task::create([
            'title' => 'Build RESTful API',
            'is_done' => false,
        ]);
        $keywords2 = Keyword::whereIn('name', ['laravel', 'api'])->get();
        $task2->keywords()->attach($keywords2->pluck('id')->toArray());
    }
}
