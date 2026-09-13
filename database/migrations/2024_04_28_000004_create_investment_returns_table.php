<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investment_returns', function (Blueprint $table) {
            $table->id('return_id');
            $table->foreignId('investment_id')->nullable()->constrained('investments', 'investment_id');
            $table->decimal('amount', 8, 2)->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_returns');
    }
}; 