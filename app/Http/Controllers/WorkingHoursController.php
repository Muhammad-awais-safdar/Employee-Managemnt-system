<?php

namespace App\Http\Controllers;

use App\Models\WorkingHoursSettings;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WorkingHoursController extends Controller
{
    /**
     * Display working hours settings
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $companyId = $this->getCompanyId($user);
        
        if (!$companyId) {
            return redirect()->back()->with('error', 'No company associated with your account.');
        }

        $workingHours = WorkingHoursSettings::getForCompany($companyId);
        $company = Company::find($companyId);

        return view('EmployeeManagemntsystem.Admin.settings.working-hours', compact('workingHours', 'company'));
    }

    /**
     * Show the form for creating new working hours settings
     */
    public function create()
    {
        $user = Auth::user();
        $companyId = $this->getCompanyId($user);
        
        if (!$companyId) {
            return redirect()->back()->with('error', 'No company associated with your account.');
        }

        $company = Company::find($companyId);
        $defaults = WorkingHoursSettings::getDefaults();

        return view('EmployeeManagemntsystem.Admin.settings.working-hours-form', compact('company', 'defaults'));
    }

    /**
     * Store working hours settings
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $companyId = $this->getCompanyId($user);
        
        if (!$companyId) {
            return response()->json(['success' => false, 'message' => 'No company associated with your account.']);
        }

        $validator = $this->validateWorkingHours($request);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ]);
        }

        try {
            // Deactivate existing settings
            WorkingHoursSettings::where('company_id', $companyId)->update(['is_active' => false]);

            // Create new settings
            $data = $request->all();
            $data['company_id'] = $companyId;
            $data['is_active'] = true;

            // Convert time inputs to proper format
            $data['standard_hours'] = $this->convertToMinutes($data['standard_hours_input'] ?? '8:00');
            $data['break_duration'] = $this->convertToMinutes($data['break_duration_input'] ?? '1:00');
            $data['max_daily_hours'] = $this->convertToMinutes($data['max_daily_hours_input'] ?? '12:00');
            $data['min_daily_hours'] = $this->convertToMinutes($data['min_daily_hours_input'] ?? '4:00');

            // Handle checkboxes
            $data['flexible_hours'] = $request->has('flexible_hours');
            $data['track_location'] = $request->has('track_location');
            $data['auto_break_deduction'] = $request->has('auto_break_deduction');

            // Handle working days
            $data['working_days'] = $request->input('working_days', [1, 2, 3, 4, 5]);

            // Handle allowed locations
            $data['allowed_locations'] = $request->has('track_location') ? 
                array_filter(explode("\n", $request->input('allowed_locations', ''))) : [];

            $workingHours = WorkingHoursSettings::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Working hours settings saved successfully!',
                'data' => $workingHours
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save working hours settings: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show the form for editing working hours settings
     */
    public function edit($id)
    {
        $user = Auth::user();
        $companyId = $this->getCompanyId($user);
        
        $workingHours = WorkingHoursSettings::where('id', $id)
                                          ->where('company_id', $companyId)
                                          ->firstOrFail();
        
        $company = Company::find($companyId);

        return view('EmployeeManagemntsystem.Admin.settings.working-hours-form', compact('workingHours', 'company'));
    }

    /**
     * Update working hours settings
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $companyId = $this->getCompanyId($user);
        
        $workingHours = WorkingHoursSettings::where('id', $id)
                                          ->where('company_id', $companyId)
                                          ->firstOrFail();

        $validator = $this->validateWorkingHours($request);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ]);
        }

        try {
            $data = $request->all();

            // Convert time inputs to proper format
            $data['standard_hours'] = $this->convertToMinutes($data['standard_hours_input'] ?? '8:00');
            $data['break_duration'] = $this->convertToMinutes($data['break_duration_input'] ?? '1:00');
            $data['max_daily_hours'] = $this->convertToMinutes($data['max_daily_hours_input'] ?? '12:00');
            $data['min_daily_hours'] = $this->convertToMinutes($data['min_daily_hours_input'] ?? '4:00');

            // Handle checkboxes
            $data['flexible_hours'] = $request->has('flexible_hours');
            $data['track_location'] = $request->has('track_location');
            $data['auto_break_deduction'] = $request->has('auto_break_deduction');

            // Handle working days
            $data['working_days'] = $request->input('working_days', [1, 2, 3, 4, 5]);

            // Handle allowed locations
            $data['allowed_locations'] = $request->has('track_location') ? 
                array_filter(explode("\n", $request->input('allowed_locations', ''))) : [];

            $workingHours->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Working hours settings updated successfully!',
                'data' => $workingHours
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update working hours settings: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Remove working hours settings
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $companyId = $this->getCompanyId($user);
        
        try {
            $workingHours = WorkingHoursSettings::where('id', $id)
                                              ->where('company_id', $companyId)
                                              ->firstOrFail();

            // Don't delete, just deactivate
            $workingHours->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Working hours settings deactivated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to deactivate working hours settings: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get company settings as JSON (for API or AJAX calls)
     */
    public function getSettings(Request $request)
    {
        $user = Auth::user();
        $companyId = $this->getCompanyId($user);
        
        if (!$companyId) {
            return response()->json(['success' => false, 'message' => 'No company found']);
        }

        $workingHours = WorkingHoursSettings::getForCompany($companyId);

        return response()->json([
            'success' => true,
            'data' => [
                'standard_hours' => $workingHours->standard_hours,
                'check_in_time' => $workingHours->check_in_time,
                'check_out_time' => $workingHours->check_out_time,
                'break_duration' => $workingHours->break_duration,
                'late_threshold' => $workingHours->late_threshold,
                'overtime_rate' => $workingHours->overtime_rate,
                'formatted_standard_hours' => $workingHours->formatted_standard_hours,
                'formatted_break_duration' => $workingHours->formatted_break_duration,
                'working_days' => $workingHours->working_days,
                'flexible_hours' => $workingHours->flexible_hours,
                'track_location' => $workingHours->track_location
            ]
        ]);
    }

    /**
     * Validate working hours input
     */
    private function validateWorkingHours(Request $request)
    {
        return Validator::make($request->all(), [
            'check_in_time' => 'required|date_format:H:i:s|before:check_out_time',
            'check_out_time' => 'required|date_format:H:i:s|after:check_in_time',
            'standard_hours_input' => 'required|regex:/^\d{1,2}:\d{2}$/',
            'break_duration_input' => 'required|regex:/^\d{1,2}:\d{2}$/',
            'late_threshold' => 'required|integer|min:0|max:120',
            'early_leave_threshold' => 'required|integer|min:0|max:120',
            'overtime_rate' => 'required|numeric|min:1|max:5',
            'weekend_overtime_rate' => 'required|numeric|min:1|max:5',
            'holiday_overtime_rate' => 'required|numeric|min:1|max:5',
            'grace_period' => 'required|integer|min:0|max:30',
            'max_daily_hours_input' => 'required|regex:/^\d{1,2}:\d{2}$/',
            'min_daily_hours_input' => 'required|regex:/^\d{1,2}:\d{2}$/',
            'working_days' => 'required|array|min:1',
            'working_days.*' => 'integer|between:0,6'
        ], [
            'check_in_time.required' => 'Check-in time is required',
            'check_out_time.after' => 'Check-out time must be after check-in time',
            'standard_hours_input.regex' => 'Standard hours must be in HH:MM format',
            'break_duration_input.regex' => 'Break duration must be in HH:MM format',
            'working_days.min' => 'At least one working day must be selected'
        ]);
    }

    /**
     * Convert HH:MM format to minutes
     */
    private function convertToMinutes($timeString)
    {
        list($hours, $minutes) = explode(':', $timeString);
        return ($hours * 60) + $minutes;
    }

    /**
     * Get company ID based on user role
     */
    private function getCompanyId($user)
    {
        if ($user->hasRole('superAdmin')) {
            // Super admin can manage any company
            return request()->input('company_id') ?? $user->company_id;
        }
        
        return $user->company_id;
    }
}