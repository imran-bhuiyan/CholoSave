<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_otps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('group_id')->constrained('my_group', 'group_id');
            $table->string('otp', 6);
            $table->dateTime('otp_expiry');
            $table->string('transaction_id', 50);
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_otps');
    }
}; 