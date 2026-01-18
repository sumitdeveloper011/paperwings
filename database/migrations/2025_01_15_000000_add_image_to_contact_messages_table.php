<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('contact_messages')) {
            Schema::table('contact_messages', function (Blueprint $table) {
                if (!Schema::hasColumn('contact_messages', 'image')) {
                    $table->string('image')->nullable()->after('message');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('contact_messages')) {
            Schema::table('contact_messages', function (Blueprint $table) {
                if (Schema::hasColumn('contact_messages', 'image')) {
                    $table->dropColumn('image');
                }
            });
        }
    }
};
