<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingHoursSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'standard_hours',
        'check_in_time',
        'check_out_time',
        'break_duration',
        'late_threshold',
        'early_leave_threshold',
        'overtime_rate',
        'weekend_overtime_rate',
        'holiday_overtime_rate',
        'flexible_hours',
        'core_hours_start',
        'core_hours_end',
        'max_daily_hours',
        'min_daily_hours',
        'track_location',
        'allowed_locations',
        'auto_break_deduction',
        'grace_period',
        'working_days',
        'is_active'
    ];

    protected $casts = [
        'allowed_locations' => 'array',
        'working_days' => 'array',
        'flexible_hours' => 'boolean',
        'track_location' => 'boolean',
        'auto_break_deduction' => 'boolean',
        'is_active' => 'boolean',
        'standard_hours' => 'integer',
        'break_duration' => 'integer',
        'late_threshold' => 'integer',
        'early_leave_threshold' => 'integer',
        'grace_period' => 'integer',
        'max_daily_hours' => 'integer',
        'min_daily_hours' => 'integer',
        'overtime_rate' => 'decimal:2',
        'weekend_overtime_rate' => 'decimal:2',
        'holiday_overtime_rate' => 'decimal:2'
    ];

    /**
     * Get the company that owns the working hours settings.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get standard hours in hours:minutes format
     */
    public function getFormattedStandardHoursAttribute()
    {
        $hours = floor($this->standard_hours / 60);
        $minutes = $this->standard_hours % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    /**
     * Get break duration in hours:minutes format
     */
    public function getFormattedBreakDurationAttribute()
    {
        $hours = floor($this->break_duration / 60);
        $minutes = $this->break_duration % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    /**
     * Check if a day is a working day
     */
    public function isWorkingDay($dayOfWeek)
    {
        return in_array($dayOfWeek, $this->working_days ?? [1, 2, 3, 4, 5]);
    }

    /**
     * Get default working hours settings
     */
    public static function getDefaults()
    {
        return [
            'standard_hours' => 480, // 8 hours in minutes
            'check_in_time' => '09:00:00',
            'check_out_time' => '18:00:00',
            'break_duration' => 60, // 1 hour in minutes
            'late_threshold' => 15, // 15 minutes
            'early_leave_threshold' => 15, // 15 minutes
            'overtime_rate' => 1.5,
            'weekend_overtime_rate' => 2.0,
            'holiday_overtime_rate' => 2.5,
            'flexible_hours' => false,
            'core_hours_start' => '10:00:00',
            'core_hours_end' => '16:00:00',
            'max_daily_hours' => 720, // 12 hours
            'min_daily_hours' => 240, // 4 hours
            'track_location' => false,
            'allowed_locations' => [],
            'auto_break_deduction' => true,
            'grace_period' => 5, // 5 minutes
            'working_days' => [1, 2, 3, 4, 5], // Monday to Friday
            'is_active' => true
        ];
    }

    /**
     * Create default settings for a company
     */
    public static function createDefaultForCompany($companyId)
    {
        $defaults = self::getDefaults();
        $defaults['company_id'] = $companyId;
        
        return self::create($defaults);
    }

    /**
     * Get working hours settings for a company (with fallback to defaults)
     */
    public static function getForCompany($companyId)
    {
        $settings = self::where('company_id', $companyId)
                        ->where('is_active', true)
                        ->first();

        if (!$settings) {
            // Create default settings for the company
            $settings = self::createDefaultForCompany($companyId);
        }

        return $settings;
    }

    /**
     * Calculate expected check out time based on check in time
     */
    public function calculateExpectedCheckOutTime($checkInTime)
    {
        $checkIn = \Carbon\Carbon::parse($checkInTime);
        $workingMinutes = $this->standard_hours + $this->break_duration;
        
        return $checkIn->addMinutes($workingMinutes)->format('H:i:s');
    }

    /**
     * Check if employee is late
     */
    public function isLate($checkInTime)
    {
        $checkIn = \Carbon\Carbon::parse($checkInTime);
        $standardCheckIn = \Carbon\Carbon::parse($this->check_in_time);
        
        if ($this->grace_period > 0) {
            $standardCheckIn->addMinutes($this->grace_period);
        }
        
        return $checkIn->gt($standardCheckIn);
    }

    /**
     * Check if employee left early
     */
    public function isEarlyLeave($checkOutTime, $checkInTime)
    {
        $expectedCheckOut = $this->calculateExpectedCheckOutTime($checkInTime);
        $actualCheckOut = \Carbon\Carbon::parse($checkOutTime);
        $expectedTime = \Carbon\Carbon::parse($expectedCheckOut);
        
        $differenceInMinutes = $expectedTime->diffInMinutes($actualCheckOut, false);
        
        return $differenceInMinutes > $this->early_leave_threshold;
    }

    /**
     * Calculate overtime hours
     */
    public function calculateOvertimeHours($totalWorkingMinutes)
    {
        $standardMinutes = $this->standard_hours;
        
        if ($totalWorkingMinutes > $standardMinutes) {
            $overtimeMinutes = $totalWorkingMinutes - $standardMinutes;
            return round($overtimeMinutes / 60, 2); // Convert to hours
        }
        
        return 0;
    }
}