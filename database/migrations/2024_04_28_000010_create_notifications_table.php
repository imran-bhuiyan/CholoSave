<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('notification_id');
            $table->foreignId('target_user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('target_group_id')->nullable()->constrained('my_group', 'group_id')->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->enum('status', ['unread', 'read'])->default('unread');
            $table->enum('type', [
                'loan_approval',
                'withdrawal',
                'join_request',
                'payment_reminder',
                'group_loan_request',
                'leave_request',
                'group_withdraw_request',
                'admin_promotion',
                'close_savings'
            ]);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
}; 