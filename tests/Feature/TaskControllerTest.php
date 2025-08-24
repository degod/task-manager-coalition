<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_reorder_updates_priorities_and_returns_success_json(): void
    {
        $project = Project::create(['name' => 'Alpha']);

        $t1 = Task::create(['name' => 'T1', 'project_id' => $project->id, 'priority' => 1]);
        $t2 = Task::create(['name' => 'T2', 'project_id' => $project->id, 'priority' => 2]);
        $t3 = Task::create(['name' => 'T3', 'project_id' => $project->id, 'priority' => 3]);

        $response = $this->postJson(route('task.reorder'), [
            'tasks' => [$t2->id, $t3->id, $t1->id],
        ]);

        $response->assertOk()->assertJson(['message' => 'Reordered']);

        $t1->refresh();
        $t2->refresh();
        $t3->refresh();

        $this->assertSame(1, $t1->priority);
        $this->assertSame(3, $t2->priority);
        $this->assertSame(2, $t3->priority);
    }

    public function test_store_creates_task_with_incremented_priority_and_redirects(): void
    {
        $project = Project::create(['name' => 'Alpha']);

        Task::create(['name' => 'Existing', 'project_id' => $project->id, 'priority' => 5]);

        $response = $this->post(route('task.store'), [
            'name' => 'New Task',
            'project_id' => $project->id,
        ]);

        $response->assertRedirect(route('task.index'));

        $created = Task::where('name', 'New Task')->first();
        $this->assertNotNull($created);
        $this->assertSame(6, $created->priority);
        $this->assertSame($project->id, $created->project_id);
    }

    public function test_update_applies_validated_data_and_redirects(): void
    {
        $projectA = Project::create(['name' => 'Alpha']);
        $projectB = Project::create(['name' => 'Beta']);

        $task = Task::create([
            'name' => 'Old Name',
            'project_id' => $projectA->id,
            'priority' => 1,
        ]);

        $response = $this->put(route('task.update', $task), [
            'name' => 'New Name',
            'project_id' => $projectB->id,
        ]);

        $response->assertRedirect(route('task.index'));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'New Name',
            'project_id' => $projectB->id,
        ]);
    }

    public function test_store_with_empty_table_sets_priority_to_one(): void
    {
        $project = Project::create(['name' => 'Alpha']);

        $response = $this->post(route('task.store'), [
            'name' => 'First Task',
            'project_id' => $project->id,
        ]);

        $response->assertRedirect(route('task.index'));

        $this->assertDatabaseHas('tasks', [
            'name' => 'First Task',
            'project_id' => $project->id,
            'priority' => 1,
        ]);
    }

    public function test_reorder_ignores_invalid_ids_and_returns_success(): void
    {
        $project = Project::create(['name' => 'Alpha']);

        $t1 = Task::create(['name' => 'T1', 'project_id' => $project->id, 'priority' => 1]);
        $t2 = Task::create(['name' => 'T2', 'project_id' => $project->id, 'priority' => 2]);

        $invalidId = 999999;

        $response = $this->postJson(route('task.reorder'), [
            'tasks' => [$invalidId, $t1->id, $t2->id],
        ]);

        $response->assertOk()->assertJson(['message' => 'Reordered']);

        $t1->refresh();
        $t2->refresh();

        $this->assertSame(2, $t1->priority);
        $this->assertSame(1, $t2->priority);
    }

    public function test_update_with_invalid_project_id_returns_validation_error(): void
    {
        $project = Project::create(['name' => 'Alpha']);

        $task = Task::create([
            'name' => 'Initial',
            'project_id' => $project->id,
            'priority' => 1,
        ]);

        $this->from(route('task.index'));
        $response = $this->put(route('task.update', $task), [
            'name' => 'Attempted Update',
            'project_id' => 123456, // non-existent
        ]);

        $response->assertRedirect(route('task.index'));
        $response->assertSessionHasErrors(['project_id']);

        $task->refresh();
        $this->assertSame('Initial', $task->name);
        $this->assertSame($project->id, $task->project_id);
    }

    public function test_index_renders_view_with_tasks_ordered_desc_and_eager_loaded_projects(): void
    {
        $projectA = Project::create(['name' => 'Alpha']);
        $projectB = Project::create(['name' => 'Beta']);

        $t1 = Task::create(['name' => 'Low', 'project_id' => $projectA->id, 'priority' => 1]);
        $t2 = Task::create(['name' => 'High', 'project_id' => $projectA->id, 'priority' => 5]);
        $t3 = Task::create(['name' => 'Mid', 'project_id' => $projectB->id, 'priority' => 3]);

        $response = $this->get(route('task.index'));

        $response->assertOk()->assertViewIs('index');

        $response->assertViewHas('tasks', function ($tasks) use ($t1, $t2, $t3) {
            $orderedIds = $tasks->pluck('id')->all();
            $expectedOrder = [$t2->id, $t3->id, $t1->id];

            $eagerLoaded = $tasks->every(function ($task) {
                return $task->relationLoaded('project');
            });

            return $orderedIds === $expectedOrder && $eagerLoaded;
        });
    }

    public function test_destroy_deletes_task_and_redirects_to_index(): void
    {
        $project = Project::create(['name' => 'Alpha']);
        $task = Task::create(['name' => 'ToDelete', 'project_id' => $project->id, 'priority' => 1]);

        $response = $this->delete(route('task.destroy', $task));

        $response->assertRedirect(route('task.index'));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_store_persists_name_and_project_association(): void
    {
        $project = Project::create(['name' => 'Alpha']);

        $response = $this->post(route('task.store'), [
            'name' => 'Persisted Task',
            'project_id' => $project->id,
        ]);

        $response->assertRedirect(route('task.index'));

        $this->assertDatabaseHas('tasks', [
            'name' => 'Persisted Task',
            'project_id' => $project->id,
        ]);
    }

    public function test_store_with_invalid_project_id_returns_validation_error_and_no_creation(): void
    {
        $this->from(route('task.index'));

        $response = $this->post(route('task.store'), [
            'name' => 'Invalid FK',
            'project_id' => 999999,
        ]);

        $response->assertRedirect(route('task.index'));
        $response->assertSessionHasErrors(['project_id']);

        $this->assertSame(0, Task::count());
    }

    public function test_update_ignores_unvalidated_fields_including_priority(): void
    {
        $projectA = Project::create(['name' => 'Alpha']);
        $projectB = Project::create(['name' => 'Beta']);

        $task = Task::create([
            'name' => 'Original',
            'project_id' => $projectA->id,
            'priority' => 10,
        ]);

        $response = $this->put(route('task.update', $task), [
            'name' => 'Updated Name',
            'project_id' => $projectB->id,
            'priority' => 1, // should be ignored
        ]);

        $response->assertRedirect(route('task.index'));

        $task->refresh();
        $this->assertSame('Updated Name', $task->name);
        $this->assertSame($projectB->id, $task->project_id);
        $this->assertSame(10, $task->priority); // unchanged
    }

    public function test_reorder_with_empty_tasks_array_is_noop_and_returns_success(): void
    {
        $project = Project::create(['name' => 'Alpha']);

        $t1 = Task::create(['name' => 'T1', 'project_id' => $project->id, 'priority' => 1]);
        $t2 = Task::create(['name' => 'T2', 'project_id' => $project->id, 'priority' => 2]);

        $response = $this->postJson(route('task.reorder'), [
            'tasks' => [],
        ]);

        $response->assertOk()->assertJson(['message' => 'Reordered']);

        $t1->refresh();
        $t2->refresh();

        $this->assertSame(1, $t1->priority);
        $this->assertSame(2, $t2->priority);
    }
}
