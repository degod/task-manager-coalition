<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;

class ProjectController extends Controller
{
    public function store(StoreProjectRequest $request)
    {
        Project::create($request->validated());

        return redirect()->route('task.index');
    }
}
