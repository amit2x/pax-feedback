<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_subcategories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('category_id')->constrained('feedback_categories')->cascadeOnDelete();

            $table->string('code', 60);
            $table->string('name_en', 120);
            $table->string('name_hi', 120)->nullable();
            $table->string('name_bn', 120)->nullable();

            $table->foreignId('default_department_id')->nullable()
                ->constrained('feedback_departments')->nullOnDelete();

            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['category_id', 'code']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_subcategories');
    }
};
