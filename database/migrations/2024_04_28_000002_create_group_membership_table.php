<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_membership', function (Blueprint $table) {
            $table->id('membership_id');
            $table->foreignId('group_id')->nullable()->constrained('my_group', 'group_id');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->enum('status', ['pending', 'approved', 'declined'])->default('pending');
            $table->boolean('is_admin')->default(false);
            $table->enum('leave_request', ['pending', 'approved', 'declined', 'no'])->default('no');
            $table->date('join_date')->nullable();
            $table->date('join_request_date')->nullable();
            $table->integer('time_period_remaining')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_membership');
    }
}; 