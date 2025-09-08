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
        Schema::table('discounts', function (Blueprint $table) {
            // Rename columns to match controller expectations
            $table->renameColumn('discount_percentage', 'percentage');
            $table->renameColumn('status', 'is_active');
            
            // Add new date columns
            $table->date('start_date')->nullable()->after('percentage');
            $table->date('end_date')->nullable()->after('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            // Reverse the changes
            $table->dropColumn(['start_date', 'end_date']);
            $table->renameColumn('percentage', 'discount_percentage');
            $table->renameColumn('is_active', 'status');
        });
    }
};
