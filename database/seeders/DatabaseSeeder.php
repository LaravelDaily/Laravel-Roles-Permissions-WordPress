<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);

        // User::factory(10)->create();

        User::factory()
            ->admin()
            ->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        User::factory()
            ->editor()
            ->create([
                'name' => 'Editor',
                'email' => 'editor@example.com',
            ]);

        User::factory()
            ->author()
            ->create([
                'name' => 'Author',
                'email' => 'author@example.com',
            ]);

        User::factory()
            ->contributor()
            ->create([
                'name' => 'Contributor',
                'email' => 'contributor@example.com',
            ]);

        Post::factory(10)->create();
    }
}
