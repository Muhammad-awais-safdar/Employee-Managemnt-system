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
        Schema::table('payroll_records', function (Blueprint $table) {
            // Federal Tax
            $table->decimal('federal_tax', 10, 2)->default(0.00)->after('tax_deduction');
            $table->decimal('federal_tax_rate', 5, 4)->default(0.0000)->after('federal_tax');
            
            // State Tax
            $table->decimal('state_tax', 10, 2)->default(0.00)->after('federal_tax_rate');
            $table->decimal('state_tax_rate', 5, 4)->default(0.0000)->after('state_tax');
            
            // Local Tax
            $table->decimal('local_tax', 10, 2)->default(0.00)->after('state_tax_rate');
            $table->decimal('local_tax_rate', 5, 4)->default(0.0000)->after('local_tax');
            
            // Social Security
            $table->decimal('social_security_tax', 10, 2)->default(0.00)->after('local_tax_rate');
            $table->decimal('social_security_rate', 5, 4)->default(0.0620)->after('social_security_tax'); // 6.2%
            
            // Medicare
            $table->decimal('medicare_tax', 10, 2)->default(0.00)->after('social_security_rate');
            $table->decimal('medicare_rate', 5, 4)->default(0.0145)->after('medicare_tax'); // 1.45%
            
            // Unemployment (SUTA/FUTA)
            $table->decimal('unemployment_tax', 10, 2)->default(0.00)->after('medicare_rate');
            $table->decimal('unemployment_rate', 5, 4)->default(0.0000)->after('unemployment_tax');
            
            // Pre-tax and Post-tax deductions
            $table->decimal('pre_tax_deductions', 10, 2)->default(0.00)->after('unemployment_rate');
            $table->decimal('post_tax_deductions', 10, 2)->default(0.00)->after('pre_tax_deductions');
            
            // Employer contributions
            $table->decimal('employer_social_security', 10, 2)->default(0.00)->after('post_tax_deductions');
            $table->decimal('employer_medicare', 10, 2)->default(0.00)->after('employer_social_security');
            $table->decimal('employer_unemployment', 10, 2)->default(0.00)->after('employer_medicare');
            $table->decimal('employer_benefits', 10, 2)->default(0.00)->after('employer_unemployment');
            
            // Tax calculation details
            $table->json('tax_calculation_details')->nullable()->after('employer_benefits');
            
            // Update the tax_deduction column to be total_tax_withholding for clarity
            $table->renameColumn('tax_deduction', 'total_tax_withholding');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_records', function (Blueprint $table) {
            $table->dropColumn([
                'federal_tax',
                'federal_tax_rate',
                'state_tax',
                'state_tax_rate',
                'local_tax',
                'local_tax_rate',
                'social_security_tax',
                'social_security_rate',
                'medicare_tax',
                'medicare_rate',
                'unemployment_tax',
                'unemployment_rate',
                'pre_tax_deductions',
                'post_tax_deductions',
                'employer_social_security',
                'employer_medicare',
                'employer_unemployment',
                'employer_benefits',
                'tax_calculation_details'
            ]);
            
            $table->renameColumn('total_tax_withholding', 'tax_deduction');
        });
    }
};