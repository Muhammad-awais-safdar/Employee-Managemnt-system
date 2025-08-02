@extends('EmployeeManagemntsystem.Layout.employee')

@section('title', 'My Profile')

@section('content')
<div class="col-lg-9">
    <!-- Breadcrumb -->
    <div class="card mb-4">
        <div class="card-body">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-divide p-0 mb-2">
                    <li class="breadcrumb-item d-flex align-items-center fw-medium">
                        <a href="{{ route('Employee.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active fw-medium" aria-current="page">My Profile</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">My Profile</h4>
                    <p class="text-muted mb-0">Manage your personal information and account settings</p>
                </div>
                <div class="profile-actions">
                    <button class="btn btn-outline-primary btn-sm" onclick="toggleEditMode()">
                        <i class="ti ti-edit me-1"></i><span id="editModeText">Edit Profile</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Overview Card -->
    <div class="row">
        <div class="col-xl-4">
            <div class="card profile-overview-card border-0 shadow-sm">
                <div class="card-body text-center">
                    <!-- Profile Image -->
                    <div class="profile-image-wrapper mb-3">
                        <div class="profile-image-container position-relative">
                            @if($user->profile_image)
                                <img src="{{ asset('storage/' . $user->profile_image) }}" 
                                     alt="Profile Image" 
                                     class="profile-image rounded-circle"
                                     id="profileImagePreview">
                            @else
                                <div class="profile-image-placeholder rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ti ti-user text-muted" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                            <button class="btn btn-sm btn-primary profile-image-edit-btn" 
                                    onclick="triggerImageUpload()" 
                                    style="display: none;">
                                <i class="ti ti-camera"></i>
                            </button>
                        </div>
                        <input type="file" id="profileImageInput" accept="image/*" style="display: none;">
                    </div>

                    <!-- Basic Info -->
                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-2">
                        @if($user->employee_id)
                            ID: {{ $user->employee_id }}
                        @else
                            Employee
                        @endif
                    </p>
                    
                    @if($department)
                        <span class="badge bg-primary mb-3">{{ $department->name }}</span>
                    @endif

                    <!-- Quick Stats -->
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="profile-stat">
                                <h6 class="mb-0">{{ $user->experience_years ?? 0 }}</h6>
                                <small class="text-muted">Years Exp.</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="profile-stat">
                                <h6 class="mb-0">
                                    @if($user->date_of_joining)
                                        {{ $user->date_of_joining->diffInYears(now()) }}
                                    @else
                                        0
                                    @endif
                                </h6>
                                <small class="text-muted">Years Here</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="profile-stat">
                                <h6 class="mb-0">{{ $user->attendances()->count() }}</h6>
                                <small class="text-muted">Attendance</small>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="contact-info mt-3">
                        @if($user->email)
                            <div class="contact-item mb-2">
                                <i class="ti ti-mail text-primary me-2"></i>
                                <small>{{ $user->email }}</small>
                            </div>
                        @endif
                        @if($user->phone)
                            <div class="contact-item mb-2">
                                <i class="ti ti-phone text-success me-2"></i>
                                <small>{{ $user->phone }}</small>
                            </div>
                        @endif
                        @if($company)
                            <div class="contact-item mb-2">
                                <i class="ti ti-building text-info me-2"></i>
                                <small>{{ $company->name }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <!-- Profile Form -->
            <div class="card profile-form-card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0">Personal Information</h6>
                </div>
                <div class="card-body">
                    <form id="profileForm">
                        @csrf
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" value="{{ $user->name }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" value="{{ $user->email }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" name="phone" value="{{ $user->phone }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Employee ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="employee_id" value="{{ $user->employee_id }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" name="date_of_birth" 
                                           value="{{ $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Gender</label>
                                    <select class="form-select" name="gender" disabled>
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ $user->gender === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ $user->gender === 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ $user->gender === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Marital Status</label>
                                    <select class="form-select" name="marital_status" disabled>
                                        <option value="">Select Status</option>
                                        <option value="single" {{ $user->marital_status === 'single' ? 'selected' : '' }}>Single</option>
                                        <option value="married" {{ $user->marital_status === 'married' ? 'selected' : '' }}>Married</option>
                                        <option value="divorced" {{ $user->marital_status === 'divorced' ? 'selected' : '' }}>Divorced</option>
                                        <option value="widowed" {{ $user->marital_status === 'widowed' ? 'selected' : '' }}>Widowed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Department</label>
                                    <select class="form-select" name="department_id" disabled>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ $user->department_id == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" name="address" rows="2" readonly>{{ $user->address }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Professional Information -->
                        <hr>
                        <h6 class="mb-3">Professional Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Date of Joining</label>
                                    <input type="date" class="form-control" name="date_of_joining" 
                                           value="{{ $user->date_of_joining ? $user->date_of_joining->format('Y-m-d') : '' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Qualification</label>
                                    <input type="text" class="form-control" name="qualification" value="{{ $user->qualification }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Experience Years</label>
                                    <input type="number" class="form-control" name="experience_years" 
                                           value="{{ $user->experience_years }}" min="0" max="50" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Salary</label>
                                    <input type="number" class="form-control" name="salary" 
                                           value="{{ $user->salary }}" min="0" step="0.01" readonly>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Skills</label>
                                    <textarea class="form-control" name="skills" rows="2" 
                                              placeholder="e.g., PHP, Laravel, JavaScript, React..." readonly>{{ $user->skills }}</textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Bio</label>
                                    <textarea class="form-control" name="bio" rows="3" 
                                              placeholder="Tell us about yourself..." readonly>{{ $user->bio }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <hr>
                        <h6 class="mb-3">Emergency Contact <span class="text-danger">*</span></h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Contact Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="emergency_contact_name" 
                                           value="{{ $user->emergency_contact_name }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Contact Phone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="emergency_contact_phone" 
                                           value="{{ $user->emergency_contact_phone }}" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions" style="display: none;">
                            <hr>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-secondary" onclick="cancelEdit()">
                                    <i class="ti ti-x me-1"></i>Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-check me-1"></i>Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Sections -->
    <div class="row">
        <!-- Password Change -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0">Change Password</h6>
                </div>
                <div class="card-body">
                    <form id="passwordForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" name="new_password_confirmation" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-lock me-1"></i>Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Settings -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0">Account Settings</h6>
                </div>
                <div class="card-body">
                    <form id="settingsForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Timezone</label>
                            <select class="form-select" name="timezone">
                                <option value="UTC" {{ ($user->settings['timezone'] ?? 'UTC') === 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="Asia/Karachi" {{ ($user->settings['timezone'] ?? '') === 'Asia/Karachi' ? 'selected' : '' }}>Asia/Karachi</option>
                                <option value="America/New_York" {{ ($user->settings['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                                <option value="Europe/London" {{ ($user->settings['timezone'] ?? '') === 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="emailNotifications" 
                                       name="email_settings" {{ ($user->settings['email_settings'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="emailNotifications">
                                    Email Notifications
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="smsNotifications" 
                                       name="sms_settings" {{ ($user->settings['sms_settings'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="smsNotifications">
                                    SMS Notifications
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-settings me-1"></i>Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-overview-card {
    border-radius: 16px;
    border: 1px solid rgba(226, 232, 240, 0.6);
}

.profile-form-card {
    border-radius: 16px;
    border: 1px solid rgba(226, 232, 240, 0.6);
}

.profile-image-container {
    width: 120px;
    height: 120px;
    margin: 0 auto;
}

.profile-image,
.profile-image-placeholder {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border: 4px solid #fff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.profile-image-placeholder {
    background: #f8fafc;
    border: 4px solid #fff;
}

.profile-image-edit-btn {
    position: absolute;
    bottom: 5px;
    right: 5px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.profile-stat {
    padding: 0.5rem 0;
}

.profile-stat h6 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #6366f1;
}

.contact-item {
    display: flex;
    align-items: center;
    text-align: left;
}

.form-control:read-only,
.form-select:disabled {
    background-color: #f8fafc;
    border-color: #e2e8f0;
}

.form-control:not(:read-only),
.form-select:not(:disabled) {
    border-color: #6366f1;
    box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.1);
}

.card-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

/* Animation for edit mode */
.form-control,
.form-select {
    transition: all 0.3s ease;
}

@media (max-width: 768px) {
    .profile-image-container {
        width: 100px;
        height: 100px;
    }
    
    .profile-image,
    .profile-image-placeholder {
        width: 100px;
        height: 100px;
    }
    
    .profile-stat h6 {
        font-size: 1rem;
    }
}
</style>

<script>
let editMode = false;

// Toggle edit mode
function toggleEditMode() {
    editMode = !editMode;
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('input, select, textarea');
    const editBtn = document.querySelector('.profile-image-edit-btn');
    const formActions = document.querySelector('.form-actions');
    const editModeText = document.getElementById('editModeText');
    
    if (editMode) {
        // Enable editing
        inputs.forEach(input => {
            if (input.type !== 'email') { // Keep email readonly for security
                input.removeAttribute('readonly');
                input.removeAttribute('disabled');
            }
        });
        editBtn.style.display = 'block';
        formActions.style.display = 'block';
        editModeText.textContent = 'Cancel Edit';
    } else {
        // Disable editing
        inputs.forEach(input => {
            if (input.tagName === 'SELECT') {
                input.setAttribute('disabled', 'disabled');
            } else {
                input.setAttribute('readonly', 'readonly');
            }
        });
        editBtn.style.display = 'none';
        formActions.style.display = 'none';
        editModeText.textContent = 'Edit Profile';
    }
}

// Cancel edit mode
function cancelEdit() {
    editMode = false;
    location.reload(); // Simple way to restore original values
}

// Trigger image upload
function triggerImageUpload() {
    document.getElementById('profileImageInput').click();
}

// Handle image upload
document.getElementById('profileImageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('profileImagePreview');
            if (preview) {
                preview.src = e.target.result;
            } else {
                // Create image element if placeholder exists
                const placeholder = document.querySelector('.profile-image-placeholder');
                if (placeholder) {
                    placeholder.outerHTML = `<img src="${e.target.result}" alt="Profile Image" class="profile-image rounded-circle" id="profileImagePreview">`;
                }
            }
        };
        reader.readAsDataURL(file);
    }
});

// Profile form submission
document.getElementById('profileForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const imageFile = document.getElementById('profileImageInput').files[0];
    if (imageFile) {
        formData.append('profile_image', imageFile);
    }
    
    try {
        const response = await fetch('{{ route("profile.update") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            toastr.success(data.message, 'Success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            toastr.error(data.message, 'Error');
        }
    } catch (error) {
        console.error('Error:', error);
        toastr.error('An error occurred while updating profile', 'Error');
    }
});

// Password form submission
document.getElementById('passwordForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('{{ route("profile.password.update") }}', {
            method: 'PUT',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            toastr.success(data.message, 'Success');
            this.reset();
        } else {
            toastr.error(data.message, 'Error');
        }
    } catch (error) {
        console.error('Error:', error);
        toastr.error('An error occurred while updating password', 'Error');
    }
});

// Settings form submission
document.getElementById('settingsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('{{ route("profile.settings.update") }}', {
            method: 'PUT',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            toastr.success(data.message, 'Success');
        } else {
            toastr.error(data.message, 'Error');
        }
    } catch (error) {
        console.error('Error:', error);
        toastr.error('An error occurred while updating settings', 'Error');
    }
});
</script>
@endsection