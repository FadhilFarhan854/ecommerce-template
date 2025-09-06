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
        Schema::table('banners', function (Blueprint $table) {
            // Drop existing columns and recreate table with correct structure
            if (Schema::hasColumn('banners', 'title')) {
                $table->dropColumn('title');
            }
            if (Schema::hasColumn('banners', 'active')) {
                $table->dropColumn('active');
            }
            if (!Schema::hasColumn('banners', 'status')) {
                $table->boolean('status')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            if (Schema::hasColumn('banners', 'status')) {
                $table->dropColumn('status');
            }
            $table->string('title')->nullable();
            $table->boolean('active')->default(true);
        });
    }
};
