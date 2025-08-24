<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
</head>

<body class="p-4">

    <div class="mb-3">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#taskModal"
            @if($projects->count() === 0) disabled @endif>
            Add Task
        </button>
        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#projectModal">
            Add Project
        </button>
    </div>

    <table class="table table-bordered" id="taskTable">
        <thead>
            <tr>
                <th>Task</th>
                <th>Project</th>
                <th>Priority</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="taskList">
            @foreach($tasks as $task)
            <tr data-id="{{ $task->id }}">
                <td>{{ $task->name }}</td>
                <td>{{ $task->project->name ?? 'N/A' }}</td>
                <td>{{ $task->priority }}</td>
                <td>
                    <!-- Edit button -->
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editTaskModal{{ $task->id }}">
                        Edit
                    </button>

                    <!-- Delete button -->
                    <form action="{{ route('task.destroy', $task) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Are you sure you want to delete this task?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>

            <!-- Edit Task Modal -->
            <div class="modal fade" id="editTaskModal{{ $task->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('task.update', $task) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Task</h5>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Task Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $task->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label>Project</label>
                                    <select name="project_id" class="form-select" required>
                                        @foreach($projects as $project)
                                        <option value="{{ $project->id }}"
                                            @if($project->id == $task->project_id) selected @endif>
                                            {{ $project->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary">Update Task</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>


    <!-- Task Modal -->
    <div class="modal fade" id="taskModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('task.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Task</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Task Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Project</label>
                            <select name="project_id" class="form-select" required>
                                @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success">Save Task</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Project Modal -->
    <div class="modal fade" id="projectModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('projects.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Project</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Project Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-warning">Save Project</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let taskList = document.getElementById('taskList');

        new Sortable(taskList, {
            animation: 150,
            onEnd: function() {
                let order = [];
                document.querySelectorAll('#taskList tr').forEach((row) => {
                    order.push(row.dataset.id);
                });

                fetch("{{ route('task.reorder') }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                            tasks: order
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            // reload page once reorder is saved
                            window.location.reload();
                        }
                    })
                    .catch(err => console.error("Reorder failed:", err));
            }
        });
    </script>
</body>

</html>