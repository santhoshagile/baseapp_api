<?php
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Created By Stalvin
     * Using from 10-02-2026
     * Global database seeder runner
     * @return void
     */

    public function run(): void
    {
        $this->runSeeder(add_superuser_seeder::class);
        $this->runSeeder(add_menu_dashboard::class);
        $this->runSeeder(add_role_seeder::class);
        $this->runSeeder(superuser_menu_seeder::class);
        $this->runSeeder(add_basic_lookups_seeder::class);
        $this->runSeeder(add_email_templates_seeder::class);
        $this->runSeeder(add_location_seeder::class);
        $this->runSeeder(add_system_parameter_basic_data::class);
        $this->runSeeder(institutions_data_seeder::class);

    }

    /**
     * Run the given seeder only if it has not been executed before.
     * @param  string  $seederClass
     * @return void
     */

    protected function runSeeder(string $seederClass)
    {
        $className       = class_basename($seederClass);
        $alreadyExecuted = DB::table('database_seeder')
            ->where('seeder', $className)
            ->where('status', 1)
            ->exists();
        if ($alreadyExecuted) {
            return;
        } else {
            $this->call($seederClass);
            DB::table('database_seeder')->insert([
                'seeder' => $className,
                'status' => 1,
            ]);
        }
    }
}
