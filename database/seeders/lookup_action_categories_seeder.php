<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class lookup_action_categories_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lookups')->insert([
            [
                'shortname' => 'ACTION_CATEGORIES',
                'longname' => 'ACTION_CATEGORIES',
                'seq' => 1,
                'parent_id' => 0,
                'icon' => '',
                'status' => 1,
                'slug' => 'action-categories',
            ],
        ]);

        $parentid = DB::table('lookups')
            ->select('id')
            ->where('shortname', '=', 'ACTION_CATEGORIES')
            ->value('id');

        DB::table('lookups')->insert([
            [
                'shortname' => 'Others',
                'longname' => 'Others',
                'seq' => 1,
                'parent_id' => $parentid,
                'icon' => '',
                'status' => 1,
                'slug' => 'others',
            ],
            [
                'shortname' => 'Institution',
                'longname' => 'Institution',
                'seq' => 2,
                'parent_id' => $parentid,
                'icon' => '',
                'status' => 1,
                'slug' => 'institution',
            ],
            [
                'shortname' => 'Drugs',
                'longname' => 'Drugs',
                'seq' => 3,
                'parent_id' => $parentid,
                'icon' => '',
                'status' => 1,
                'slug' => 'drugs',
            ],
            [
                'shortname' => 'Patient',
                'longname' => 'Patient',
                'seq' => 4,
                'parent_id' => $parentid,
                'icon' => '',
                'status' => 1,
                'slug' => 'patient',
            ],
        ]);
    }
}
