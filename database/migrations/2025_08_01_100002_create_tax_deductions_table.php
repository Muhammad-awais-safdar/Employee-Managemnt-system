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
        Schema::create('tax_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Standard Deduction, Health Insurance, etc.
            $table->enum('type', ['standard', 'itemized', 'pre_tax', 'post_tax'])->default('pre_tax');
            $table->enum('calculation_method', ['percentage', 'fixed', 'formula'])->default('fixed');
            $table->decimal('amount', 10, 2)->default(0.00); // Fixed amount or percentage
            $table->decimal('max_amount', 10, 2)->nullable(); // Maximum deduction amount
            $table->decimal('min_amount', 10, 2)->default(0.00); // Minimum deduction amount
            $table->boolean('is_taxable')->default(false); // Whether this deduction is taxable
            $table->boolean('applies_to_federal')->default(true);
            $table->boolean('applies_to_state')->default(true);
            $table->boolean('applies_to_local')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->json('eligibility_criteria')->nullable(); // JSON for complex eligibility rules
            $table->timestamps();
            
            // Indexes
            $table->index(['company_id', 'is_active']);
            $table->index(['type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_deductions');
    }
};