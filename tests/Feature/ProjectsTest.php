<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $account = Account::create(['name' => 'Acme Corporation']);

        $this->user = factory(User::class)->create([
            'account_id' => $account->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'owner' => true,
        ]);
    }

    public function test_can_view_projects()
    {
        $this->user->account->projects()->saveMany(
            factory(Project::class, 9)->make()
        );

        $this->actingAs($this->user)
            ->get('/projects')
            ->assertStatus(200)
            ->assertPropCount('projects.data', 9)
            ->assertPropValue('projects.data', function ($projects) {
                $this->assertEquals(
                    ['id', 'title', 'description', 'status','priority','creater','due_date','completed_date'
                    'deleted_at'],
                    array_keys($projects[0])
                );
            });
    }

    public function test_can_search_for_projects()
    {
        $this->user->account->projects()->saveMany(
            factory(Project::class, 5)->make()
        )->first()->update([
            'title' => 'Project',
            // 'last_name' => 'Andersson'
        ]);

        $this->actingAs($this->user)
            ->get('/projects?search=Project')
            ->assertStatus(200)
            ->assertPropValue('filters.search', 'Project')
            ->assertPropCount('projects.data', 1)
            ->assertPropValue('projects.data', function ($projects) {
                $this->assertEquals('Greg Andersson', $projects[0]['title']);
            });
    }

    public function test_cannot_view_deleted_projects()
    {
        $this->user->account->projects()->saveMany(
            factory(Project::class, 5)->make()
        )->first()->delete();

        $this->actingAs($this->user)
            ->get('/projects')
            ->assertStatus(200)
            ->assertPropCount('projects.data', 4);
    }

    public function test_can_filter_to_view_deleted_projects()
    {
        $this->user->account->projects()->saveMany(
            factory(Project::class, 5)->make()
        )->first()->delete();

        $this->actingAs($this->user)
            ->get('/projects?trashed=with')
            ->assertStatus(200)
            ->assertPropValue('filters.trashed', 'with')
            ->assertPropCount('projects.data', 5);
    }
}