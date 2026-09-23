<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_attachments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('feedback_id')->constrained('feedback')->cascadeOnDelete();

            $table->enum('type', ['voice', 'photo']);
            $table->string('original_name', 255)->nullable();
            $table->string('stored_name', 255);
            $table->string('mime_type', 100);
            $table->string('extension', 10);
            $table->unsignedBigInteger('size');
            $table->string('storage_disk', 32)->default('local');
            $table->string('storage_path', 500);
            $table->string('checksum', 64)->nullable();
            $table->unsignedSmallInteger('duration_seconds')->nullable();

            $table->enum('scan_status', ['pending', 'clean', 'infected', 'failed'])->default('pending');
            $table->enum('processing_status', ['pending', 'processing', 'completed', 'failed'])
                ->default('pending');

            $table->timestamps();

            $table->index('feedback_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_attachments');
    }
};
