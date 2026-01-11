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
        Schema::table('regions', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        // Generate UUIDs for existing records
        $regions = \App\Models\Region::whereNull('uuid')->get();
        foreach ($regions as $region) {
            $region->uuid = \Illuminate\Support\Str::uuid();
            $region->save();
        }

        // Make UUID not nullable and add index
        Schema::table('regions', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
            $table->index('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->dropIndex(['uuid']);
            $table->dropColumn('uuid');
        });
    }
};
