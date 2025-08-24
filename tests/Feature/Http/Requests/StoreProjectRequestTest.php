<?php

namespace Tests\Feature\Http\Requests;

use Tests\TestCase;
use App\Http\Requests\StoreProjectRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Validator;

class StoreProjectRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('projects')) {
            Schema::create('projects', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            });
        } else {
            DB::table('projects')->delete();
        }
    }

    public function test_authorize_returns_true_for_any_user(): void
    {
        $request = new StoreProjectRequest();
        $this->assertTrue($request->authorize());
    }

    public function test_accepts_valid_unique_string_name(): void
    {
        $data = ['name' => 'Project ' . uniqid('valid_', true)];

        $validator = Validator::make($data, (new StoreProjectRequest())->rules());

        $this->assertTrue($validator->passes(), 'Expected validation to pass for a valid unique name.');
    }

    public function test_accepts_name_at_max_length(): void
    {
        $data = ['name' => str_repeat('a', 255)];

        $validator = Validator::make($data, (new StoreProjectRequest())->rules());

        $this->assertTrue($validator->passes(), 'Expected validation to pass for name at maximum length (255).');
    }

    public function test_rejects_missing_name_field(): void
    {
        $data = [];

        $validator = Validator::make($data, (new StoreProjectRequest())->rules());

        $this->assertTrue($validator->fails(), 'Expected validation to fail when name is missing.');
        $this->assertTrue($validator->errors()->has('name'));
    }

    public function test_rejects_name_exceeding_max_length(): void
    {
        $data = ['name' => str_repeat('a', 256)];

        $validator = Validator::make($data, (new StoreProjectRequest())->rules());

        $this->assertTrue($validator->fails(), 'Expected validation to fail when name exceeds 255 characters.');
        $this->assertTrue($validator->errors()->has('name'));
    }

    public function test_rejects_duplicate_project_name(): void
    {
        $existingName = 'Existing Project Name';

        DB::table('projects')->insert([
            'name' => $existingName,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $data = ['name' => $existingName];

        $validator = Validator::make($data, (new StoreProjectRequest())->rules());

        $this->assertTrue($validator->fails(), 'Expected validation to fail for duplicate project name.');
        $this->assertTrue($validator->errors()->has('name'));
    }
}
