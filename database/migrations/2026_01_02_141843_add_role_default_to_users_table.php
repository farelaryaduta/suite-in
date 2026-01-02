<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Fix Bug #6: Add default value for role column to prevent NULL roles
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Change role column to have 'customer' as default value
            $table->string('role')->default('customer')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert to nullable without default
            $table->string('role')->nullable()->default(null)->change();
        });
    }
};
