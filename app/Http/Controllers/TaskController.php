<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Keyword;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Log::info('Fetching tasks with keywords');
        return Task::with('keywords')->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request)
    {
        $validated = $request->validated();
        Log::info('Creating task', $validated);

        $data = [
            'title' => $validated['title'],
            'is_done' => $validated['is_done'] ?? false,
        ];

        $task = Task::create($data);

        if (!empty($validated['keywords']) && is_array($validated['keywords'])) {
            $keywords = [];
            foreach ($validated['keywords'] as $keywordName) {
                $keywords[] = Keyword::firstOrCreate(['name' => $keywordName])->id;
            }
            $task->keywords()->attach($keywords);
        }

        return response()->json($task->load('keywords'), 201);
    }

    /**
     * Toggle the is_done status of the specified resource.
     */
    public function toggle($taskId)
    {
        $task = Task::findOrFail($taskId);
        $task->is_done = !$task->is_done;
        $task->save();

        return response()->json($task->load('keywords'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::with('keywords')->findOrFail($id);
        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     * Resyncs keywords if provided.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::findOrFail($id);
        $task->update($request->only(['title', 'is_done']));
        if ($request->has('keywords')) {
        $keywordIds = collect($request->input('keywords'))->map(function ($name) {
            return Keyword::firstOrCreate(['name' => $name])->id;
        })->toArray();

        $task->keywords()->sync($keywordIds);
    }

        return response()->json($task->load('keywords'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);
        $task->keywords()->detach();
        $task->delete();

        return response()->json(null, 204);
    }
}
