<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A transaction id must map to at most one credited payment, so a
     * repeated or concurrent OTP verification cannot credit savings twice.
     */
    public function up(): void
    {
        Schema::table('transaction_info', function (Blueprint $table) {
            $table->unique('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('transaction_info', function (Blueprint $table) {
            $table->dropUnique(['transaction_id']);
        });
    }
};
