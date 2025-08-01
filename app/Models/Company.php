<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    
    protected $fillable = [
        'company_name',
        'contact_person',
        'email',
        'phone',
        'website',
        'logo',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'status',
        'notes',
        'user_id',
    ];

    /**
     * Scope to only get active companies.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the working hours settings for this company.
     */
    public function workingHoursSettings()
    {
        return $this->hasMany(WorkingHoursSettings::class);
    }

    /**
     * Get the active working hours settings for this company.
     */
    public function activeWorkingHours()
    {
        return $this->hasOne(WorkingHoursSettings::class)->where('is_active', true);
    }

    /**
     * Get the departments belonging to this company.
     */
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Accessor for capitalized company name.
     */
    public function getCompanyNameAttribute($value)
    {
        return ucwords($value);
    }

    /**
     * Full address accessor (optional).
     */
    public function getFullAddressAttribute()
    {
        return trim("{$this->address}, {$this->city}, {$this->state}, {$this->country}, {$this->postal_code}", ', ');
    }
}
