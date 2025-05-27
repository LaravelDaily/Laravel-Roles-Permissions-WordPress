<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('view', Post::class);

        $posts = Post::query();

        if (! Gate::allows('editOthers', Post::class)) {
            $posts->where('user_id', auth()->user()->id);
        } else {
            $posts->with('user');
        }

        $posts = $posts->get();

        return view('posts.index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Post::class);

        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        Gate::authorize('create', Post::class);

        $data = $request->validated();

        if (! Gate::allows('publish', Post::class)) {
            $data['is_published'] = false;
        }

        Post::create(array_merge($data, [
            'user_id' => auth()->user()->id,
        ]));

        return redirect()->route('posts.index')->with('status', 'Post created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post): View
    {
        Gate::authorize('update', $post);

        if ($post->user_id !== auth()->user()->id) {
            Gate::authorize('editOthers', $post);
        }

        return view('posts.edit', [
            'post' => $post,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        Gate::authorize('update', $post);

        if ($post->user_id !== auth()->user()->id) {
            Gate::authorize('editOthers', $post);
        }

        $data = $request->validated();

        if (! Gate::allows('publish', Post::class)) {
            $data['is_published'] = $post->is_published; // keep the same value
        }

        $post->update($data);

        return redirect()->route('posts.index')->with('status', 'Post updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        Gate::authorize('delete', $post);

        if ($post->user_id !== auth()->user()->id) {
            Gate::authorize('editOthers', $post);
        }

        $post->delete();

        return redirect()->route('posts.index')->with('status', 'Post deleted successfully');
    }
}
