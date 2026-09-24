<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback_qr_codes', function (Blueprint $table) {
            // Encrypted plaintext token — used ONLY to re-render QR downloads.
            // Lookup at scan time still uses token_hash, never this column.
            $table->text('token_encrypted')->nullable()->after('token_prefix');
        });
    }

    public function down(): void
    {
        Schema::table('feedback_qr_codes', function (Blueprint $table) {
            $table->dropColumn('token_encrypted');
        });
    }
};
