<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings', function (Blueprint $table) {
            $table->id('savings_id');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('group_id')->nullable()->constrained('my_group', 'group_id');
            $table->decimal('amount', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings');
    }
}; 