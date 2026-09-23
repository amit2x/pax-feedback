<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_qr_codes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->string('token_hash', 64)->unique();
            $table->string('token_prefix', 8);

            $table->enum('type', ['location', 'generic']);
            $table->string('name', 120);
            $table->text('description')->nullable();

            $table->foreignId('airport_id')->nullable()->constrained('feedback_airports')->nullOnDelete();
            $table->foreignId('terminal_id')->nullable()->constrained('feedback_terminals')->nullOnDelete();
            $table->foreignId('zone_id')->nullable()->constrained('feedback_zones')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('feedback_locations')->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('feedback_services')->nullOnDelete();

            $table->enum('status', ['active', 'inactive', 'expired', 'compromised'])->default('active');

            $table->unsignedBigInteger('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index('token_prefix');
            $table->index('status');
            $table->index(['type', 'status']);
            $table->index('location_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_qr_codes');
    }
};
