<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class add_doc_category_lookup_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lookups')->insert([
            [
                'shortname' => 'DOCUMENT_CATEGORY',
                'longname'  => 'DOCUMENT_CATEGORY',
                'seq'       => 1,
                'parent_id' => 0,
                'icon'      => '',
                'status'    => 1,
                'slug'      => 'document-category',
            ],
        ]);

        $parentid = DB::table('lookups')
            ->select('id')
            ->where('shortname', '=', 'DOCUMENT_CATEGORY')
            ->value('id');

        DB::table('lookups')->insert([
            [
                'shortname' => 'Training Document',
                'longname'  => 'Training Document',
                'seq'       => 1,
                'parent_id' => $parentid,
                'icon'      => '',
                'status'    => 1,
                'slug'      => 'training-document',
            ],
            [
                'shortname' => 'Lenalidomide',
                'longname'  => 'Lenalidomide',
                'seq'       => 2,
                'parent_id' => $parentid,
                'icon'      => '',
                'status'    => 1,
                'slug'      => 'lenalidomide',
            ],
            [
                'shortname' => '50mg-ThalidomideDrugs',
                'longname'  => '50mg-Thalidomide',
                'seq'       => 3,
                'parent_id' => $parentid,
                'icon'      => '',
                'status'    => 1,
                'slug'      => '50mg-thalidomide',
            ],
            [
                'shortname' => 'Pomalidomide',
                'longname'  => 'Pomalidomide',
                'seq'       => 4,
                'parent_id' => $parentid,
                'icon'      => '',
                'status'    => 1,
                'slug'      => 'pomalidomide',
            ],
        ]);
    }
}
