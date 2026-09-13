<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('group_id')->constrained('my_group', 'group_id');
            $table->decimal('amount', 8, 2);
            $table->string('transaction_id');
            $table->enum('payment_method', ['bKash', 'Rocket', 'Nagad']);
            $table->timestamp('payment_time')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_info');
    }
}; 