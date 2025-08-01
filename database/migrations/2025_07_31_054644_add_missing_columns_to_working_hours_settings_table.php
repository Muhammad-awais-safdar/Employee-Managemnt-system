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
        Schema::table('working_hours_settings', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('working_hours_settings', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('company_id');
            }
            
            if (!Schema::hasColumn('working_hours_settings', 'standard_hours')) {
                $table->integer('standard_hours')->default(480)->after('is_active');
            }
            
            if (!Schema::hasColumn('working_hours_settings', 'check_in_time')) {
                $table->time('check_in_time')->default('09:00:00')->after('standard_hours');
            }
            
            if (!Schema::hasColumn('working_hours_settings', 'check_out_time')) {
                $table->time('check_out_time')->default('18:00:00')->after('check_in_time');
            }
            
            if (!Schema::hasColumn('working_hours_settings', 'early_leave_threshold')) {
                $table->integer('early_leave_threshold')->default(15)->after('late_threshold');
            }
            
            if (!Schema::hasColumn('working_hours_settings', 'weekend_overtime_rate')) {
                $table->decimal('weekend_overtime_rate', 3, 2)->default(2.00)->after('overtime_rate');
            }
            
            if (!Schema::hasColumn('working_hours_settings', 'holiday_overtime_rate')) {
                $table->decimal('holiday_overtime_rate', 3, 2)->default(2.50)->after('weekend_overtime_rate');
            }
            
            if (!Schema::hasColumn('working_hours_settings', 'core_hours_start')) {
                $table->time('core_hours_start')->nullable()->after('flexible_hours');
            }
            
            if (!Schema::hasColumn('working_hours_settings', 'core_hours_end')) {
                $table->time('core_hours_end')->nullable()->after('core_hours_start');
            }
            
            if (!Schema::hasColumn('working_hours_settings', 'max_daily_hours')) {
                $table->integer('max_daily_hours')->default(720)->after('core_hours_end');
            }
            
            if (!Schema::hasColumn('working_hours_settings', 'min_daily_hours')) {
                $table->integer('min_daily_hours')->default(240)->after('max_daily_hours');
            }
            
            if (!Schema::hasColumn('working_hours_settings', 'allowed_locations')) {
                $table->json('allowed_locations')->nullable()->after('min_daily_hours');
            }
            
            if (!Schema::hasColumn('working_hours_settings', 'grace_period')) {
                $table->integer('grace_period')->default(5)->after('early_leave_threshold');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('working_hours_settings', function (Blueprint $table) {
            $table->dropColumn([
                'is_active',
                'standard_hours',
                'check_in_time',
                'check_out_time',
                'early_leave_threshold',
                'weekend_overtime_rate',
                'holiday_overtime_rate',
                'core_hours_start',
                'core_hours_end',
                'max_daily_hours',
                'min_daily_hours',
                'allowed_locations',
                'grace_period'
            ]);
        });
    }
};