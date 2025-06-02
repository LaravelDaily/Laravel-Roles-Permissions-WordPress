<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

it('only admins can create users', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->get(route('users.create'))->assertStatus(403);
});

it('non admins cannot create users', function (User $user) {
    $this->actingAs($user);

    $this->get(route('users.create'))->assertStatus(403);
})->with([
    fn() => User::factory()->editor()->create(),
    fn() => User::factory()->author()->create(),
    fn() => User::factory()->contributor()->create(),
]);

it('can create users', function (User $user) {
    $this->actingAs($user);

    $this->post(route('users.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'role' => 'Editor',
    ])->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $this->assertDatabaseHas('model_has_roles', [
        'role_id' => Role::where('name', 'Editor')->first()->id,
    ]);
})->with([
    fn() => User::factory()->admin()->create(),
    fn() => User::factory()->editor()->withPermissions(['users-create'])->create(),
]);

it('can update users without password', function (User $user) {
    $this->actingAs($user);

    $user = User::factory()->create();

    $this->put(route('users.update', $user->id), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'role' => 'Editor',
    ])->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $this->assertDatabaseHas('model_has_roles', [
        'role_id' => Role::where('name', 'Editor')->first()->id,
    ]);

    $user = $user->fresh();
    $this->assertTrue(Hash::check('password', $user->password));
})->with([
    fn() => User::factory()->admin()->create(),
    fn() => User::factory()->editor()->withPermissions(['users-update'])->create(),
]);

it('can update user with assigned permissions', function (User $user) {
    $this->actingAs($user);

    $user = User::factory()->create();

    $this->put(route('users.update', $user->id), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'role' => 'Editor',
        'permissions' => [
            '1' => 'posts-create',
            '2' => 'posts-update',
            '3' => 'posts-delete',
        ],
    ])->assertRedirect(route('users.index'));

    $user = $user->fresh();
    $this->assertTrue($user->hasPermissionTo('posts-create'));
    $this->assertTrue($user->hasPermissionTo('posts-update'));
    $this->assertTrue($user->hasPermissionTo('posts-delete'));
})->with([
    fn() => User::factory()->admin()->create(),
    fn() => User::factory()->editor()->withPermissions(['users-update'])->create(),
]);

it('can view list of users', function (User $user) {
    $users = User::factory()->count(10)->create();
    $this->actingAs($user);

    $this->get(route('users.index'))->assertStatus(200)->assertSeeText($users->pluck('name')->toArray());
})->with([
    fn() => User::factory()->admin()->create(),
]);

it('can delete users', function (User $user) {
    $this->actingAs($user);

    $this->delete(route('users.destroy', $user->id))->assertRedirect(route('users.index'));
})->with([
    fn() => User::factory()->admin()->create(),
    fn() => User::factory()->editor()->withPermissions(['users-delete'])->create(),
]);

it('other roles cannot delete users', function (User $user) {
    $this->actingAs($user);

    $this->delete(route('users.destroy', $user->id))->assertStatus(403);
})->with([
    fn() => User::factory()->editor()->create(),
    fn() => User::factory()->author()->create(),
    fn() => User::factory()->contributor()->create(),
]);

it('other roles cannot edit users', function (User $user) {
    $this->actingAs($user);

    $this->get(route('users.edit', $user->id))->assertStatus(403);
    $this->put(route('users.update', $user->id), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password512',
        'role' => 'Editor',
    ])->assertStatus(403);
})->with([
    fn() => User::factory()->editor()->create(),
    fn() => User::factory()->author()->create(),
    fn() => User::factory()->contributor()->create(),
]);
