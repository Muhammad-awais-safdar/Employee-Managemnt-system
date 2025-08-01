<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable , HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'team_lead_id',
        'department_id',
        'status',
        'phone',
        'address',
        'date_of_birth',
        'profile_image',
        'bio',
        'employee_id',
        'date_of_joining',
        'salary',
        'qualification',
        'experience_years',
        'skills',
        'gender',
        'marital_status',
        'emergency_contact_name',
        'emergency_contact_phone',
        'linkedin_url',
        'twitter_url',
        'settings',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
            'date_of_birth' => 'date',
            'date_of_joining' => 'date',
            'settings' => 'array',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function ownedCompany()
    {
        return $this->hasOne(Company::class);
    }

    public function teamLead()
    {
        return $this->belongsTo(User::class, 'team_lead_id');
    }

    public function teamMembers()
    {
        return $this->hasMany(User::class, 'team_lead_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function todayAttendance()
    {
        return $this->hasOne(Attendance::class)->whereDate('date', today());
    }

    public function currentMonthAttendances()
    {
        return $this->hasMany(Attendance::class)
            ->whereBetween('date', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ]);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function approvedLeaves()
    {
        return $this->hasMany(Leave::class, 'approved_by');
    }

    public function salaryHistories()
    {
        return $this->hasMany(SalaryHistory::class, 'employee_id');
    }

    public function salaryChanges()
    {
        return $this->hasMany(SalaryHistory::class, 'changed_by');
    }

    public function getCurrentSalaryHistoryAttribute()
    {
        return $this->salaryHistories()->orderBy('effective_date', 'desc')->first();
    }
}
