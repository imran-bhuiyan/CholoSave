<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawal', function (Blueprint $table) {
            $table->id('withdrawal_id');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('group_id')->nullable()->constrained('my_group', 'group_id');
            $table->decimal('amount', 8, 2)->nullable();
            $table->string('payment_number');
            $table->string('payment_method');
            $table->enum('status', ['pending', 'approved', 'declined'])->default('pending');
            $table->date('approve_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawal');
    }
}; 