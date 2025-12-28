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
        Schema::table('product_questions', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->nullable()->after('id');
        });

        // Generate UUIDs for existing records
        $questions = DB::table('product_questions')->whereNull('uuid')->get();
        foreach ($questions as $question) {
            DB::table('product_questions')
                ->where('id', $question->id)
                ->update(['uuid' => Str::uuid()->toString()]);
        }

        // Make UUID not nullable after populating
        Schema::table('product_questions', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_questions', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
