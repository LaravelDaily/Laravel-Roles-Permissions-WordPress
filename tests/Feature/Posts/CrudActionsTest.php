<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can view all posts in list', function (User $user) {
    $posts = Post::factory(10)->create();

    $this->actingAs($user)
        ->get(route('posts.index'))
        ->assertStatus(200)
        ->assertSee($posts->pluck('title')->toArray());
})->with([
    fn() => User::factory()->admin()->create(),
    fn() => User::factory()->editor()->create(),
]);

it('can edit all posts', function (User $user) {
    $posts = Post::factory(10)->create();

    $editLinks = $posts->map(fn(Post $post) => route('posts.edit', $post->id));

    $this->actingAs($user)
        ->get(route('posts.index'))
        ->assertStatus(200)
        ->assertSee($editLinks->toArray());

    $editLinks->each(fn($link) => $this->actingAs($user)
        ->get($link)
        ->assertStatus(200));
})->with([
    fn() => User::factory()->admin()->create(),
    fn() => User::factory()->editor()->create(),
]);

it('can create posts with publishing', function (User $user) {
    $this->actingAs($user)
        ->get(route('posts.create'))
        ->assertSee('Published')
        ->assertSee('Create Post');

    $this->actingAs($user)
        ->post(route('posts.store'), [
            'title' => 'Test Post',
            'content' => 'Test Content',
            'is_published' => '1',
            'meta_title' => 'Test Meta Title',
            'meta_description' => 'Test Meta Description',
        ])
        ->assertStatus(302);
})->with([
    fn() => User::factory()->admin()->create(),
    fn() => User::factory()->editor()->create(),
]);

it('can delete all posts', function (User $user) {
    $posts = Post::factory(10)->create();

    $posts->each(fn($post) => $this->actingAs($user)
        ->delete(route('posts.destroy', $post->id))
        ->assertStatus(302));
})->with([
    fn() => User::factory()->admin()->create(),
    fn() => User::factory()->editor()->create(),
]);

it('can view only his posts in list', function (User $user) {
    $posts = Post::factory(10)->create([
        'user_id' => $user->id,
    ]);

    $otherPosts = Post::factory(10)->create();

    $this->actingAs($user)
        ->get(route('posts.index'))
        ->assertStatus(200)
        ->assertSee($posts->pluck('title')->toArray())
        ->assertDontSee($otherPosts->pluck('title')->toArray());
})->with([
    fn() => User::factory()->author()->create(),
    fn() => User::factory()->contributor()->create(),
]);

it('author can create and publish his own posts', function (User $user) {
    $this->actingAs($user)
        ->get(route('posts.create'))
        ->assertSee('Published')
        ->assertSee('Create Post');

    $this->actingAs($user)
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
        'is_published' => true,
        'meta_title' => 'Test Meta Title',
        'meta_description' => 'Test Meta Description',
    ]);
})->with([
    fn() => User::factory()->author()->create(),
]);

it('author can edit his own posts', function (User $user) {
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->get(route('posts.edit', $post->id))
        ->assertStatus(200)
        ->assertSee('Edit Post')
        ->assertSee('Update Post');

    $this->actingAs($user)
        ->put(route('posts.update', $post->id), [
            'title' => 'Test Post',
            'content' => 'Test Content',
        ])
        ->assertStatus(302);

    $this->assertDatabaseHas('posts', [
        'title' => 'Test Post',
        'content' => 'Test Content',
    ]);
})->with([
    fn() => User::factory()->author()->create(),
    fn() => User::factory()->contributor()->create(),
]);

it('author can delete his own posts', function (User $user) {
    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->delete(route('posts.destroy', $post->id))
        ->assertStatus(302);

    $this->assertDatabaseMissing('posts', [
        'id' => $post->id,
    ]);
})->with([
    fn() => User::factory()->author()->create(),
    fn() => User::factory()->contributor()->create(),
]);

// Contributor can only view his own posts and can't publish them

it('contributor cant publish posts', function (User $user) {
    $this->actingAs($user)
        ->get(route('posts.create'))
        ->assertDontSee('Published')
        ->assertSee('Create Post');

    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->post(route('posts.store'), [
            'title' => 'Test Post',
            'content' => 'Test Content',
            'is_published' => '1',
        ])
        ->assertStatus(302);

    $this->assertDatabaseHas('posts', [
        'title' => 'Test Post',
        'content' => 'Test Content',
        'is_published' => false,
    ]);

    $this->actingAs($user)
        ->get(route('posts.edit', $post->id))
        ->assertDontSee('Published')
        ->assertSee('Edit Post');

    $this->actingAs($user)
        ->put(route('posts.update', $post->id), [
            'title' => 'Test Post',
            'content' => 'Test Content',
            'is_published' => '1',
        ])
        ->assertStatus(302);

    $this->assertDatabaseHas('posts', [
        'title' => 'Test Post',
        'content' => 'Test Content',
        'is_published' => false,
    ]);
})->with([
    fn() => User::factory()->contributor()->create(),
]);

it('cant edit other users posts', function (User $user) {
    $post = Post::factory()->create();

    $this->actingAs($user)
        ->get(route('posts.edit', $post->id))
        ->assertStatus(403);
})->with([
    fn() => User::factory()->author()->create(),
    fn() => User::factory()->contributor()->create(),
]);

it('cant delete other users posts', function (User $user) {
    $post = Post::factory()->create();

    $this->actingAs($user)
        ->delete(route('posts.destroy', $post->id))
        ->assertStatus(403);
})->with([
    fn() => User::factory()->author()->create(),
    fn() => User::factory()->contributor()->create(),
]);
