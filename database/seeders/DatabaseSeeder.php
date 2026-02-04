<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(add_superuser_seeder::class);
        $this->call(add_menu_dashboard::class);
        $this->call(add_role_seeder::class);
        $this->call(superuser_menu_seeder::class);
        $this->call(LookupTemplateTypeSeeder::class);
        $this->call(add_email_templates_seeder::class);
    }
}
