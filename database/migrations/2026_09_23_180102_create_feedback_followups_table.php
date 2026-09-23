<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feedback_id')->constrained('feedback')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('channel', ['call', 'email', 'sms', 'in_person', 'other'])->default('call');
            $table->text('notes')->nullable();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamps();

            $table->index('feedback_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_followups');
    }
};
