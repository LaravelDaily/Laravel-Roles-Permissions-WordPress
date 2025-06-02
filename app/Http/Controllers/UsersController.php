<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('view', User::class);

        $users = User::with('roles')->get();

        return view('users.index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', User::class);

        $roles = Role::all();

        return view('users.create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        Gate::authorize('create', User::class);

        $user = User::create($request->validated());
        $user->assignRole($request->validated('role'));

        return redirect()->route('users.index')->with('status', 'User created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        Gate::authorize('update', $user);

        $user->load('roles', 'permissions');

        $roles = Role::all();
        $permissions = Permission::all();

        return view('users.edit', [
            'user' => $user,
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        Gate::authorize('update', $user);

        $user->update($request->validated());
        $user->syncRoles($request->validated('role'));
        $user->syncPermissions($request->validated('permissions'));

        return redirect()->route('users.index')->with('status', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);

        $user->delete();

        return redirect()->route('users.index')->with('status', 'User deleted successfully');
    }
}
