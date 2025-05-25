<?php

use App\Models\Post;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('can create a post', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('posts.store'), [
            'title' => 'Test Post',
            'content' => 'Test Content',
            'is_published' => '1',
            'meta_title' => 'Test Meta Title',
            'meta_description' => 'Test Meta Description',
        ])
        ->assertStatus(403);
});

it('admin can create post', function () {
    Artisan::call('db:seed', ['--class' => RoleSeeder::class]);

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('posts.store'), [
            'title' => 'Test Post',
            'content' => 'Test Content',
            'is_published' => '1',
            'meta_title' => 'Test Meta Title',
            'meta_description' => 'Test Meta Description',
        ])
        ->assertStatus(302);

    $this->assertDatabaseHas('posts', [
        'title' => 'Test Post',
        'content' => 'Test Content',
        'is_published' => '1',
        'meta_title' => 'Test Meta Title',
        'meta_description' => 'Test Meta Description',
    ]);
});

it('can update a post', function () {
    $post = Post::factory()->create();

    $this->actingAs(User::factory()->create())
        ->put(route('posts.update', $post->id), [
            'title' => 'Updated Post',
            'content' => 'Updated Content',
            'is_published' => '0',
            'meta_title' => 'Updated Meta Title',
            'meta_description' => 'Updated Meta Description',
        ])
        ->assertStatus(403);
});

it('admin can update a post', function () {
    Artisan::call('db:seed', ['--class' => RoleSeeder::class]);

    $post = Post::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('posts.update', $post->id), [
            'title' => 'Updated Post',
            'content' => 'Updated Content',
            'is_published' => '0',
            'meta_title' => 'Updated Meta Title',
            'meta_description' => 'Updated Meta Description',
        ])
        ->assertStatus(302);

    $this->assertDatabaseHas('posts', [
        'title' => 'Updated Post',
        'content' => 'Updated Content',
        'is_published' => '0',
        'meta_title' => 'Updated Meta Title',
        'meta_description' => 'Updated Meta Description',
    ]);
});

it('can delete a post', function () {
    $post = Post::factory()->create();

    $this->actingAs(User::factory()->create())
        ->delete(route('posts.destroy', $post->id))
        ->assertStatus(403);
});

it('admin can delete a post', function () {
    Artisan::call('db:seed', ['--class' => RoleSeeder::class]);

    $post = Post::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->delete(route('posts.destroy', $post->id))
        ->assertStatus(302);

    $this->assertDatabaseMissing('posts', [
        'id' => $post->id,
    ]);
});

it('can open the posts index page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('posts.index'))
        ->assertStatus(403);
});

it('admin can open the posts index page', function () {
    Artisan::call('db:seed', ['--class' => RoleSeeder::class]);

    $posts = Post::factory(10)->create();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('posts.index'))
        ->assertStatus(200)
        ->assertSee('Posts')
        ->assertSee($posts->pluck('title')->toArray())
        ->assertSee('Add Post')
        ->assertSee(route('posts.edit', $posts->first()->id)) // Acts as edit button as we have icon only
        ->assertSee(route('posts.destroy', $posts->first()->id)); // Acts as delete button as we have icon only
});

it('role with posts-view permission can open the posts index page', function () {
    $posts = Post::factory(10)->create();
    $permission = Permission::create(['name' => 'posts-view']);
    $role = Role::create(['name' => 'Test Role']);
    $role->givePermissionTo($permission);

    $user = User::factory()->create();
    $user->assignRole($role);

    $this->actingAs($user)
        ->get(route('posts.index'))
        ->assertStatus(200)
        ->assertSee($posts->pluck('title')->toArray())
        ->assertDontSee('Add Post')
        ->assertDontSee(route('posts.edit', $posts->first()->id)) // Acts as edit button as we have icon only
        ->assertDontSee(route('posts.destroy', $posts->first()->id)); // Acts as delete button as we have icon only
});
