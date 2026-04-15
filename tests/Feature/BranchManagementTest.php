<?php

namespace Tests\Feature;

use App\Modules\IAM\Models\User;
use App\Modules\Organization\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchManagementTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        static $index = 0;
        $index++;

        return User::create([
            'name' => ucfirst($role).' User',
            'email' => $role.$index.'@example.com',
            'password' => 'password',
            'role' => $role,
        ]);
    }

    public function test_guest_is_redirected_from_branch_index(): void
    {
        $response = $this->get(route('branches.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_staff_can_view_branch_index(): void
    {
        $user = $this->makeUser('staff');

        Branch::create(['name' => 'North', 'status' => 'active']);

        $response = $this->actingAs($user)->get(route('branches.index'));

        $response->assertOk();
        $response->assertSeeText('North');
    }

    public function test_staff_cannot_create_branch(): void
    {
        $user = $this->makeUser('staff');

        $response = $this->actingAs($user)->post(route('branches.store'), [
            'name' => 'Unauthorized Branch',
            'status' => 'active',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('branches', ['name' => 'Unauthorized Branch']);
    }

    public function test_admin_can_create_branch(): void
    {
        $user = $this->makeUser('admin');

        $response = $this->actingAs($user)->post(route('branches.store'), [
            'name' => 'Created Branch',
            'location' => 'Metro City',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('branches.index'));
        $this->assertDatabaseHas('branches', [
            'name' => 'Created Branch',
            'location' => 'Metro City',
            'status' => 'active',
        ]);
    }

    public function test_branch_creation_requires_name_and_valid_status(): void
    {
        $user = $this->makeUser('admin');

        $response = $this->actingAs($user)
            ->from(route('branches.create'))
            ->post(route('branches.store'), [
                'name' => '',
                'status' => 'archived',
            ]);

        $response->assertRedirect(route('branches.create'));
        $response->assertSessionHasErrors(['name', 'status']);
    }

    public function test_branch_creation_requires_unique_email(): void
    {
        $user = $this->makeUser('admin');

        Branch::create([
            'name' => 'Main Branch',
            'email' => 'main@example.com',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)
            ->from(route('branches.create'))
            ->post(route('branches.store'), [
                'name' => 'Another Branch',
                'email' => 'main@example.com',
                'status' => 'active',
            ]);

        $response->assertRedirect(route('branches.create'));
        $response->assertSessionHasErrors(['email']);
    }

    public function test_staff_cannot_update_branch(): void
    {
        $staff = $this->makeUser('staff');
        $branch = Branch::create(['name' => 'Original Name', 'status' => 'active']);

        $response = $this->actingAs($staff)->put(route('branches.update', $branch), [
            'name' => 'Changed Name',
            'status' => 'active',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'name' => 'Original Name',
        ]);
    }

    public function test_manager_can_update_branch(): void
    {
        $manager = $this->makeUser('manager');
        $branch = Branch::create(['name' => 'Legacy Name', 'status' => 'active']);

        $response = $this->actingAs($manager)->put(route('branches.update', $branch), [
            'name' => 'Updated Name',
            'status' => 'inactive',
        ]);

        $response->assertRedirect(route('branches.index'));
        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'name' => 'Updated Name',
            'status' => 'inactive',
        ]);
    }

    public function test_manager_cannot_delete_branch(): void
    {
        $manager = $this->makeUser('manager');
        $branch = Branch::create(['name' => 'Protected Branch', 'status' => 'active']);

        $response = $this->actingAs($manager)->delete(route('branches.destroy', $branch));

        $response->assertForbidden();
        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'name' => 'Protected Branch',
        ]);
    }

    public function test_admin_can_delete_branch(): void
    {
        $admin = $this->makeUser('admin');
        $branch = Branch::create(['name' => 'Disposable Branch', 'status' => 'active']);

        $response = $this->actingAs($admin)->delete(route('branches.destroy', $branch));

        $response->assertRedirect(route('branches.index'));
        $this->assertDatabaseMissing('branches', [
            'id' => $branch->id,
        ]);
    }

    public function test_admin_sees_create_edit_and_delete_actions_on_index(): void
    {
        $admin = $this->makeUser('admin');
        $branch = Branch::create(['name' => 'Action Branch', 'status' => 'active']);
        $deleteFormAction = '<form action="'.route('branches.destroy', $branch).'" method="POST"';

        $response = $this->actingAs($admin)->get(route('branches.index'));

        $response->assertOk();
        $response->assertSee(route('branches.create'), false);
        $response->assertSee(route('branches.edit', $branch), false);
        $response->assertSee($deleteFormAction, false);
    }

    public function test_manager_sees_create_and_edit_but_not_delete_on_index(): void
    {
        $manager = $this->makeUser('manager');
        $branch = Branch::create(['name' => 'Manager Branch', 'status' => 'active']);
        $deleteFormAction = '<form action="'.route('branches.destroy', $branch).'" method="POST"';

        $response = $this->actingAs($manager)->get(route('branches.index'));

        $response->assertOk();
        $response->assertSee(route('branches.create'), false);
        $response->assertSee(route('branches.edit', $branch), false);
        $response->assertDontSee($deleteFormAction, false);
    }

    public function test_staff_sees_view_only_actions_on_index(): void
    {
        $staff = $this->makeUser('staff');
        $branch = Branch::create(['name' => 'Staff Branch', 'status' => 'active']);
        $deleteFormAction = '<form action="'.route('branches.destroy', $branch).'" method="POST"';

        $response = $this->actingAs($staff)->get(route('branches.index'));

        $response->assertOk();
        $response->assertDontSee(route('branches.create'), false);
        $response->assertDontSee(route('branches.edit', $branch), false);
        $response->assertDontSee($deleteFormAction, false);
        $response->assertSee(route('branches.show', $branch), false);
    }

    public function test_staff_cannot_see_edit_action_on_show(): void
    {
        $staff = $this->makeUser('staff');
        $branch = Branch::create(['name' => 'Show Staff', 'status' => 'active']);

        $response = $this->actingAs($staff)->get(route('branches.show', $branch));

        $response->assertOk();
        $response->assertDontSee(route('branches.edit', $branch), false);
    }

    public function test_manager_can_see_edit_action_on_show(): void
    {
        $manager = $this->makeUser('manager');
        $branch = Branch::create(['name' => 'Show Manager', 'status' => 'active']);

        $response = $this->actingAs($manager)->get(route('branches.show', $branch));

        $response->assertOk();
        $response->assertSee(route('branches.edit', $branch), false);
    }

    public function test_branch_index_supports_search_status_and_pagination(): void
    {
        $user = $this->makeUser('admin');

        Branch::create(['name' => 'North Alpha', 'location' => 'North City', 'status' => 'active']);
        Branch::create(['name' => 'South Alpha', 'location' => 'South City', 'status' => 'inactive']);

        for ($i = 1; $i <= 12; $i++) {
            Branch::create(['name' => "Branch {$i}", 'status' => 'active']);
        }

        $response = $this->actingAs($user)->get(route('branches.index', [
            'search' => 'North',
            'status' => 'active',
        ]));

        $response->assertOk();
        $response->assertSeeText('North Alpha');
        $response->assertDontSeeText('South Alpha');

        $paginatedResponse = $this->actingAs($user)->get(route('branches.index'));
        $paginatedResponse->assertOk();
        $paginatedResponse->assertSeeText('Next');
    }
}