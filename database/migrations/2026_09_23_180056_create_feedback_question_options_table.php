<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_question_options', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('question_id')->constrained('feedback_questions')->cascadeOnDelete();

            $table->string('value', 100);
            $table->string('label_en', 150);
            $table->string('label_hi', 150)->nullable();
            $table->string('label_bn', 150)->nullable();

            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['question_id', 'value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_question_options');
    }
};
