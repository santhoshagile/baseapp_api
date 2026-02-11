<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('lastname', 100)->comment('User last name')->nullable()->after('name');
            $table->date('dob')->comment('Date of Birth')->nullable()->nullable()->after('gender');
            $table->string('address', 250)->comment('User address')->nullable()->after('dob');
            $table->string('postcode', 100)->comment('Area Postcode')->nullable()->after('address');
            $table->longText('description', 500)->comment('Description if any for the user')->nullable()->after('postcode');
            $table->string('image_url', 200)->comment('Image Url')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['lastname', 'dob', 'address', 'postcode', 'description', 'image_url']);
        });
    }
};
