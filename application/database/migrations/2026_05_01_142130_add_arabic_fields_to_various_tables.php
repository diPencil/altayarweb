<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->after('name');
            $table->string('location_ar')->nullable()->after('location');
        });

        Schema::table('listings', function (Blueprint $table) {
            $table->string('title_ar')->nullable()->after('title');
            $table->text('summary_ar')->nullable()->after('summary');
            $table->longText('description_ar')->nullable()->after('description');
        });

        Schema::table('listing_types', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->after('name');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['name_ar', 'location_ar']);
        });

        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['title_ar', 'summary_ar', 'description_ar']);
        });

        Schema::table('listing_types', function (Blueprint $table) {
            $table->dropColumn('name_ar');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('name_ar');
        });
    }
};
