<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'description',
        'max_amount',
        'requires_receipt',
        'auto_approve_limit',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'max_amount' => 'decimal:2',
        'auto_approve_limit' => 'decimal:2',
        'requires_receipt' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company that owns the expense category.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the expenses for this category.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'category_id');
    }

    /**
     * Scope for company-specific categories.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope for active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for categories ordered by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Check if amount can be auto-approved.
     */
    public function canAutoApprove($amount): bool
    {
        return $this->auto_approve_limit && $amount <= $this->auto_approve_limit;
    }

    /**
     * Check if amount exceeds maximum limit.
     */
    public function exceedsMaxAmount($amount): bool
    {
        return $this->max_amount && $amount > $this->max_amount;
    }
}