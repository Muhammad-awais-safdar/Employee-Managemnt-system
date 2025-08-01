<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add the new 'on_leave' status to the enum
        DB::statement("ALTER TABLE attendances MODIFY COLUMN status ENUM('present', 'absent', 'half_day', 'late', 'early_leave', 'unpaid_leave', 'without_notice', 'on_leave') DEFAULT 'absent'");
        
        // Add leave-related columns to attendances table
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('leave_id')->nullable()->constrained('leaves')->onDelete('set null')->after('company_id');
            $table->string('leave_type_name')->nullable()->after('leave_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['leave_id']);
            $table->dropColumn(['leave_id', 'leave_type_name']);
        });
        
        // Remove the 'on_leave' status from the enum
        DB::statement("ALTER TABLE attendances MODIFY COLUMN status ENUM('present', 'absent', 'half_day', 'late', 'early_leave', 'unpaid_leave', 'without_notice') DEFAULT 'absent'");
    }
};