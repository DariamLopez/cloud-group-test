<?php

use App\Http\Controllers\KeywordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource("/tasks", App\Http\Controllers\TaskController::class);
Route::post("/tasks/{task}/toggle", [App\Http\Controllers\TaskController::class, 'toggle']);


Route::get("/keywords", [KeywordController::class, 'index']);
Route::post("/keywords", [KeywordController::class, 'store']);
route::get("/keywords/{id}", [KeywordController::class, 'show']);
Route::delete("/keywords/{id}", [KeywordController::class, 'destroy']);
Route::post("/keywords/attach-to-task/{taskId}", [KeywordController::class, 'attachToTask']);
Route::post("/keywords/{keywordId}/dettach-from-task/{taskId}", [KeywordController::class, 'dettachFromTask']);
