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
    Schema::create('report_verifications', function (Blueprint $table) {
        $table->id();
        $table->foreignId('report_id')->constrained()->cascadeOnDelete();
        $table->string('witness_token_hash');
        $table->enum('action', ['verify', 'flag']);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_verifications');
    }
};
