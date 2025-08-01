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
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Federal Income Tax, State Tax, etc.
            $table->string('type')->default('income'); // income, social_security, medicare, etc.
            $table->decimal('rate', 5, 4)->default(0.0000); // Tax rate as decimal (e.g., 0.1000 for 10%)
            $table->decimal('min_income', 12, 2)->default(0.00); // Minimum income for this rate
            $table->decimal('max_income', 12, 2)->nullable(); // Maximum income for this rate (null = no limit)
            $table->decimal('fixed_amount', 10, 2)->default(0.00); // Fixed tax amount
            $table->enum('calculation_method', ['percentage', 'fixed', 'bracket'])->default('percentage');
            $table->boolean('is_employer_contribution')->default(false); // For employer-paid taxes
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['company_id', 'is_active']);
            $table->index(['type', 'is_active']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
    }
};