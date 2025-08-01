<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\TaxRate;
use App\Models\TaxBracket;
use App\Models\TaxDeduction;
use App\Models\EmployeeTaxInfo;
use App\Policies\AttendancePolicy;
use App\Policies\LeavePolicy;
use App\Policies\LeaveTypePolicy;
use App\Policies\TaxRatePolicy;
use App\Policies\TaxBracketPolicy;
use App\Policies\TaxDeductionPolicy;
use App\Policies\EmployeeTaxInfoPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Policies
        Gate::policy(Attendance::class, AttendancePolicy::class);
        Gate::policy(Leave::class, LeavePolicy::class);
        Gate::policy(LeaveType::class, LeaveTypePolicy::class);
        Gate::policy(TaxRate::class, TaxRatePolicy::class);
        Gate::policy(TaxBracket::class, TaxBracketPolicy::class);
        Gate::policy(TaxDeduction::class, TaxDeductionPolicy::class);
        Gate::policy(EmployeeTaxInfo::class, EmployeeTaxInfoPolicy::class);
        
        // Register Blade directives for company logo
        Blade::directive('companyLogo', function ($expression) {
            return "<?php 
                \$user = Auth::user();
                \$logoSize = $expression ?: '45px';
                \$companyLogo = null;
                \$companyName = 'Employee Management System';
                
                if (\$user && \$user->company && \$user->company->logo) {
                    \$companyLogo = asset('storage/' . \$user->company->logo);
                    \$companyName = \$user->company->name;
                }
                
                echo '<img src=\"' . (\$companyLogo ?: asset('assets/img/logo.svg')) . '\" alt=\"' . \$companyName . ' Logo\" style=\"max-height: ' . \$logoSize . '; width: auto;\">';
            ?>";
        });
    }
}
