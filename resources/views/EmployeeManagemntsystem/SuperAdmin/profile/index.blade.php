@extends('EmployeeManagemntsystem.Layout.App')

@section('title', 'SuperAdmin Profile')

@section('content')
<div class="page-wrapper">
    <!-- Page Content -->
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3 class="page-title">SuperAdmin Profile</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('superAdmin.dashboard') }}">Dashboard</a>
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
                <div class="card profile-card superadmin-card">
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
                                    <div class="profile-image-placeholder rounded-circle d-flex align-items-center justify-content-center bg-gradient text-white">
                                        <i class="ti ti-crown" style="font-size: 3rem;"></i>
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

                        <!-- SuperAdmin Info -->
                        <h4 class="mb-1 text-primary">{{ $user->name }}</h4>
                        <p class="text-muted mb-2">System Administrator</p>
                        <span class="badge bg-gradient-primary mb-3">SuperAdmin</span>

                        <!-- System Overview -->
                        <div class="system-overview mb-4">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="overview-stat">
                                        <h4 class="text-primary mb-0">{{ $totalCompanies }}</h4>
                                        <small class="text-muted">Companies</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="overview-stat">
                                        <h4 class="text-success mb-0">{{ $totalUsers }}</h4>
                                        <small class="text-muted">Total Users</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="contact-info">
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
                            @if($user->twitter_url)
                                <div class="contact-item mb-2">
                                    <i class="ti ti-brand-twitter text-primary me-2"></i>
                                    <a href="{{ $user->twitter_url }}" target="_blank" class="text-decoration-none">Twitter</a>
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
                        <h5 class="card-title mb-0">SuperAdmin Profile Information</h5>
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
                                        <label class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control" name="date_of_birth" 
                                               value="{{ $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '' }}" readonly>
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
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Bio</label>
                                        <textarea class="form-control" name="bio" rows="4" 
                                                  placeholder="Tell us about your role, experience, and vision for the platform..." readonly>{{ $user->bio }}</textarea>
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

        <!-- System Management Tools -->
        <div class="row">
            <!-- Password Change -->
            <div class="col-xl-4">
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
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">System Settings</h5>
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
                                           name="email_notifications" {{ ($user->settings['email_notifications'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="emailNotifications">
                                        System Email Notifications
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

            <!-- Quick SuperAdmin Actions -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">System Management</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('superAdmin.company.index') }}" class="btn btn-outline-primary">
                                <i class="ti ti-building-community me-2"></i>Manage Companies
                            </a>
                            <a href="{{ route('superAdmin.users.index') }}" class="btn btn-outline-success">
                                <i class="ti ti-users-group me-2"></i>Manage Users
                            </a>
                            <a href="{{ route('superAdmin.notifications.index') }}" class="btn btn-outline-warning">
                                <i class="ti ti-bell me-2"></i>System Notifications
                            </a>
                            <button class="btn btn-outline-info" onclick="viewSystemLogs()">
                                <i class="ti ti-file-text me-2"></i>System Logs
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Companies Overview -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Companies Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Company Name</th>
                                        <th>Industry</th>
                                        <th>Employees</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($companies as $company)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="company-icon bg-primary bg-opacity-10 text-primary me-2">
                                                        <i class="ti ti-building"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $company->name }}</h6>
                                                        <small class="text-muted">{{ $company->email ?? 'No email' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $company->industry ?? 'Not specified' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $company->users->count() }} users</span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $company->is_active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $company->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>{{ $company->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        Actions
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="{{ route('superAdmin.company.show', $company) }}">View Details</a></li>
                                                        <li><a class="dropdown-item" href="{{ route('superAdmin.company.edit', $company) }}">Edit</a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item text-danger" href="#" onclick="toggleCompanyStatus({{ $company->id }})">
                                                            {{ $company->is_active ? 'Deactivate' : 'Activate' }}
                                                        </a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <i class="ti ti-building-off text-muted" style="font-size: 2rem;"></i>
                                                <p class="text-muted mt-2">No companies found</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
.superadmin-card {
    border: none;
    box-shadow: 0 8px 24px rgba(99, 102, 241, 0.15);
    border-radius: 16px;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(168, 85, 247, 0.05) 100%);
}

.bg-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.profile-image-container {
    width: 140px;
    height: 140px;
    margin: 0 auto;
}

.profile-image,
.profile-image-placeholder {
    width: 140px;
    height: 140px;
    object-fit: cover;
    border: 6px solid #fff;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.profile-image-edit-btn {
    position: absolute;
    bottom: 5px;
    right: 5px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.overview-stat {
    padding: 1rem 0;
}

.overview-stat h4 {
    font-size: 2rem;
    font-weight: 700;
}

.system-overview {
    padding: 1.5rem;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(168, 85, 247, 0.1) 100%);
    border-radius: 12px;
    margin-bottom: 1rem;
}

.contact-info .contact-item {
    display: flex;
    align-items: center;
    text-align: left;
    margin-bottom: 0.75rem;
    padding: 0.5rem;
    background: rgba(255, 255, 255, 0.5);
    border-radius: 8px;
}

.section-header h6 {
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 1.1rem;
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
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border-radius: 12px;
}

.card-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-bottom: 1px solid #e2e8f0;
    border-radius: 12px 12px 0 0;
}

.company-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.btn-outline-primary:hover,
.btn-outline-success:hover,
.btn-outline-info:hover,
.btn-outline-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    .overview-stat h4 {
        font-size: 1.5rem;
    }
    
    .profile-image-container {
        width: 120px;
        height: 120px;
    }
    
    .profile-image,
    .profile-image-placeholder {
        width: 120px;
        height: 120px;
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

// View system logs
function viewSystemLogs() {
    alert('System logs functionality will be implemented soon.');
}

// Toggle company status
function toggleCompanyStatus(companyId) {
    if (!confirm('Are you sure you want to change the company status?')) return;
    
    // Implementation for toggling company status
    alert('Company status toggle functionality will be implemented.');
}

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