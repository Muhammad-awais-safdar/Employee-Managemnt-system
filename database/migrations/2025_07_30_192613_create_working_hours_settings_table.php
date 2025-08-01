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
        Schema::create('working_hours_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->time('standard_check_in_time')->default('09:00:00');
            $table->time('standard_check_out_time')->default('17:00:00');
            $table->integer('standard_working_hours')->default(480); // in minutes (8 hours)
            $table->integer('break_duration')->default(60); // in minutes (1 hour)
            $table->integer('late_threshold')->default(30); // in minutes
            $table->decimal('overtime_rate', 3, 2)->default(1.50); // 1.5x multiplier
            $table->json('working_days')->nullable(); // Monday to Friday
            $table->boolean('flexible_hours')->default(false);
            $table->time('flexible_start_time')->nullable();
            $table->time('flexible_end_time')->nullable();
            $table->boolean('auto_break_deduction')->default(true);
            $table->boolean('require_break_approval')->default(false);
            $table->integer('max_break_duration')->default(120); // in minutes (2 hours)
            $table->boolean('track_location')->default(false);
            $table->decimal('office_latitude', 10, 8)->nullable();
            $table->decimal('office_longitude', 11, 8)->nullable();
            $table->integer('location_radius')->default(100); // in meters
            $table->json('holiday_dates')->nullable(); // Array of holiday dates
            $table->boolean('weekend_overtime')->default(false);
            $table->decimal('weekend_rate', 3, 2)->default(2.00); // 2x multiplier for weekends
            $table->timestamps();

            // Ensure one setting per company
            $table->unique('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('working_hours_settings');
    }
};