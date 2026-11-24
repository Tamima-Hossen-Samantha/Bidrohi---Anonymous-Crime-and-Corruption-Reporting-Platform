<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only add the column if the table exists AND the column doesn't already exist
        if (Schema::hasTable('evidence_files') && !Schema::hasColumn('evidence_files', 'sha256_hash')) {
            Schema::table('evidence_files', function (Blueprint $table) {
                $table->string('sha256_hash', 64)->nullable()->after('checksum');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('evidence_files') && Schema::hasColumn('evidence_files', 'sha256_hash')) {
            Schema::table('evidence_files', function (Blueprint $table) {
                $table->dropColumn('sha256_hash');
            });
        }
    }
};