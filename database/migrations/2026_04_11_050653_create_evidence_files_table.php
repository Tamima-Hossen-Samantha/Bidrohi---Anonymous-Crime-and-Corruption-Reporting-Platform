<?php

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
    Schema::create('evidence_files', function (Blueprint $table) {
        $table->id();
        $table->foreignId('report_id')->constrained()->cascadeOnDelete();
        $table->string('vault_id')->unique();
        $table->string('file_path');
        $table->enum('file_type', ['image', 'video', 'audio']);
        $table->string('timestamp_hash');
        $table->string('gps_metadata')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidence_files');
    }
};
