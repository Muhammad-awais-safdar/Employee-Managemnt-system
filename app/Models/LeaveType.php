<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'description',
        'max_days_per_year',
        'carry_forward_limit',
        'requires_medical_certificate',
        'min_notice_days',
        'max_consecutive_days',
        'is_paid',
        'weekend_included',
        'holiday_included',
        'applicable_roles',
        'deduction_rate',
        'is_active'
    ];

    protected $casts = [
        'requires_medical_certificate' => 'boolean',
        'is_paid' => 'boolean',
        'weekend_included' => 'boolean',
        'holiday_included' => 'boolean',
        'is_active' => 'boolean',
        'applicable_roles' => 'array',
        'deduction_rate' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company that owns the leave type.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the leaves for this type.
     */
    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    /**
     * Get the leave balances for this type.
     */
    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class);
    }

    /**
     * Scope for active leave types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for company-specific leave types.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope for role-specific leave types.
     */
    public function scopeForRole($query, $role)
    {
        return $query->where(function($q) use ($role) {
            $q->whereNull('applicable_roles')
              ->orWhereJsonContains('applicable_roles', $role);
        });
    }

    /**
     * Check if this leave type is available for a specific role.
     */
    public function isAvailableForRole($role): bool
    {
        if (!$this->applicable_roles) {
            return true; // Available for all roles if not specified
        }
        
        return in_array($role, $this->applicable_roles);
    }

    /**
     * Get the status badge class for UI.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return $this->is_active ? 'bg-success' : 'bg-danger';
    }

    /**
     * Get the status label for display.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    /**
     * Get the formatted deduction rate.
     */
    public function getFormattedDeductionRateAttribute(): string
    {
        return ($this->deduction_rate * 100) . '%';
    }

    /**
     * Get default leave types for a company.
     */
    public static function getDefaultTypes(): array
    {
        return [
            [
                'name' => 'Sick Leave',
                'code' => 'SL',
                'description' => 'Leave for medical reasons and health issues',
                'max_days_per_year' => 10,
                'carry_forward_limit' => 5,
                'requires_medical_certificate' => true,
                'min_notice_days' => 0,
                'max_consecutive_days' => 5,
                'is_paid' => true,
                'weekend_included' => false,
                'holiday_included' => false,
                'deduction_rate' => 1.00,
                'is_active' => true
            ],
            [
                'name' => 'Casual Leave',
                'code' => 'CL',
                'description' => 'Leave for personal matters and casual purposes',
                'max_days_per_year' => 12,
                'carry_forward_limit' => 3,
                'requires_medical_certificate' => false,
                'min_notice_days' => 1,
                'max_consecutive_days' => 3,
                'is_paid' => true,
                'weekend_included' => false,
                'holiday_included' => false,
                'deduction_rate' => 1.00,
                'is_active' => true
            ],
            [
                'name' => 'Annual Leave',
                'code' => 'AL',
                'description' => 'Annual vacation and holiday leave',
                'max_days_per_year' => 21,
                'carry_forward_limit' => 7,
                'requires_medical_certificate' => false,
                'min_notice_days' => 7,
                'max_consecutive_days' => 0, // Unlimited
                'is_paid' => true,
                'weekend_included' => false,
                'holiday_included' => false,
                'deduction_rate' => 1.00,
                'is_active' => true
            ],
            [
                'name' => 'Maternity Leave',
                'code' => 'ML',
                'description' => 'Leave for maternity and childbirth',
                'max_days_per_year' => 90,
                'carry_forward_limit' => 0,
                'requires_medical_certificate' => true,
                'min_notice_days' => 30,
                'max_consecutive_days' => 90,
                'is_paid' => true,
                'weekend_included' => false,
                'holiday_included' => false,
                'applicable_roles' => ['Employee', 'TeamLead', 'HR', 'Finance'],
                'deduction_rate' => 1.00,
                'is_active' => true
            ],
            [
                'name' => 'Unpaid Leave',
                'code' => 'UL',
                'description' => 'Leave without pay for extended personal matters',
                'max_days_per_year' => 30,
                'carry_forward_limit' => 0,
                'requires_medical_certificate' => false,
                'min_notice_days' => 15,
                'max_consecutive_days' => 0,
                'is_paid' => false,
                'weekend_included' => false,
                'holiday_included' => false,
                'deduction_rate' => 1.00,
                'is_active' => true
            ]
        ];
    }

    /**
     * Create default leave types for a company.
     */
    public static function createDefaultForCompany($companyId)
    {
        $defaultTypes = self::getDefaultTypes();
        
        foreach ($defaultTypes as $typeData) {
            $typeData['company_id'] = $companyId;
            self::create($typeData);
        }
    }
}