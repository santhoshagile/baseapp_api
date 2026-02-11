<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class add_salutation_lookup_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lookups')->insert([
            [
                'shortname' => 'SALUTATION',
                'longname' => 'SALUTATION',
                'seq' => 1,
                'parent_id' => 0,
                'icon' => '',
                'status' => 1,
                'slug' => 'salutation',
            ],
            [
                'shortname' => 'GENDER',
                'longname' => 'GENDER',
                'seq' => 1,
                'parent_id' => 0,
                'icon' => '',
                'status' => 1,
                'slug' => 'gender',
            ],
        ]);
        // Salutations
        $parent = DB::table('lookups')
            ->select('id')
            ->where('shortname', '=', 'SALUTATION')
            ->pluck('id');
        $quotes = ['[', ']'];
        $parentid = str_replace($quotes, '', $parent);

        DB::table('lookups')->insert([
            [
                'shortname' => 'Mr',
                'longname' => 'Mr',
                'seq' => 1,
                'parent_id' => $parentid,
                'icon' => '',
                'status' => 1,
                'slug' => 'mr',
            ],
            [
                'shortname' => 'Ms',
                'longname' => 'Ms',
                'seq' => 2,
                'parent_id' => $parentid,
                'icon' => '',
                'status' => 1,
                'slug' => 'ms',
            ],
            [
                'shortname' => 'Mrs',
                'longname' => 'Mrs',
                'seq' => 3,
                'parent_id' => $parentid,
                'icon' => '',
                'status' => 1,
                'slug' => 'mrs',
            ],
            [
                'shortname' => 'Miss',
                'longname' => 'Miss',
                'seq' => 4,
                'parent_id' => $parentid,
                'icon' => '',
                'status' => 1,
                'slug' => 'miss',
            ],
            [
                'shortname' => 'Dr',
                'longname' => 'Dr',
                'seq' => 5,
                'parent_id' => $parentid,
                'icon' => '',
                'status' => 1,
                'slug' => 'dr',
            ],
        ]);

        // Gender
        $g_parent = DB::table('lookups')
            ->select('id')
            ->where('shortname', '=', 'GENDER')
            ->pluck('id');
        $g_quotes = ['[', ']'];
        $g_parentid = str_replace($g_quotes, '', $g_parent);

        DB::table('lookups')->insert([
            [
                'shortname' => 'Male',
                'longname' => 'Male',
                'seq' => 1,
                'parent_id' => $g_parentid,
                'icon' => '',
                'status' => 1,
                'slug' => 'male',
            ],
            [
                'shortname' => 'Female',
                'longname' => 'Female',
                'seq' => 2,
                'parent_id' => $g_parentid,
                'icon' => '',
                'status' => 1,
                'slug' => 'female',
            ],
            [
                'shortname' => 'Others',
                'longname' => 'Others',
                'seq' => 3,
                'parent_id' => $g_parentid,
                'icon' => '',
                'status' => 1,
                'slug' => 'others',
            ],
        ]);
    }
}
