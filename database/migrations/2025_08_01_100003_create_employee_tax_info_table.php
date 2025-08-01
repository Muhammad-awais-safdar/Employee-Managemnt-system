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
        Schema::create('employee_tax_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            
            // W-4 Information
            $table->enum('filing_status', ['single', 'married_jointly', 'married_separately', 'head_of_household'])->default('single');
            $table->integer('allowances')->default(0); // Number of allowances claimed
            $table->decimal('additional_withholding', 8, 2)->default(0.00); // Additional amount to withhold
            $table->boolean('exempt_from_federal')->default(false);
            $table->boolean('exempt_from_state')->default(false);
            $table->boolean('exempt_from_local')->default(false);
            
            // Tax Identification
            $table->string('ssn')->nullable(); // Social Security Number (encrypted)
            $table->string('tax_id')->nullable(); // Alternative tax ID
            $table->string('state_tax_id')->nullable(); // State tax ID if different
            
            // Address for tax purposes
            $table->text('tax_address')->nullable();
            $table->string('tax_city')->nullable();
            $table->string('tax_state')->nullable();
            $table->string('tax_zip')->nullable();
            
            // Deductions and Benefits
            $table->json('pre_tax_deductions')->nullable(); // Array of pre-tax deduction IDs and amounts
            $table->json('post_tax_deductions')->nullable(); // Array of post-tax deduction IDs and amounts
            $table->decimal('health_insurance_premium', 8, 2)->default(0.00);
            $table->decimal('retirement_contribution', 8, 2)->default(0.00);
            $table->decimal('retirement_contribution_percent', 5, 2)->default(0.00);
            
            // Tax Year Information
            $table->integer('tax_year')->default(2025);
            $table->boolean('is_active')->default(true);
            $table->timestamp('effective_date')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'is_active']);
            $table->index(['company_id', 'tax_year']);
            $table->index('effective_date');
            $table->unique(['user_id', 'tax_year'], 'unique_user_tax_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_tax_info');
    }
};