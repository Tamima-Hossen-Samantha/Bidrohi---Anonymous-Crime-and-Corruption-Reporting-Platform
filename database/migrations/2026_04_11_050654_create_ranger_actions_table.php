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
    Schema::create('ranger_actions', function (Blueprint $table) {
        $table->id();
        $table->string('witness_token_hash');
        $table->enum('action_type', ['submit', 'verify', 'flag', 'confirmed', 'ngo_action']);
        $table->foreignId('report_id')->constrained()->cascadeOnDelete();
        $table->integer('points_earned');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ranger_actions');
    }
};
