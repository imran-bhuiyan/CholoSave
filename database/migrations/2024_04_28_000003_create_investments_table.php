<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investments', function (Blueprint $table) {
            $table->id('investment_id');
            $table->foreignId('group_id')->nullable()->constrained('my_group', 'group_id');
            $table->decimal('amount', 8, 2)->nullable();
            $table->string('investment_type')->nullable();
            $table->double('ex_profit')->nullable();
            $table->date('ex_return_date')->nullable();
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
}; 