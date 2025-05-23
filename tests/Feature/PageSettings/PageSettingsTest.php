<?php

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

it('can open the page settings page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('pageSettings.index'))
        ->assertStatus(403);
});

it('admin can open the page settings page', function () {
    Artisan::call('db:seed', ['--class' => RoleSeeder::class]);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('pageSettings.index'))
        ->assertStatus(200);
});

it('admin can update page settings', function () {
    Artisan::call('db:seed', ['--class' => RoleSeeder::class]);

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

it('can update page settings', function () {
    Setting::create([
        'title' => 'Laravel',
        'maintenance_mode' => '0',
    ]);

    $this->actingAs(User::factory()->create())
        ->post(route('pageSettings.update'), [
            'title' => 'Test Page',
            'maintenance_mode' => '1',
        ])
        ->assertStatus(403);
});
