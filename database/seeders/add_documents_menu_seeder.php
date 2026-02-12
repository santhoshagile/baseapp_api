<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class add_documents_menu_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locale = DB::table('menus')
            ->select('id')
            ->where('title', '=', 'Configuration')
            ->pluck('id');
        $quotes = ['[', ']'];
        $parentid = str_replace($quotes, '', $locale);

        DB::table('menus')->insert([
            [
                'is_header' => 0,
                'title' => 'Documents',
                'href' => 'documents',
                'parent_id' => $parentid,
                'seq' => 5,
                'icon' => '',
                'slug' => 'documents',
            ],
        ]);
    }
}
