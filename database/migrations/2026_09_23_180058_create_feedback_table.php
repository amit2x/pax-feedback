<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('reference_no', 32)->unique();
            $table->uuid('submission_uuid')->unique();

            $table->foreignId('airport_id')->nullable()->constrained('feedback_airports')->nullOnDelete();
            $table->foreignId('terminal_id')->nullable()->constrained('feedback_terminals')->nullOnDelete();
            $table->foreignId('zone_id')->nullable()->constrained('feedback_zones')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('feedback_locations')->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('feedback_services')->nullOnDelete();
            $table->foreignId('qr_code_id')->nullable()->constrained('feedback_qr_codes')->nullOnDelete();

            $table->string('language_code', 5)->default('en');
            $table->enum('feedback_type', ['compliment', 'suggestion', 'complaint', 'query']);
            $table->unsignedTinyInteger('overall_rating');

            $table->foreignId('category_id')->nullable()->constrained('feedback_categories')->nullOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained('feedback_subcategories')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('feedback_departments')->nullOnDelete();

            $table->string('comment', 500)->nullable();

            $table->boolean('is_anonymous')->default(true);

            $table->string('name', 100)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->enum('preferred_contact_method', ['email', 'mobile', 'none'])->default('none');

            $table->string('flight_number', 10)->nullable();
            $table->date('travel_date')->nullable();

            $table->enum('status', [
                'submitted', 'acknowledged', 'assigned',
                'in_progress', 'resolved', 'closed',
            ])->default('submitted');

            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->string('submission_source', 32)->default('qr');

            // Phase 2 SAMANVAY fields
            $table->string('samanvay_incident_id', 64)->nullable();
            $table->string('samanvay_reference_no', 64)->nullable();
            $table->timestamp('incident_created_at')->nullable();
            $table->enum('incident_sync_status', [
                'pending', 'processing', 'analyzed', 'incident_required',
                'incident_created', 'not_required', 'failed',
            ])->nullable();
            $table->unsignedInteger('incident_sync_attempts')->default(0);
            $table->text('incident_sync_error')->nullable();
            $table->timestamp('analyzed_at')->nullable();
            $table->enum('analysis_status', ['pending', 'processing', 'analyzed', 'failed'])
                ->default('pending');

            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('reference_no');
            $table->index('submission_uuid');
            $table->index('qr_code_id');
            $table->index('location_id');
            $table->index('category_id');
            $table->index('subcategory_id');
            $table->index('department_id');
            $table->index('status');
            $table->index('priority');
            $table->index('feedback_type');
            $table->index('submitted_at');
            $table->index(['status', 'submitted_at']);
            $table->index(['location_id', 'submitted_at']);
            $table->index(['analysis_status', 'submitted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
