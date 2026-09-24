<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableName = config('otp.table_name', 'otps');

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->index();
            $table->string('code');
            $table->string('purpose')->default('email_verification')->index();
            $table->timestamp('expires_at')->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->unsignedInteger('max_attempts')->default(5);
            $table->timestamp('verified_at')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['identifier', 'purpose', 'verified_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = config('otp.table_name', 'otps');

        Schema::dropIfExists($tableName);
    }
};
