<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_index_screen_can_be_rendered(): void
    {
        $user = User::create([
            'name'   => 'Alice Test',
            'email'  => 'alice@test.com',
            'role'   => 'Lead Architect',
            'status' => 'active',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Users Management');
        $response->assertSee('Alice Test');
        $response->assertSee('alice@test.com');
    }

    public function test_new_user_can_be_created_the_laravel_way(): void
    {
        $response = $this->post(route('users.store'), [
            'name'   => 'Samantha Ray',
            'email'  => 'samantha@example.com',
            'role'   => 'DevOps Engineer',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'samantha@example.com',
            'role'  => 'DevOps Engineer',
        ]);
    }

    public function test_user_can_be_updated_the_laravel_way(): void
    {
        $user = User::create([
            'name'   => 'Bob Original',
            'email'  => 'bob@test.com',
            'role'   => 'Junior Dev',
            'status' => 'pending',
        ]);

        $response = $this->put(route('users.update', $user), [
            'name'   => 'Bob Promoted',
            'email'  => 'bob@test.com',
            'role'   => 'Senior Dev',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id'     => $user->id,
            'name'   => 'Bob Promoted',
            'role'   => 'Senior Dev',
            'status' => 'active',
        ]);
    }

    public function test_user_can_be_deleted_the_laravel_way(): void
    {
        $user = User::create([
            'name'   => 'To Delete',
            'email'  => 'delete@test.com',
            'role'   => 'Tester',
            'status' => 'inactive',
        ]);

        $response = $this->delete(route('users.destroy', $user));

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_eloquent_snippet_runner_executes_query(): void
    {
        User::create([
            'name'   => 'Live Query User',
            'email'  => 'live@test.com',
            'role'   => 'Architect',
            'status' => 'active',
        ]);

        $response = $this->post(route('users.run'), [
            'code' => "User::where('status', 'active')->get();",
        ]);

        $response->assertStatus(200);
        $response->assertSee('Live Query User');
        $response->assertSee('Eloquent executed successfully!');
    }

    public function test_guide_page_can_be_rendered(): void
    {
        $response = $this->get(route('guide'));

        $response->assertStatus(200);
        $response->assertSee('Vanilla PHP (PDO) vs. The Laravel Way');
    }
}
