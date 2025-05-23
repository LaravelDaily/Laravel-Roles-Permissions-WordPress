<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        ->assertStatus(302);

    $this->assertDatabaseMissing('posts', [
        'id' => $post->id,
    ]);
});

it('can open the posts index page', function () {
    $posts = Post::factory(10)->create();

    $this->actingAs(User::factory()->create())
        ->get(route('posts.index'))
        ->assertStatus(200)
        ->assertSee('Posts')
        ->assertSee($posts->pluck('title')->toArray());
});
