@extends('EmployeeManagemntsystem.Layout.App')

@section('content')
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div>
                <h6 class="mb-3 fs-14">
                    @if(Auth::user()->hasRole('superAdmin'))
                        <a href="{{ route('superAdmin.company.index') }}" class="text-decoration-none">
                            <i class="ti ti-arrow-left me-1"></i>Back to Companies
                        </a>
                    @else
                        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                            <i class="ti ti-arrow-left me-1"></i>Back to Dashboard
                        </a>
                    @endif
                </h6>
                
                <!-- Alert Messages -->
                <div id="alert-container"></div>
                
                <div class="card rounded-0">
                    <div class="card-header">
                        <h5 class="fw-bold mb-0">
                            @if(Auth::user()->hasRole('superAdmin'))
                                Edit Company
                            @else
                                Edit My Company
                            @endif
                        </h5>
                    </div>
                    
                    <form id="company-edit-form" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="company-id" value="{{ $company->id }}">

                        <div class="card rounded-0">
                            <div class="card-header">
                                <h6 class="fw-bold mb-0">Basic Details</h6>
                            </div>
                            <div class="card-body">

                                <!-- Logo Upload -->
                                <div class="mb-4">
                                    <label class="form-label">Company Logo</label>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar avatar-xxl border border-dashed bg-light me-3 flex-shrink-0" id="logo-preview">
                                            @if($company->logo)
                                                <img src="{{ asset('storage/' . $company->logo) }}" class="img-fluid rounded" style="max-width: 100%; max-height: 100px;">
                                            @else
                                                <i class="ti ti-photo text-primary"></i>
                                            @endif
                                        </div>
                                        <div class="d-inline-flex flex-column align-items-start">
                                            <div class="drag-upload-btn btn btn-sm btn-primary position-relative mb-2">
                                                <i class="ti ti-photo me-1"></i>Upload Logo
                                                <input type="file" name="logo" id="logo" class="form-control position-absolute top-0 start-0 w-100 h-100 opacity-0" accept="image/*">
                                            </div>
                                            @if($company->logo)
                                                <label class="form-check mb-2">
                                                    <input type="checkbox" name="remove_logo" id="remove_logo" class="form-check-input">
                                                    <span class="form-check-label">Remove current logo</span>
                                                </label>
                                            @endif
                                            <span class="text-dark fs-12">JPG or PNG format, max 2MB.</span>
                                            <div class="invalid-feedback d-block" id="logo-error"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    @if(Auth::user()->hasRole('superAdmin') && $adminUsers->isNotEmpty())
                                        <!-- Admin Assignment (SuperAdmin only) -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Assign to Admin</label>
                                                <select name="admin_user_id" id="admin_user_id" class="form-select">
                                                    <option value="">-- Keep Current Admin --</option>
                                                    @foreach($adminUsers as $admin)
                                                        <option value="{{ $admin->id }}" {{ $company->user_id == $admin->id ? 'selected' : '' }}>
                                                            {{ $admin->name }} ({{ $admin->email }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback" id="admin_user_id-error"></div>
                                            </div>
                                        </div>
                                        
                                        <!-- Company Name -->
                                        <div class="col-md-6">
                                    @else
                                        <!-- Company Name (Full width for admin users) -->
                                        <div class="col-md-12">
                                    @endif
                                        <div class="mb-3">
                                            <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                            <input type="text" name="company_name" id="company_name" class="form-control" value="{{ $company->company_name }}" required>
                                            <div class="invalid-feedback" id="company_name-error"></div>
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" name="email" id="email" class="form-control" value="{{ $company->email }}" required>
                                            <div class="invalid-feedback" id="email-error"></div>
                                        </div>
                                    </div>

                                    <!-- Phone -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Phone Number</label>
                                            <input type="text" name="phone" id="phone" class="form-control" value="{{ $company->phone }}">
                                            <div class="invalid-feedback" id="phone-error"></div>
                                        </div>
                                    </div>

                                    <!-- Contact Person -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Contact Person</label>
                                            <input type="text" name="contact_person" id="contact_person" class="form-control" value="{{ $company->contact_person }}">
                                            <div class="invalid-feedback" id="contact_person-error"></div>
                                        </div>
                                    </div>

                                    <!-- Website -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Website</label>
                                            <input type="url" name="website" id="website" class="form-control" value="{{ $company->website }}" placeholder="https://example.com">
                                            <div class="invalid-feedback" id="website-error"></div>
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Status <span class="text-danger">*</span></label>
                                            <select name="status" id="status" class="form-select" required>
                                                <option value="active" {{ $company->status == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ $company->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                            <div class="invalid-feedback" id="status-error"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Details -->
                        <div class="card rounded-0">
                            <div class="card-header">
                                <h6 class="fw-bold mb-0">Address Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Address -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Address</label>
                                            <textarea name="address" id="address" class="form-control" rows="3">{{ $company->address }}</textarea>
                                            <div class="invalid-feedback" id="address-error"></div>
                                        </div>
                                    </div>

                                    <!-- City -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">City</label>
                                            <input type="text" name="city" id="city" class="form-control" value="{{ $company->city }}">
                                            <div class="invalid-feedback" id="city-error"></div>
                                        </div>
                                    </div>

                                    <!-- State -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">State</label>
                                            <input type="text" name="state" id="state" class="form-control" value="{{ $company->state }}">
                                            <div class="invalid-feedback" id="state-error"></div>
                                        </div>
                                    </div>

                                    <!-- Country -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Country</label>
                                            <input type="text" name="country" id="country" class="form-control" value="{{ $company->country }}">
                                            <div class="invalid-feedback" id="country-error"></div>
                                        </div>
                                    </div>

                                    <!-- Postal Code -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Postal Code</label>
                                            <input type="text" name="postal_code" id="postal_code" class="form-control" value="{{ $company->postal_code }}">
                                            <div class="invalid-feedback" id="postal_code-error"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="card rounded-0">
                            <div class="card-header">
                                <h6 class="fw-bold mb-0">Additional Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="4" placeholder="Any additional notes about the company...">{{ $company->notes }}</textarea>
                                    <div class="invalid-feedback" id="notes-error"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex align-items-center justify-content-end">
                            <button type="button" class="btn btn-light me-2" onclick="window.history.back()">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <span class="spinner-border spinner-border-sm me-1 d-none" id="loading-spinner"></span>
                                Update Company
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for AJAX and Real-time Validation -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('company-edit-form');
        const submitBtn = document.getElementById('submit-btn');
        const loadingSpinner = document.getElementById('loading-spinner');
        const alertContainer = document.getElementById('alert-container');
        const companyId = document.getElementById('company-id').value;
        
        // Real-time validation
        const validationFields = ['admin_user_id', 'company_name', 'email', 'phone', 'website', 'city', 'state', 'country', 'postal_code', 'contact_person'];
        
        validationFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field) {
                field.addEventListener('blur', function() {
                    validateField(fieldName, this.value, companyId);
                });
            }
        });

        // Logo preview and removal
        document.getElementById('logo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('logo-preview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded" style="max-width: 100%; max-height: 100px;">`;
                };
                reader.readAsDataURL(file);
                
                // Uncheck remove logo if new file is selected
                const removeCheckbox = document.getElementById('remove_logo');
                if (removeCheckbox) {
                    removeCheckbox.checked = false;
                }
            }
        });

        // Handle logo removal checkbox
        const removeLogoCheckbox = document.getElementById('remove_logo');
        if (removeLogoCheckbox) {
            removeLogoCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    document.getElementById('logo-preview').innerHTML = '<i class="ti ti-photo text-primary"></i>';
                    document.getElementById('logo').value = '';
                } else {
                    // Restore original logo preview if unchecked
                    @if($company->logo)
                        document.getElementById('logo-preview').innerHTML = '<img src="{{ asset('storage/' . $company->logo) }}" class="img-fluid rounded" style="max-width: 100%; max-height: 100px;">';
                    @endif
                }
            });
        }

        // Form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            
            // Show loading state
            submitBtn.disabled = true;
            loadingSpinner.classList.remove('d-none');
            
            // Clear previous errors
            clearErrors();
            
            fetch('@if(Auth::user()->hasRole('superAdmin')){{ route("superAdmin.company.update", $company->id) }}@else{{ route("admin.company.update") }}@endif', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    
                    // Redirect after success
                    setTimeout(() => {
                        @if(Auth::user()->hasRole('superAdmin'))
                            window.location.href = '{{ route("superAdmin.company.index") }}';
                        @else
                            window.location.href = '{{ route("admin.dashboard") }}';
                        @endif
                    }, 1500);
                } else {
                    if (data.errors) {
                        displayErrors(data.errors);
                    } else {
                        showAlert('danger', data.message || 'An error occurred');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An unexpected error occurred');
            })
            .finally(() => {
                submitBtn.disabled = false;
                loadingSpinner.classList.add('d-none');
            });
        });

        // Real-time field validation
        function validateField(fieldName, value, companyId) {
            fetch('@if(Auth::user()->hasRole('superAdmin')){{ route("superAdmin.company.validate-field") }}@else{{ route("admin.company.validate-field") }}@endif', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    field: fieldName,
                    value: value,
                    company_id: companyId
                })
            })
            .then(response => response.json())
            .then(data => {
                const field = document.getElementById(fieldName);
                const errorDiv = document.getElementById(fieldName + '-error');
                
                if (data.valid) {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                    errorDiv.textContent = '';
                } else {
                    field.classList.remove('is-valid');
                    field.classList.add('is-invalid');
                    errorDiv.textContent = data.message;
                }
            })
            .catch(error => {
                console.error('Validation error:', error);
            });
        }

        // Display form errors
        function displayErrors(errors) {
            Object.keys(errors).forEach(field => {
                const fieldElement = document.getElementById(field);
                const errorDiv = document.getElementById(field + '-error');
                
                if (fieldElement && errorDiv) {
                    fieldElement.classList.add('is-invalid');
                    errorDiv.textContent = errors[field][0];
                }
            });
        }

        // Clear all errors
        function clearErrors() {
            const invalidFields = document.querySelectorAll('.is-invalid');
            invalidFields.forEach(field => {
                field.classList.remove('is-invalid');
            });
            
            const errorDivs = document.querySelectorAll('[id$="-error"]');
            errorDivs.forEach(div => {
                div.textContent = '';
            });
        }

        // Show alert messages
        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            alertContainer.innerHTML = alertHtml;
            
            // Auto dismiss after 5 seconds for success messages
            if (type === 'success') {
                setTimeout(() => {
                    const alert = alertContainer.querySelector('.alert');
                    if (alert) {
                        alert.remove();
                    }
                }, 5000);
            }
        }
    });
    </script>
@endsection