<?php

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can open the page settings page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('pageSettings.index'))
        ->assertStatus(403);
});

it('admin can open the page settings page', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('pageSettings.index'))
        ->assertStatus(200);
});

it('users without permission cannot open the page settings page', function (User $user) {
    $this->actingAs($user)
        ->get(route('pageSettings.index'))
        ->assertStatus(403);
})->with([
    fn() => User::factory()->create(),
    fn() => User::factory()->editor()->create(),
    fn() => User::factory()->author()->create(),
    fn() => User::factory()->contributor()->create(),
]);

it('admin can update page settings', function () {
    Setting::create([
        'title' => 'Laravel',
        'maintenance_mode' => '0',
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('pageSettings.update'), [
            'title' => 'Test Page',
            'maintenance_mode' => '1',
        ])
        ->assertStatus(302);

    $this->assertDatabaseHas('settings', [
        'title' => 'Test Page',
        'maintenance_mode' => '1',
    ]);
});

it('users without permission cannot update page settings', function (User $user) {
    Setting::create([
        'title' => 'Laravel',
        'maintenance_mode' => '0',
    ]);

    $this->actingAs($user)
        ->post(route('pageSettings.update'), [
            'title' => 'Test Page',
            'maintenance_mode' => '1',
        ])
        ->assertStatus(403);
})->with([
    fn() => User::factory()->create(),
    fn() => User::factory()->editor()->create(),
    fn() => User::factory()->author()->create(),
    fn() => User::factory()->contributor()->create(),
]);
