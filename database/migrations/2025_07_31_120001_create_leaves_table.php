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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Leave Details
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('duration', ['full_day', 'first_half', 'second_half'])->default('full_day');
            $table->decimal('total_days', 5, 2); // Can be fractional for half days
            $table->text('reason');
            $table->text('comments')->nullable(); // Employee additional comments
            $table->text('admin_notes')->nullable(); // HR/Admin notes
            
            // Status Management
            $table->enum('status', [
                'pending',
                'approved', 
                'rejected',
                'cancelled',
                'withdrawn'
            ])->default('pending');
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            
            // Documentation
            $table->json('attachments')->nullable(); // Medical certificates, etc.
            $table->boolean('emergency_leave')->default(false);
            $table->string('contact_number')->nullable(); // Emergency contact during leave
            $table->text('handover_notes')->nullable(); // Work handover details
            
            // System Fields
            $table->string('application_id')->unique(); // Human-readable ID like LV-2025-001
            $table->json('approval_workflow')->nullable(); // Track approval chain
            $table->boolean('affects_attendance')->default(true); // Whether it should update attendance
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['company_id', 'status']);
            $table->index(['start_date', 'end_date']);
            $table->index(['leave_type_id', 'status']);
            $table->index('applied_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};