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
            $table->uuid('uuid')->unique()->after('id');
            $table->dropColumn('name');
            $table->string('first_name')->nullable()->after('uuid');
            $table->string('last_name')->nullable()->after('first_name');
            $table->tinyInteger('status')->default(1)->after('remember_token')->comment('1: Active, 0: Inactive');
            $table->tinyInteger('agree_terms')->default(0)->after('status')->comment('1: Agree, 0: Disagree');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'first_name', 'last_name', 'status']);
            $table->string('name')->nullable()->after('id');
        });
    }
};
