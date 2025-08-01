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
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Sick Leave, Casual Leave, Annual Leave, etc.
            $table->string('code')->unique(); // SL, CL, AL, etc.
            $table->text('description')->nullable();
            $table->integer('max_days_per_year')->default(0); // Maximum days allowed per year
            $table->integer('carry_forward_limit')->default(0); // How many days can be carried forward
            $table->boolean('requires_medical_certificate')->default(false);
            $table->integer('min_notice_days')->default(0); // Minimum days notice required
            $table->integer('max_consecutive_days')->default(0); // Max consecutive days allowed (0 = unlimited)
            $table->boolean('is_paid')->default(true);
            $table->boolean('weekend_included')->default(true); // Whether weekends count as leave days
            $table->boolean('holiday_included')->default(false); // Whether holidays count as leave days
            $table->json('applicable_roles')->nullable(); // Which roles can use this leave type
            $table->decimal('deduction_rate', 5, 2)->default(1.00); // 1.00 = full day, 0.5 = half day
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index(['company_id', 'is_active']);
            $table->unique(['company_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};