@extends('EmployeeManagemntsystem.Layout.App')

@section('title', isset($workingHours) ? 'Edit Working Hours' : 'Create Working Hours Policy')

@section('content')
<div class="content">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3 class="page-title">{{ isset($workingHours) ? 'Edit Working Hours Settings' : 'Create Working Hours Policy' }}</h3>
                <p class="text-muted">Configure attendance rules and working time policies</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route(auth()->user()->getRoleNames()->first() . '.working-hours.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left me-2"></i>Back to Settings
                </a>
            </div>
        </div>
    </div>

    <form id="workingHoursForm" method="POST" action="{{ isset($workingHours) ? route(auth()->user()->getRoleNames()->first() . '.working-hours.update', $workingHours->id) : route(auth()->user()->getRoleNames()->first() . '.working-hours.store') }}">
        @csrf
        @if(isset($workingHours))
            @method('PUT')
        @endif

        <div class="row">
            <!-- Basic Working Hours -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fa fa-clock text-primary me-2"></i>Basic Working Hours
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="check_in_time" class="form-label">Check-in Time <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" name="check_in_time" id="check_in_time" 
                                           value="{{ isset($workingHours) ? $workingHours->check_in_time : '09:00' }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="check_out_time" class="form-label">Check-out Time <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" name="check_out_time" id="check_out_time" 
                                           value="{{ isset($workingHours) ? $workingHours->check_out_time : '18:00' }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="standard_hours_input" class="form-label">Daily Working Hours <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="standard_hours_input" id="standard_hours_input" 
                                           value="{{ isset($workingHours) ? $workingHours->formatted_standard_hours : '8:00' }}" 
                                           placeholder="8:00" pattern="[0-9]{1,2}:[0-9]{2}" required>
                                    <small class="text-muted">Format: HH:MM (e.g., 8:00 for 8 hours)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="break_duration_input" class="form-label">Break Duration <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="break_duration_input" id="break_duration_input" 
                                           value="{{ isset($workingHours) ? $workingHours->formatted_break_duration : '1:00' }}" 
                                           placeholder="1:00" pattern="[0-9]{1,2}:[0-9]{2}" required>
                                    <small class="text-muted">Format: HH:MM (e.g., 1:00 for 1 hour)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Rules -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fa fa-user-clock text-warning me-2"></i>Attendance Rules
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="late_threshold" class="form-label">Late Threshold (minutes)</label>
                                    <input type="number" class="form-control" name="late_threshold" id="late_threshold" 
                                           value="{{ isset($workingHours) ? $workingHours->late_threshold : 15 }}" 
                                           min="0" max="120" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="grace_period" class="form-label">Grace Period (minutes)</label>
                                    <input type="number" class="form-control" name="grace_period" id="grace_period" 
                                           value="{{ isset($workingHours) ? $workingHours->grace_period : 5 }}" 
                                           min="0" max="30" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="early_leave_threshold" class="form-label">Early Leave Threshold (minutes)</label>
                                    <input type="number" class="form-control" name="early_leave_threshold" id="early_leave_threshold" 
                                           value="{{ isset($workingHours) ? $workingHours->early_leave_threshold : 15 }}" 
                                           min="0" max="120" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <div class="form-check form-switch mt-4">
                                        <input class="form-check-input" type="checkbox" name="auto_break_deduction" id="auto_break_deduction" 
                                               {{ isset($workingHours) && $workingHours->auto_break_deduction ? 'checked' : 'checked' }}>
                                        <label class="form-check-label" for="auto_break_deduction">
                                            Auto Break Deduction
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overtime Settings -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fa fa-money-bill text-success me-2"></i>Overtime Rates
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="overtime_rate" class="form-label">Regular Overtime</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="overtime_rate" id="overtime_rate" 
                                               value="{{ isset($workingHours) ? $workingHours->overtime_rate : 1.5 }}" 
                                               step="0.1" min="1" max="5" required>
                                        <span class="input-group-text">x</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="weekend_overtime_rate" class="form-label">Weekend Overtime</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="weekend_overtime_rate" id="weekend_overtime_rate" 
                                               value="{{ isset($workingHours) ? $workingHours->weekend_overtime_rate : 2.0 }}" 
                                               step="0.1" min="1" max="5" required>
                                        <span class="input-group-text">x</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="holiday_overtime_rate" class="form-label">Holiday Overtime</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="holiday_overtime_rate" id="holiday_overtime_rate" 
                                               value="{{ isset($workingHours) ? $workingHours->holiday_overtime_rate : 2.5 }}" 
                                               step="0.1" min="1" max="5" required>
                                        <span class="input-group-text">x</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Working Days -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fa fa-calendar text-info me-2"></i>Working Days
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="form-label">Select Working Days <span class="text-danger">*</span></label>
                            <div class="working-days-selector">
                                @php
                                    $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                    $selectedDays = isset($workingHours) ? $workingHours->working_days : [1,2,3,4,5];
                                @endphp
                                @foreach($dayNames as $index => $day)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="working_days[]" 
                                               id="day_{{ $index }}" value="{{ $index }}" 
                                               {{ in_array($index, $selectedDays) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="day_{{ $index }}">
                                            {{ $day }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flexible Hours -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fa fa-clock-rotate-left text-purple me-2"></i>Flexible Hours Configuration
                            </h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="flexible_hours" id="flexible_hours" 
                                       {{ isset($workingHours) && $workingHours->flexible_hours ? 'checked' : '' }}>
                                <label class="form-check-label" for="flexible_hours">
                                    Enable Flexible Hours
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="flexible-hours-content" style="{{ isset($workingHours) && $workingHours->flexible_hours ? '' : 'display: none;' }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="core_hours_start" class="form-label">Core Hours Start</label>
                                    <input type="time" class="form-control" name="core_hours_start" id="core_hours_start" 
                                           value="{{ isset($workingHours) ? $workingHours->core_hours_start : '10:00' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="core_hours_end" class="form-label">Core Hours End</label>
                                    <input type="time" class="form-control" name="core_hours_end" id="core_hours_end" 
                                           value="{{ isset($workingHours) ? $workingHours->core_hours_end : '16:00' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="min_daily_hours_input" class="form-label">Minimum Daily Hours</label>
                                    <input type="text" class="form-control" name="min_daily_hours_input" id="min_daily_hours_input" 
                                           value="{{ isset($workingHours) ? sprintf('%d:%02d', floor($workingHours->min_daily_hours / 60), $workingHours->min_daily_hours % 60) : '4:00' }}" 
                                           placeholder="4:00" pattern="[0-9]{1,2}:[0-9]{2}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="max_daily_hours_input" class="form-label">Maximum Daily Hours</label>
                                    <input type="text" class="form-control" name="max_daily_hours_input" id="max_daily_hours_input" 
                                           value="{{ isset($workingHours) ? sprintf('%d:%02d', floor($workingHours->max_daily_hours / 60), $workingHours->max_daily_hours % 60) : '12:00' }}" 
                                           placeholder="12:00" pattern="[0-9]{1,2}:[0-9]{2}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Tracking -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fa fa-map-marker-alt text-danger me-2"></i>Location Tracking
                            </h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="track_location" id="track_location" 
                                       {{ isset($workingHours) && $workingHours->track_location ? 'checked' : '' }}>
                                <label class="form-check-label" for="track_location">
                                    Enable Location Tracking
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="location-tracking-content" style="{{ isset($workingHours) && $workingHours->track_location ? '' : 'display: none;' }}">
                        <div class="form-group">
                            <label for="allowed_locations" class="form-label">Allowed Locations</label>
                            <textarea class="form-control" name="allowed_locations" id="allowed_locations" rows="4" 
                                      placeholder="Enter allowed locations (one per line)&#10;e.g.:&#10;Office Building A&#10;Remote Work Location&#10;Branch Office">{{ isset($workingHours) && $workingHours->allowed_locations ? implode("\n", $workingHours->allowed_locations) : '' }}</textarea>
                            <small class="text-muted">Enter one location per line. Leave empty to allow any location.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-3">
                            <i class="fa fa-save me-2"></i>{{ isset($workingHours) ? 'Update Settings' : 'Save Settings' }}
                        </button>
                        <a href="{{ route(auth()->user()->getRoleNames()->first() . '.working-hours.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fa fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.working-days-selector {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.working-days-selector .form-check {
    margin-bottom: 0;
}

.text-purple {
    color: #6f42c1 !important;
}

.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    border-radius: 10px 10px 0 0 !important;
}

.form-control:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
}

.btn-primary {
    background: linear-gradient(45deg, #4f46e5, #7c3aed);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(45deg, #4338ca, #6d28d9);
}

@media (max-width: 768px) {
    .working-days-selector {
        flex-direction: column;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle flexible hours content
    const flexibleHoursCheckbox = document.getElementById('flexible_hours');
    const flexibleHoursContent = document.getElementById('flexible-hours-content');
    
    flexibleHoursCheckbox.addEventListener('change', function() {
        if (this.checked) {
            flexibleHoursContent.style.display = 'block';
        } else {
            flexibleHoursContent.style.display = 'none';
        }
    });

    // Toggle location tracking content
    const locationTrackingCheckbox = document.getElementById('track_location');
    const locationTrackingContent = document.getElementById('location-tracking-content');
    
    locationTrackingCheckbox.addEventListener('change', function() {
        if (this.checked) {
            locationTrackingContent.style.display = 'block';
        } else {
            locationTrackingContent.style.display = 'none';
        }
    });

    // Form submission
    document.getElementById('workingHoursForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Saving...';
        submitBtn.disabled = true;

        // Prepare form data
        const formData = new FormData(this);
        
        // Convert FormData to regular object for JSON
        const data = {};
        for (let [key, value] of formData.entries()) {
            if (key.endsWith('[]')) {
                const arrayKey = key.slice(0, -2);
                if (!data[arrayKey]) data[arrayKey] = [];
                data[arrayKey].push(value);
            } else {
                data[key] = value;
            }
        }

        // Submit form
        fetch(this.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message, 'Success');
                setTimeout(() => {
                    window.location.href = "{{ route(auth()->user()->getRoleNames()->first() . '.working-hours.index') }}";
                }, 1500);
            } else {
                toastr.error(data.message || 'An error occurred while saving settings', 'Error');
                
                // Show validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        data.errors[field].forEach(error => {
                            toastr.error(error, 'Validation Error');
                        });
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('An error occurred while saving settings', 'Network Error');
        })
        .finally(() => {
            // Restore button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    // Validate time format inputs
    const timeInputs = document.querySelectorAll('input[pattern="[0-9]{1,2}:[0-9]{2}"]');
    timeInputs.forEach(input => {
        input.addEventListener('blur', function() {
            const pattern = /^[0-9]{1,2}:[0-9]{2}$/;
            if (this.value && !pattern.test(this.value)) {
                toastr.warning('Please enter time in HH:MM format (e.g., 8:00)', 'Invalid Format');
                this.focus();
            }
        });
    });

    // Ensure at least one working day is selected
    const workingDayCheckboxes = document.querySelectorAll('input[name="working_days[]"]');
    workingDayCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedDays = document.querySelectorAll('input[name="working_days[]"]:checked');
            if (checkedDays.length === 0) {
                toastr.warning('At least one working day must be selected', 'Validation Error');
                this.checked = true;
            }
        });
    });
});
</script>
@endsection