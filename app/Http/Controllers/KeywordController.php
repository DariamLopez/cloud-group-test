<?php

namespace App\Http\Controllers;

use App\Http\Requests\KeywordRequest;
use App\Models\Keyword;
use App\Models\Task;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as FacadesLog;

class KeywordController extends Controller
{
    public function index()
    {
        $keywords = Keyword::all();
        return response()->json($keywords);
    }

    public function show($id)
    {
        $keyword = Keyword::with('tasks')->findOrFail($id);
        return response()->json($keyword);
    }

    public function store(KeywordRequest $request)
    {
        $keyword = Keyword::create([
            'name' => trim(mb_strtolower($request->input('name'))),
        ]);

        return response()->json($keyword, 201);
    }

    public function destroy($id)
    {
        $keyword = Keyword::findOrFail($id);
        $keyword->tasks()->detach();
        $keyword->delete();

        return response()->json(null, 204);
    }
    public function attachToTask(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);
        foreach ($request->input('keywords') as $keywordName) {
            $keyword = Keyword::firstOrCreate(['name' => trim(mb_strtolower($keywordName))]);
            $keyword->tasks()->attach($taskId);
        }


        return response()->json($task->load('keywords'));
    }
    public function dettachFromTask(Request $request, $keywordId, $taskId)
    {
        $keyword = Keyword::findOrFail($keywordId);
        $task = Task::findOrFail($taskId);
        $keyword->tasks()->detach($taskId);

        return response()->json($task->load('keywords'));
    }
}
