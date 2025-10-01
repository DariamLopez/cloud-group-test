<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ViewsControllers\TaskViewController;
use Inertia\Inertia;

Route::get('/', [TaskViewController::class, 'index']);
Route::get('/tasks', [TaskViewController::class, 'taskView'])->name('tasks.index');
