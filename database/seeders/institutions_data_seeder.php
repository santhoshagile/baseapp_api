<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class institutions_data_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('institutions')->insert([
            [
                'name' => 'St Thomas’ Hospital',
                'type' => 'NHS Teaching Hospitals',
                'address' => 'Westminster Bridge Rd, London SE1 7EH, United Kingdom',
                'slug' => 'st-thomas-hospital',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'Guy’s Hospital',
                'type' => 'NHS Teaching Hospital',
                'address' => 'Great Maze Pond, London SE1 9RT, United Kingdoms',
                'slug' => 'guys-hospital',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'Royal London Hospital',
                'type' => 'NHS Acute Hospital',
                'address' => 'Whitechapel Rd, London E1 1FR, United Kingdom',
                'slug' => 'royal-london-hospital',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'Addenbrooke’s Hospital',
                'type' => 'NHS Teaching Hospital',
                'address' => 'Hills Rd, Cambridge CB2 0QQ, United Kingdom',
                'slug' => 'addenbrookes-hospital',
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ]);
    }
}
