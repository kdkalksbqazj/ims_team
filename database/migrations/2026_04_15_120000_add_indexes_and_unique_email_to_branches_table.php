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
        Schema::table('branches', function (Blueprint $table) {
            $table->index('status', 'branches_status_idx');
            $table->index(['name', 'location'], 'branches_name_location_idx');
            $table->unique('email', 'branches_email_unique_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropUnique('branches_email_unique_idx');
            $table->dropIndex('branches_name_location_idx');
            $table->dropIndex('branches_status_idx');
        });
    }
};