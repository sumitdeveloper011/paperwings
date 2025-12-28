<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->nullable()->after('id');
        });

        // Generate UUIDs for existing records
        $tags = DB::table('tags')->whereNull('uuid')->get();
        foreach ($tags as $tag) {
            DB::table('tags')
                ->where('id', $tag->id)
                ->update(['uuid' => Str::uuid()->toString()]);
        }

        // Make UUID not nullable after populating
        Schema::table('tags', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
