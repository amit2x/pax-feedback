<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_answers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('feedback_id')->constrained('feedback')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('feedback_questions')->cascadeOnDelete();
            $table->foreignId('question_option_id')->nullable()
                ->constrained('feedback_question_options')->nullOnDelete();
            $table->text('answer_text')->nullable();
            $table->timestamps();

            $table->index('feedback_id');
            $table->index('question_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_answers');
    }
};
