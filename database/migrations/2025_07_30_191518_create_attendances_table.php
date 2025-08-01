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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->integer('total_hours')->default(0); // in minutes
            $table->integer('break_duration')->default(0); // in minutes
            $table->enum('status', [
                'present', 
                'absent', 
                'half_day', 
                'late', 
                'early_leave',
                'unpaid_leave',
                'without_notice'
            ])->default('absent');
            $table->enum('leave_type', [
                'full_day',
                'half_day', 
                'unpaid_leave',
                'without_notice',
                'overtime'
            ])->nullable();
            $table->decimal('overtime_hours', 5, 2)->default(0.00); // for 1.5x calculation
            $table->decimal('overtime_rate', 5, 2)->default(1.50); // multiplier for overtime
            $table->text('notes')->nullable();
            $table->boolean('is_holiday')->default(false);
            $table->boolean('is_weekend')->default(false);
            $table->json('break_times')->nullable(); // store multiple breaks
            $table->string('ip_address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable(); // for geo-location tracking
            $table->decimal('longitude', 11, 8)->nullable(); // for geo-location tracking
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'date']);
            $table->index(['company_id', 'date']);
            $table->index(['date', 'status']);
            $table->unique(['user_id', 'date']); // one record per user per day
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};