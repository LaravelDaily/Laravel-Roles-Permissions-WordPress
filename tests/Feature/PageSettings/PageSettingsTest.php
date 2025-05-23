<?php

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

uses(RefreshDatabase::class);

it('can open the page settings page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('pageSettings.index'))
        ->assertStatus(200);

    $this->assertDatabaseHas('settings', [
        'title' => 'Laravel',
        'maintenance_mode' => '0',
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
        ->assertStatus(302);

    $this->assertDatabaseHas('settings', [
        'title' => 'Test Page',
        'maintenance_mode' => '1',
    ]);
});
