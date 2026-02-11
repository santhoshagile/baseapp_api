<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class add_menu_action_master_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parentid = DB::table('menus')
            ->select('id')
            ->where('title', '=', 'Configuration')
            ->value('id');

        DB::table('menus')->insert([
            [
                'is_header' => 0,
                'title' => 'Action Master',
                'href' => 'action_master',
                'parent_id' => $parentid,
                'seq' => 3,
                'icon' => '',
                'slug' => 'action-master',
            ]
        ]);
    }
}
