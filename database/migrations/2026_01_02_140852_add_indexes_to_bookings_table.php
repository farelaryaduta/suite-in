<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Fix #11: Add database indexes for better query performance
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add composite index for date range queries (availability checks)
            $table->index(['check_in', 'check_out'], 'bookings_check_in_check_out_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_check_in_check_out_index');
        });
    }
};
