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
    Schema::create('reports', function (Blueprint $table) {
        $table->id();
        $table->string('tracking_token')->nullable()->index();
        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('zone_id')->nullable()->constrained()->nullOnDelete();
        $table->string('category');
        $table->string('title')->nullable();
        $table->text('description');
        $table->string('district');
        $table->string('upazila')->nullable();
        $table->string('nearest_thana')->nullable();
        $table->decimal('latitude', 10, 7)->nullable();
        $table->decimal('longitude', 10, 7)->nullable();
        $table->enum('status', ['pending', 'verified', 'rejected', 'escalated'])->default('pending');
        $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('low');
        $table->boolean('is_anonymous')->default(true);
        $table->boolean('ai_processed')->default(false);
        $table->json('ai_tags')->nullable();
        $table->string('ai_cluster_id')->nullable();
        $table->boolean('ngo_alerted')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
