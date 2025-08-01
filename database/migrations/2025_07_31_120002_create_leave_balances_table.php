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
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained()->onDelete('cascade');
            $table->year('year');
            
            // Balance Tracking
            $table->decimal('allocated_days', 5, 2)->default(0); // Allocated for the year
            $table->decimal('used_days', 5, 2)->default(0); // Days already used
            $table->decimal('pending_days', 5, 2)->default(0); // Days in pending applications
            $table->decimal('carried_forward', 5, 2)->default(0); // Days carried from previous year
            $table->decimal('bonus_days', 5, 2)->default(0); // Additional days given by admin
            $table->decimal('deducted_days', 5, 2)->default(0); // Days deducted by admin
            
            // Calculated fields (can be computed)
            $table->decimal('available_days', 5, 2)->storedAs('allocated_days + carried_forward + bonus_days - used_days - deducted_days');
            $table->decimal('total_entitled', 5, 2)->storedAs('allocated_days + carried_forward + bonus_days - deducted_days');
            
            // Metadata
            $table->json('transaction_log')->nullable(); // Track all balance changes
            $table->date('last_updated')->useCurrent();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Constraints and Indexes
            $table->unique(['user_id', 'leave_type_id', 'year']);
            $table->index(['company_id', 'year']);
            $table->index(['user_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
};