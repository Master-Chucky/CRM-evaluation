<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function data(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        return response()->json(Task::paginate($perPage));
    }

    public function nbdata()
    {
        return response()->json([
            "nb_tasks" => Task::count()
        ]);
    }
}
