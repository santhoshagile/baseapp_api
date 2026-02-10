<?php

namespace Database\Seeders;

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
        $this->call(lookup_template_type_seeder::class);
        $this->call(add_email_templates_seeder::class);
        $this->call(add_location_seeder::class);
        $this->call(add_login_otp_verification_email_template_seeder::class);
        $this->call(add_system_parameter_basic_data::class);
    }
}
