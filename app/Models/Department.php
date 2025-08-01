<?php

namespace App\Models;

use App\Policies\DepartmentPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'status',
        'location',
        'phone',
        'email',
        'company_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::saved(function ($department) {
            Gate::policy(Department::class, DepartmentPolicy::class);
        });
    }

    /**
     * Get the company that owns the department.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the users for the department.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'department_id');
    }

    /**
     * Get the active users for the department.
     */
    public function activeUsers(): HasMany
    {
        return $this->users()->where('status', 1);
    }

    /**
     * Get the status label.
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    /**
     * Get the status badge class.
     *
     * @return string
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return $this->status ? 'bg-success' : 'bg-danger';
    }

    /**
     * Scope a query to only include active departments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
    
    /**
     * Scope a query to only include departments from a specific company.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $companyId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }
    
    /**
     * Scope a query based on user's role and company access.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Models\User  $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAccessibleBy($query, $user)
    {
        if ($user->hasRole('superAdmin')) {
            return $query; // SuperAdmin can access all departments
        }
        
        return $query->where('company_id', $user->company_id);
    }

    /**
     * Scope a query to only include inactive departments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    /**
     * Check if the department can be deleted.
     *
     * @return bool
     */
    public function canBeDeleted(): bool
    {
        return $this->users()->count() === 0;
    }
    
    /**
     * Check if the department can be managed by the given user.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function canBeManagedBy($user): bool
    {
        if ($user->hasRole('superAdmin')) {
            return true;
        }
        
        return $this->company_id === $user->company_id;
    }
    
    /**
     * Get departments accessible by user with user count.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAccessibleWithUserCount($user)
    {
        return static::accessibleBy($user)
            ->withCount('users')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get formatted created date.
     *
     * @return string
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->format('M d, Y');
    }

    /**
     * Get formatted updated date.
     *
     * @return string
     */
    public function getFormattedUpdatedAtAttribute(): string
    {
        return $this->updated_at->format('M d, Y');
    }
}