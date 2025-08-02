@extends('EmployeeManagemntsystem.Layout.App')

@section('title', 'Admin Profile')

@section('content')

        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3 class="page-title">My Profile</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('Admin.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ul>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-primary" onclick="toggleEditMode()">
                        <i class="ti ti-edit me-1"></i><span id="editModeText">Edit Profile</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Profile Overview -->
        <div class="row">
            <div class="col-xl-4">
                <div class="card profile-card">
                    <div class="card-body text-center">
                        <!-- Profile Image -->
                        <div class="profile-image-wrapper mb-4">
                            <div class="profile-image-container position-relative">
                                @if($user->profile_image)
                                    <img src="{{ asset('storage/' . $user->profile_image) }}" 
                                         alt="Profile Image" 
                                         class="profile-image rounded-circle"
                                         id="profileImagePreview">
                                @else
                                    <div class="profile-image-placeholder rounded-circle d-flex align-items-center justify-content-center bg-primary text-white">
                                        <i class="ti ti-user" style="font-size: 3rem;"></i>
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

                        <!-- Admin Info -->
                        <h4 class="mb-1">{{ $user->name }}</h4>
                        <p class="text-muted mb-2">Administrator</p>
                        @if($user->employee_id)
                            <span class="badge bg-primary mb-3">ID: {{ $user->employee_id }}</span>
                        @endif

                        <!-- Company Info -->
                        @if($company)
                            <div class="company-info mb-4">
                                <h6 class="text-primary mb-2">{{ $company->name }}</h6>
                                <p class="text-muted small mb-0">{{ $company->industry ?? 'Technology' }}</p>
                                @if($company->address)
                                    <p class="text-muted small">{{ $company->address }}</p>
                                @endif
                            </div>
                        @endif

                        <!-- Admin Stats -->
                        <div class="row text-center admin-stats">
                            <div class="col-4">
                                <div class="stat-item">
                                    <h5 class="text-primary mb-0">{{ $companyUsers }}</h5>
                                    <small class="text-muted">Employees</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <h5 class="text-success mb-0">{{ $departments->count() }}</h5>
                                    <small class="text-muted">Departments</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <h5 class="text-info mb-0">
                                        @if($user->date_of_joining)
                                            {{ $user->date_of_joining->diffInYears(now()) }}
                                        @else
                                            0
                                        @endif
                                    </h5>
                                    <small class="text-muted">Years</small>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="contact-info mt-4">
                            @if($user->email)
                                <div class="contact-item mb-2">
                                    <i class="ti ti-mail text-primary me-2"></i>
                                    <span>{{ $user->email }}</span>
                                </div>
                            @endif
                            @if($user->phone)
                                <div class="contact-item mb-2">
                                    <i class="ti ti-phone text-success me-2"></i>
                                    <span>{{ $user->phone }}</span>
                                </div>
                            @endif
                            @if($user->linkedin_url)
                                <div class="contact-item mb-2">
                                    <i class="ti ti-brand-linkedin text-info me-2"></i>
                                    <a href="{{ $user->linkedin_url }}" target="_blank" class="text-decoration-none">LinkedIn</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <!-- Profile Form -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <form id="profileForm">
                            @csrf
                            
                            <!-- Basic Information -->
                            <div class="section-header mb-3">
                                <h6 class="text-primary">Basic Information</h6>
                                <hr>
                            </div>
                            
                            <div class="row">
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
                                        <label class="form-label">Employee ID</label>
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
                                        <label class="form-label">Date of Joining</label>
                                        <input type="date" class="form-control" name="date_of_joining" 
                                               value="{{ $user->date_of_joining ? $user->date_of_joining->format('Y-m-d') : '' }}" readonly>
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
                            <div class="section-header mb-3 mt-4">
                                <h6 class="text-primary">Professional Information</h6>
                                <hr>
                            </div>
                            
                            <div class="row">
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
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Bio</label>
                                        <textarea class="form-control" name="bio" rows="3" 
                                                  placeholder="Tell us about your role and experience..." readonly>{{ $user->bio }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">LinkedIn URL</label>
                                        <input type="url" class="form-control" name="linkedin_url" value="{{ $user->linkedin_url }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Twitter URL</label>
                                        <input type="url" class="form-control" name="twitter_url" value="{{ $user->twitter_url }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Emergency Contact -->
                            <div class="section-header mb-3 mt-4">
                                <h6 class="text-primary">Emergency Contact</h6>
                                <hr>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Contact Name</label>
                                        <input type="text" class="form-control" name="emergency_contact_name" 
                                               value="{{ $user->emergency_contact_name }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Contact Phone</label>
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

        <!-- Additional Settings -->
        <div class="row">
            <!-- Password Change -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Change Password</h5>
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

            <!-- System Settings -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Admin Settings</h5>
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
                                <label class="form-label">Language</label>
                                <select class="form-select" name="language">
                                    <option value="en" {{ ($user->settings['language'] ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
                                    <option value="ur" {{ ($user->settings['language'] ?? '') === 'ur' ? 'selected' : '' }}>Urdu</option>
                                    <option value="ar" {{ ($user->settings['language'] ?? '') === 'ar' ? 'selected' : '' }}>Arabic</option>
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


<style>
.profile-card {
    border: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
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

.admin-stats .stat-item {
    padding: 1rem 0;
}

.admin-stats h5 {
    font-size: 1.5rem;
    font-weight: 700;
}

.company-info {
    padding: 1rem;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(168, 85, 247, 0.1) 100%);
    border-radius: 8px;
}

.contact-info .contact-item {
    display: flex;
    align-items: center;
    text-align: left;
    margin-bottom: 0.5rem;
}

.section-header h6 {
    margin-bottom: 0.5rem;
    font-weight: 600;
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

.card {
    border: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border-radius: 8px;
}

.card-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-bottom: 1px solid #e2e8f0;
}

@media (max-width: 768px) {
    .admin-stats {
        text-align: center;
    }
    
    .admin-stats .stat-item {
        padding: 0.5rem 0;
    }
    
    .admin-stats h5 {
        font-size: 1.25rem;
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
                    placeholder.outerHTML = `<img src={{ asset('${e.target.result}') }} alt="Profile Image" class="profile-image rounded-circle" id="profileImagePreview">`;
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