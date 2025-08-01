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
        Schema::create('salary_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('changed_by')->constrained('users')->onDelete('cascade');
            
            // Salary change details
            $table->decimal('old_salary', 12, 2)->nullable();
            $table->decimal('new_salary', 12, 2);
            $table->decimal('change_amount', 12, 2);
            $table->decimal('change_percentage', 8, 4)->nullable();
            
            // Change metadata
            $table->enum('change_type', ['direct_update', 'increment_request', 'bulk_update', 'initial_salary'])->default('direct_update');
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->date('effective_date');
            
            // Related records
            $table->foreignId('increment_request_id')->nullable()->constrained('salary_increment_requests')->onDelete('set null');
            $table->foreignId('payroll_record_id')->nullable()->constrained('payroll_records')->onDelete('set null');
            
            // Metadata
            $table->json('metadata')->nullable(); // For additional data like approval workflow info
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['employee_id', 'effective_date']);
            $table->index(['company_id', 'created_at']);
            $table->index('change_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_histories');
    }
};