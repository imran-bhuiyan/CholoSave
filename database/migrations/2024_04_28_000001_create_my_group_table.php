<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('my_group', function (Blueprint $table) {
            $table->id('group_id');
            $table->string('group_name')->nullable();
            $table->integer('members')->default(0);
            $table->foreignId('group_admin_id')->nullable()->constrained('users');
            $table->enum('dps_type', ['weekly', 'monthly'])->nullable();
            $table->integer('time_period')->nullable();
            $table->decimal('amount', 8, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->integer('goal_amount')->nullable();
            $table->integer('warning_time')->nullable();
            $table->decimal('emergency_fund', 8, 2)->nullable();
            $table->string('bKash')->nullable();
            $table->string('Rocket')->nullable();
            $table->string('Nagad')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('my_group');
    }
}; 