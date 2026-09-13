<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('polls_vote', function (Blueprint $table) {
            $table->id('vote_id');
            $table->foreignId('poll_id')->nullable()->constrained('polls', 'poll_id');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->enum('vote_option', ['yes', 'no']);
            $table->timestamps();

            $table->unique(['poll_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('polls_vote');
    }
}; 