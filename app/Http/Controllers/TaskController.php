<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        $tasks = Task::with('project')->orderByDesc('priority')->get();

        return view('index', compact('projects', 'tasks'));
    }

    public function store(StoreTaskRequest $request)
    {
        $priority = Task::max('priority') + 1;

        Task::create([
            'name' => $request->name,
            'project_id' => $request->project_id,
            'priority' => $priority,
        ]);

        return redirect()->route('task.index');
    }

    public function reorder(Request $request)
    {
        foreach ($request->tasks as $index => $id) {
            Task::where('id', $id)->update(['priority' => count($request->tasks) - $index]);
        }

        return response()->json(['message' => 'Reordered']);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());

        return redirect()->route('task.index');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('task.index');
    }
}
