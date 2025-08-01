<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'company_id',
        'leave_id',
        'date',
        'check_in_time',
        'check_out_time',
        'total_hours',
        'break_duration',
        'status',
        'leave_type',
        'leave_type_name',
        'overtime_hours',
        'overtime_rate',
        'notes',
        'is_holiday',
        'is_weekend',
        'break_times',
        'ip_address',
        'location',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'string',
        'check_out_time' => 'string',
        'overtime_hours' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'is_holiday' => 'boolean',
        'is_weekend' => 'boolean',
        'break_times' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the attendance.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company that owns the attendance.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the leave that owns the attendance.
     */
    public function leave(): BelongsTo
    {
        return $this->belongsTo(Leave::class);
    }

    /**
     * Scope a query to only include attendance for a specific company.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope a query to only include attendance for a specific date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include attendance with specific status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include today's attendance.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date', Carbon::today());
    }

    /**
     * Scope a query to only include this month's attendance.
     */
    public function scopeThisMonth($query)
    {
        return $query->whereBetween('date', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ]);
    }

    /**
     * Get formatted total working hours.
     */
    public function getFormattedTotalHoursAttribute(): string
    {
        $hours = floor($this->total_hours / 60);
        $minutes = $this->total_hours % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    /**
     * Get formatted break duration.
     */
    public function getFormattedBreakDurationAttribute(): string
    {
        $hours = floor($this->break_duration / 60);
        $minutes = $this->break_duration % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    /**
     * Get status badge class for UI.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'present' => 'bg-success',
            'late' => 'bg-warning',
            'half_day' => 'bg-info',
            'early_leave' => 'bg-warning',
            'absent' => 'bg-danger',
            'unpaid_leave' => 'bg-secondary',
            'without_notice' => 'bg-danger',
            'on_leave' => 'bg-primary',
            default => 'bg-secondary'
        };
    }

    /**
     * Get status label for display.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'present' => 'Present',
            'late' => 'Late',
            'half_day' => 'Half Day',
            'early_leave' => 'Early Leave',
            'absent' => 'Absent',
            'unpaid_leave' => 'Unpaid Leave',
            'without_notice' => 'Without Notice',
            'on_leave' => 'On Leave',
            default => 'Unknown'
        };
    }

    /**
     * Check if user is currently checked in.
     */
    public function getIsCheckedInAttribute(): bool
    {
        return $this->check_in_time && !$this->check_out_time;
    }

    /**
     * Calculate total working hours in minutes.
     */
    public function calculateTotalHours(): int
    {
        if (!$this->check_in_time || !$this->check_out_time) {
            return 0;
        }

        try {
            // Handle both H:i:s and H:i formats
            $checkInTime = $this->check_in_time;
            $checkOutTime = $this->check_out_time;
            
            // Add seconds if not present
            if (substr_count($checkInTime, ':') == 1) {
                $checkInTime .= ':00';
            }
            if (substr_count($checkOutTime, ':') == 1) {
                $checkOutTime .= ':00';
            }
            
            $checkIn = Carbon::createFromFormat('H:i:s', $checkInTime);
            $checkOut = Carbon::createFromFormat('H:i:s', $checkOutTime);
            
            $totalMinutes = $checkOut->diffInMinutes($checkIn);
            $workingMinutes = $totalMinutes - $this->break_duration;

            return max(0, $workingMinutes);
        } catch (\Exception $e) {
            \Log::error('Error calculating total hours: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Calculate overtime hours.
     */
    public function calculateOvertimeHours($standardWorkingHours = 480): float // 8 hours = 480 minutes
    {
        $totalMinutes = $this->calculateTotalHours();
        $overtimeMinutes = max(0, $totalMinutes - $standardWorkingHours);
        
        return round($overtimeMinutes / 60, 2);
    }

    /**
     * Update attendance status based on working hours and timing.
     */
    public function updateStatus($standardWorkingHours = 480, $lateThreshold = 30): void
    {
        if (!$this->check_in_time) {
            $this->status = 'absent';
            return;
        }

        try {
            // Handle time format
            $checkInTime = $this->check_in_time;
            if (substr_count($checkInTime, ':') == 1) {
                $checkInTime .= ':00';
            }
            
            $checkIn = Carbon::createFromFormat('H:i:s', $checkInTime);
            $standardCheckIn = Carbon::createFromTimeString('09:00:00'); // Assuming 9 AM standard
            
            $isLate = $checkIn->gt($standardCheckIn->addMinutes($lateThreshold));
            
            if ($this->check_out_time) {
                $totalMinutes = $this->calculateTotalHours();
                $halfDayThreshold = $standardWorkingHours / 2; // 4 hours for half day
                
                if ($totalMinutes < $halfDayThreshold) {
                    $this->status = 'half_day';
                } elseif ($totalMinutes >= $standardWorkingHours) {
                    $this->status = $isLate ? 'late' : 'present';
                    
                    // Calculate overtime
                    $this->overtime_hours = $this->calculateOvertimeHours($standardWorkingHours);
                } else {
                    $this->status = 'early_leave';
                }
            } else {
                // Only checked in, no checkout yet
                $this->status = $isLate ? 'late' : 'present';
            }

            $this->total_hours = $this->calculateTotalHours();
            $this->save();
        } catch (\Exception $e) {
            \Log::error('Error updating attendance status: ' . $e->getMessage());
            $this->status = 'present'; // Default status
            $this->save();
        }
    }

    /**
     * Get attendance summary for a user.
     */
    public static function getUserSummary($userId, $startDate, $endDate)
    {
        $attendances = self::where('user_id', $userId)
            ->dateRange($startDate, $endDate)
            ->get();

        return [
            'total_days' => $attendances->count(),
            'present_days' => $attendances->where('status', 'present')->count(),
            'late_days' => $attendances->where('status', 'late')->count(),
            'half_days' => $attendances->where('status', 'half_day')->count(),
            'absent_days' => $attendances->where('status', 'absent')->count(),
            'unpaid_leave_days' => $attendances->where('status', 'unpaid_leave')->count(),
            'without_notice_days' => $attendances->where('status', 'without_notice')->count(),
            'total_hours' => $attendances->sum('total_hours'),
            'total_overtime_hours' => $attendances->sum('overtime_hours'),
            'attendance_percentage' => $attendances->count() > 0 
                ? round(($attendances->whereIn('status', ['present', 'late', 'half_day'])->count() / $attendances->count()) * 100, 2)
                : 0
        ];
    }

    /**
     * Get company attendance summary.
     */
    public static function getCompanySummary($companyId, $date = null)
    {
        $date = $date ?: Carbon::today();
        
        $attendances = self::forCompany($companyId)
            ->whereDate('date', $date)
            ->get();

        $totalEmployees = User::where('company_id', $companyId)
            ->whereHas('roles', function($query) {
                $query->whereIn('name', ['Employee', 'TeamLead', 'HR', 'Finance']);
            })
            ->count();

        return [
            'total_employees' => $totalEmployees,
            'present_today' => $attendances->whereIn('status', ['present', 'late'])->count(),
            'absent_today' => $attendances->where('status', 'absent')->count(),
            'late_today' => $attendances->where('status', 'late')->count(),
            'half_day_today' => $attendances->where('status', 'half_day')->count(),
            'on_leave_today' => $attendances->whereIn('status', ['on_leave', 'unpaid_leave', 'without_notice'])->count(),
            'attendance_rate' => $totalEmployees > 0 
                ? round(($attendances->whereIn('status', ['present', 'late', 'half_day', 'on_leave'])->count() / $totalEmployees) * 100, 2)  
                : 0
        ];
    }
}