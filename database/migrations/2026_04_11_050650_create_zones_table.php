<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_bn');
            $table->string('division');
            $table->string('division_bn');
            $table->enum('status', ['green', 'yellow', 'red'])->default('green');
            $table->integer('score')->default(0);
            $table->integer('report_count')->default(0);
            $table->integer('verified_count')->default(0);
            $table->integer('ranger_count')->default(0);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedBigInteger('controlling_ranger_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};