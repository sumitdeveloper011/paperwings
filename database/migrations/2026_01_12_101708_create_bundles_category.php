<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if category already exists
        $existing = DB::table('categories')->where('slug', 'bundles')->first();

        if (!$existing) {
            // Create "Bundles" category
            DB::table('categories')->insert([
                'uuid' => Str::uuid()->toString(),
                'name' => 'Bundles',
                'slug' => 'bundles',
                'description' => 'Product bundles and special offers',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('categories')->where('slug', 'bundles')->delete();
    }
};
