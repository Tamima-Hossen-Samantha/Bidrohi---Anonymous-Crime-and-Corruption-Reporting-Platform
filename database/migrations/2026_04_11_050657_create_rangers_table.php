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
    Schema::create('rangers', function (Blueprint $table) {
        $table->id();
        $table->string('witness_token')->unique();
        $table->string('district')->nullable();
        $table->string('zone_id')->nullable();
        $table->integer('total_points')->default(0);
        $table->enum('rank', ['nagorik', 'shakkhi', 'protigya', 'rokkhok', 'bidrohi'])->default('nagorik');
        $table->integer('reports_count')->default(0);
        $table->integer('verifications_count')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rangers');
    }
};
