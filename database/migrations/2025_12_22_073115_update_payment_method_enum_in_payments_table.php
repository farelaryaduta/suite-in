com<?php

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
        Schema::table('payments', function (Blueprint $table) {
            // Ubah kolom method agar bisa menerima string apa saja (termasuk 'midtrans')
            // Atau tambahkan 'midtrans' ke enum jika database mendukung alter enum
            // Cara paling aman di Laravel/MySQL adalah mengubahnya jadi string biasa
            $table->string('method')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Kembalikan ke enum jika perlu (tapi hati-hati data bisa hilang jika tidak cocok)
            // $table->enum('method', ['credit_card', 'debit_card', 'bank_transfer', 'e_wallet', 'dummy'])->default('dummy')->change();
        });
    }
};
