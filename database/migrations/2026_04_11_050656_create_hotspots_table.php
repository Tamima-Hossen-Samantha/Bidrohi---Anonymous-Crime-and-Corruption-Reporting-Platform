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
    Schema::create('hotspots', function (Blueprint $table) {
        $table->id();
        $table->decimal('cluster_center_lat', 10, 7);
        $table->decimal('cluster_center_lng', 10, 7);
        $table->integer('cluster_radius')->default(500);
        $table->string('crime_type');
        $table->integer('report_count');
        $table->integer('risk_score')->default(0);
        $table->timestamp('last_synced')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotspots');
    }
};
