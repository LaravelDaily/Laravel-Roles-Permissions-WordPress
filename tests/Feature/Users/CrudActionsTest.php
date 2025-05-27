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

it('can create users', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

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
});

it('can edit users', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $user = User::factory()->create();

    $this->put(route('users.update', $user->id), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password512',
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
    $this->assertTrue(Hash::check('password512', $user->password));
});

it('can update users without password', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

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
});

it('can view list of users', function () {
    $admin = User::factory()->admin()->create();
    $users = User::factory()->count(10)->create();
    $this->actingAs($admin);

    $this->get(route('users.index'))->assertStatus(200)->assertSeeText($users->pluck('name')->toArray());
});

it('can delete users', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $this->actingAs($admin);

    $this->delete(route('users.destroy', $user->id))->assertRedirect(route('users.index'));
});

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
