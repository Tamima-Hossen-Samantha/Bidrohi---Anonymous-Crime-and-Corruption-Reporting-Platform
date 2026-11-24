<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Column already added in main reports migration — skip safely
        if (Schema::hasTable('reports') && !Schema::hasColumn('reports', 'nearest_thana')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->string('nearest_thana')->nullable()->after('district');
            });
        }
    }

    public function down(): void
    {
        // Do nothing — managed by main migration
    }
};