<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function view(User $user): bool
    {
        return $user->hasPermissionTo('posts-view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('posts-create');
    }

    public function update(User $user, Post $post): bool
    {
        if ($post->user_id !== $user->id) {
            return $user->hasPermissionTo('posts-edit-others');
        }

        return $user->hasPermissionTo('posts-update');
    }

    public function delete(User $user, Post $post): bool
    {
        if ($post->user_id !== $user->id) {
            return $user->hasPermissionTo('posts-edit-others');
        }

        return $user->hasPermissionTo('posts-delete');
    }

    public function publish(User $user): bool
    {
        return $user->hasPermissionTo('posts-publish');
    }

    public function editOthers(User $user): bool
    {
        return $user->hasPermissionTo('posts-edit-others');
    }
}
