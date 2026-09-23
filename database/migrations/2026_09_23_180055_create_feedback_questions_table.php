<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_questions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('category_id')->nullable()
                ->constrained('feedback_categories')->nullOnDelete();
            $table->foreignId('subcategory_id')->nullable()
                ->constrained('feedback_subcategories')->nullOnDelete();

            $table->enum('type', ['radio', 'checkbox', 'text', 'rating', 'select'])->default('radio');

            $table->string('question_en', 255);
            $table->string('question_hi', 255)->nullable();
            $table->string('question_bn', 255)->nullable();

            $table->boolean('is_required')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'subcategory_id']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_questions');
    }
};
