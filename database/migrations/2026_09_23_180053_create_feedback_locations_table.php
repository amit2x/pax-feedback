<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_locations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('airport_id')->constrained('feedback_airports')->cascadeOnDelete();
            $table->foreignId('terminal_id')->nullable()->constrained('feedback_terminals')->nullOnDelete();
            $table->foreignId('zone_id')->nullable()->constrained('feedback_zones')->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('feedback_services')->nullOnDelete();

            $table->string('code', 30)->unique();
            $table->string('name', 120);
            $table->string('description', 255)->nullable();
            $table->string('checkpoint_label', 60)->nullable();

            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['airport_id', 'terminal_id', 'zone_id']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_locations');
    }
};
