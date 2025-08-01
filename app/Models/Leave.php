<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id', 
        'leave_type_id',
        'approved_by',
        'start_date',
        'end_date',
        'duration',
        'total_days',
        'reason',
        'comments',
        'admin_notes',
        'status',
        'applied_at',
        'reviewed_at',
        'attachments',
        'emergency_leave',
        'contact_number',
        'handover_notes',
        'application_id',
        'approval_workflow',
        'affects_attendance'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_days' => 'decimal:2',
        'applied_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'attachments' => 'array',
        'emergency_leave' => 'boolean',
        'approval_workflow' => 'array',
        'affects_attendance' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model and set up event listeners.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($leave) {
            // Generate application ID
            if (!$leave->application_id) {
                $leave->application_id = $leave->generateApplicationId();
            }
            
            // Set applied_at timestamp
            if (!$leave->applied_at) {
                $leave->applied_at = now();
            }
            
            // Calculate total days if not set
            if (!$leave->total_days) {
                $leave->total_days = $leave->calculateTotalDays();
            }
        });
        
        static::updated(function ($leave) {
            // Update attendance records when leave status changes
            if ($leave->isDirty('status') && $leave->affects_attendance) {
                $leave->updateAttendanceRecords();
            }
        });
    }

    /**
     * Get the user that owns the leave.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company that owns the leave.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the leave type.
     */
    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * Get the user who approved the leave.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Generate a unique application ID.
     */
    public function generateApplicationId(): string
    {
        $year = date('Y');
        $lastLeave = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
            
        $sequence = $lastLeave ? intval(substr($lastLeave->application_id, -3)) + 1 : 1;
        
        return 'LV-' . $year . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate total leave days based on start date, end date, and duration.
     */
    public function calculateTotalDays(): float
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        
        if ($start->gt($end)) {
            return 0;
        }

        // If it's the same day
        if ($start->isSameDay($end)) {
            return match($this->duration) {
                'first_half', 'second_half' => 0.5,
                default => 1.0
            };
        }

        // Calculate business days between dates
        $totalDays = 0;
        $current = $start->copy();
        
        while ($current->lte($end)) {
            // Check if it's a working day (skip weekends if leave type excludes them)
            if ($this->leaveType && !$this->leaveType->weekend_included) {
                if (!$current->isWeekend()) {
                    $totalDays++;
                }
            } else {
                $totalDays++;
            }
            $current->addDay();
        }

        // Adjust for partial days on first/last day
        if ($this->duration === 'first_half' || $this->duration === 'second_half') {
            $totalDays -= 0.5;
        }

        return $totalDays;
    }

    /**
     * Update attendance records for approved/rejected leave.
     */
    public function updateAttendanceRecords()
    {
        if (!$this->affects_attendance) {
            return;
        }

        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        $current = $start->copy();

        while ($current->lte($end)) {
            $attendance = Attendance::firstOrCreate([
                'user_id' => $this->user_id,
                'date' => $current->toDateString(),
            ], [
                'company_id' => $this->company_id,
                'is_weekend' => $current->isWeekend(),
            ]);

            if ($this->status === 'approved') {
                // Load the relationship if not already loaded
                if (!$this->relationLoaded('leaveType')) {
                    $this->load('leaveType');
                }
                
                $leaveTypeName = $this->leaveType ? $this->leaveType->name : 'Leave';
                
                // Mark as leave in attendance
                $attendance->status = 'on_leave';
                $attendance->leave_id = $this->id; // Link to the leave record
                
                // Set appropriate leave_type enum based on duration
                $attendance->leave_type = match($this->duration) {
                    'first_half', 'second_half' => 'half_day',
                    default => 'full_day'
                };
                
                $attendance->leave_type_name = $leaveTypeName; // Use the new column for actual leave type name
                $attendance->notes = "On {$leaveTypeName} - Application: {$this->application_id}";
                $attendance->save();
            } elseif ($this->status === 'rejected' || $this->status === 'cancelled') {
                // Reset attendance status if leave was rejected/cancelled
                if ($attendance->status === 'on_leave') {
                    $attendance->status = 'absent';
                    $attendance->leave_id = null;
                    $attendance->leave_type = null;
                    $attendance->leave_type_name = null;
                    $attendance->notes = null;
                    $attendance->save();
                }
            }

            $current->addDay();
        }
    }

    /**
     * Scope for pending leaves.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved leaves.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for company-specific leaves.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope for date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function($q2) use ($startDate, $endDate) {
                  $q2->where('start_date', '<=', $startDate)
                     ->where('end_date', '>=', $endDate);
              });
        });
    }

    /**
     * Scope for leaves that a user can review.
     */
    public function scopeReviewableBy($query, User $user)
    {
        // SuperAdmin can review all leaves
        if ($user->hasRole('superAdmin')) {
            return $query;
        }

        // Must be from the same company
        $query->where('company_id', $user->company_id);

        // Admin can review all leaves in their company
        if ($user->hasRole('Admin')) {
            return $query;
        }

        // HR can review leaves except for Admin and SuperAdmin leaves
        if ($user->hasRole('HR')) {
            return $query->whereHas('user', function($q) {
                $q->whereDoesntHave('roles', function($roleQuery) {
                    $roleQuery->whereIn('name', ['Admin', 'superAdmin']);
                });
            });
        }

        // Team lead can review their team members' leaves
        if ($user->hasRole('TeamLead')) {
            return $query->whereHas('user', function($q) use ($user) {
                $q->where('team_lead_id', $user->id)
                  ->whereDoesntHave('roles', function($roleQuery) {
                      $roleQuery->whereIn('name', ['Admin', 'superAdmin', 'HR']);
                  });
            });
        }

        // If no matching role, return empty query
        return $query->where('id', -1);
    }

    /**
     * Get the status badge class for UI.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-warning',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            'cancelled' => 'bg-secondary',
            'withdrawn' => 'bg-info',
            default => 'bg-secondary'
        };
    }

    /**
     * Get the status label for display.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
            'withdrawn' => 'Withdrawn',
            default => 'Unknown'
        };
    }

    /**
     * Get the duration label for display.
     */
    public function getDurationLabelAttribute(): string
    {
        return match($this->duration) {
            'full_day' => 'Full Day',
            'first_half' => 'First Half',
            'second_half' => 'Second Half',
            default => 'Full Day'
        };
    }

    /**
     * Check if leave can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        if (!in_array($this->status, ['pending', 'approved'])) {
            return false;
        }
        
        // Can't cancel if leave has already started
        return Carbon::parse($this->start_date)->isFuture();
    }

    /**
     * Check if leave can be edited.
     */
    public function canBeEdited(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }
        
        // Can edit if leave hasn't started yet
        return Carbon::parse($this->start_date)->isFuture();
    }

    /**
     * Get overlapping leaves for the same user.
     */
    public function getOverlappingLeaves()
    {
        return self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->where('status', '!=', 'rejected')
            ->dateRange($this->start_date, $this->end_date)
            ->get();
    }

    /**
     * Check if there are any overlapping leaves.
     */
    public function hasOverlappingLeaves(): bool
    {
        return $this->getOverlappingLeaves()->count() > 0;
    }

    /**
     * Get formatted date range.
     */
    public function getDateRangeAttribute(): string
    {
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        
        if ($start->isSameDay($end)) {
            return $start->format('M d, Y') . ' (' . $this->duration_label . ')';
        }
        
        return $start->format('M d, Y') . ' - ' . $end->format('M d, Y');
    }

    /**
     * Get days until leave starts.
     */
    public function getDaysUntilStartAttribute(): int
    {
        return max(0, Carbon::now()->diffInDays($this->start_date, false));
    }

    /**
     * Check if leave is current (ongoing).
     */
    public function getIsCurrentAttribute(): bool
    {
        $now = Carbon::now()->startOfDay();
        $start = Carbon::parse($this->start_date)->startOfDay();
        $end = Carbon::parse($this->end_date)->endOfDay();
        
        return $this->status === 'approved' && $now->between($start, $end);
    }
}