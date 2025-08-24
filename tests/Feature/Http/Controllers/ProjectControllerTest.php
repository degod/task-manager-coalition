<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\Project;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\ProjectController;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Define minimal routes required for the tests
        Route::middleware('web')->group(function () {
            Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
            Route::get('/tasks', fn() => 'tasks')->name('task.index');
        });
    }

    public function test_stores_project_with_valid_unique_name_persists_record(): void
    {
        $payload = ['name' => 'Alpha Project'];

        $response = $this->post(route('projects.store'), $payload);

        $response->assertRedirect(route('task.index'));
        $this->assertDatabaseHas('projects', ['name' => 'Alpha Project']);
    }

    public function test_redirects_to_task_index_after_successful_store(): void
    {
        $payload = ['name' => 'Beta Project'];

        $response = $this->post(route('projects.store'), $payload);

        $response->assertRedirect(route('task.index'));
    }

    public function test_ignores_unvalidated_fields_when_creating_project(): void
    {
        $payload = [
            'name' => 'Gamma Project',
            'id' => 999,
            'created_at' => '2000-01-01 00:00:00',
            'unexpected' => 'value',
        ];

        $this->post(route('projects.store'), $payload)->assertRedirect(route('task.index'));

        $project = Project::where('name', 'Gamma Project')->firstOrFail();

        $this->assertNotEquals(999, $project->id);
        $this->assertNotEquals('2000-01-01 00:00:00', optional($project->created_at)->format('Y-m-d H:i:s'));
        $this->assertDatabaseHas('projects', ['name' => 'Gamma Project']);
    }

    public function test_fails_validation_on_duplicate_project_name_and_does_not_persist(): void
    {
        Project::create(['name' => 'Existing Project']);

        $response = $this->from('/projects/create')
            ->post(route('projects.store'), ['name' => 'Existing Project']);

        $response->assertRedirect('/projects/create');
        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('projects', 1);
    }

    public function test_fails_validation_when_name_is_missing_and_does_not_persist(): void
    {
        $response = $this->from('/projects/create')
            ->post(route('projects.store'), []);

        $response->assertRedirect('/projects/create');
        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('projects', 0);
    }

    public function test_fails_validation_when_name_exceeds_max_length_and_does_not_persist(): void
    {
        $longName = str_repeat('a', 256);

        $response = $this->from('/projects/create')
            ->post(route('projects.store'), ['name' => $longName]);

        $response->assertRedirect('/projects/create');
        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('projects', 0);
    }
}
