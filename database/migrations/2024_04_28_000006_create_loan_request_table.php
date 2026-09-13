<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_request', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('group_id')->nullable()->constrained('my_group', 'group_id');
            $table->text('reason')->nullable();
            $table->decimal('amount', 8, 2)->nullable();
            $table->enum('status', ['pending', 'approved', 'declined', 'repaid'])->default('pending');
            $table->date('return_time')->nullable();
            $table->date('approve_date')->nullable();
            $table->decimal('repayment_amount', 10, 2)->default(0.00);
            $table->enum('payment_method', ['bkash', 'Rocket', 'Nagad'])->nullable();
            $table->string('transaction_id')->nullable();
            $table->dateTime('payment_time')->nullable();
            $table->dateTime('repayment_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_request');
    }
}; 