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
    Schema::create('ngo_alerts', function (Blueprint $table) {
        $table->id();
        $table->string('location_name');
        $table->decimal('latitude', 10, 7);
        $table->decimal('longitude', 10, 7);
        $table->integer('abuse_count');
        $table->string('alert_type')->default('child_abuse');
        $table->string('sent_to_email');
        $table->enum('status', ['sent', 'pending', 'failed'])->default('pending');
        $table->timestamp('sent_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngo_alerts');
    }
};
