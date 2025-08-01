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
        Schema::create('tax_brackets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Tax Year 2025, Federal Brackets, etc.
            $table->integer('tax_year')->default(2025);
            $table->enum('filing_status', ['single', 'married_jointly', 'married_separately', 'head_of_household'])->default('single');
            $table->decimal('min_income', 12, 2);
            $table->decimal('max_income', 12, 2)->nullable();
            $table->decimal('tax_rate', 5, 4); // Tax rate as decimal
            $table->decimal('base_tax', 10, 2)->default(0.00); // Base tax amount for this bracket
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index(['company_id', 'tax_year', 'is_active']);
            $table->index(['filing_status', 'is_active']);
            $table->index(['min_income', 'max_income']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_brackets');
    }
};