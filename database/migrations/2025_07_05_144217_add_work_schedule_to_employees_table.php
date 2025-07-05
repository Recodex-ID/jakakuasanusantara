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
        Schema::table('employees', function (Blueprint $table) {
            $table->time('work_start_time')->default('08:00')->after('status');
            $table->time('work_end_time')->default('17:00')->after('work_start_time');
            $table->integer('late_tolerance_minutes')->default(15)->after('work_end_time');
            $table->json('work_days')->nullable()->after('late_tolerance_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'work_start_time',
                'work_end_time',
                'late_tolerance_minutes',
                'work_days'
            ]);
        });
    }
};
