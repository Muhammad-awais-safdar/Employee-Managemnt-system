@extends('EmployeeManagemntsystem.Layout.App')

@section('content')
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div>
                <h6 class="mb-3 fs-14">
                    <a href="{{ route('superAdmin.company.index') }}" class="text-decoration-none">
                        <i class="ti ti-arrow-left me-1"></i>Back to Companies
                    </a>
                </h6>

                <!-- Alert Messages -->
                <div id="alert-container"></div>

                <div class="card rounded-0">
                    <div class="card-header">
                        <h5 class="fw-bold mb-0">Add New Company</h5>
                    </div>

                    <form id="company-form" enctype="multipart/form-data">
                        @csrf

                        <div class="card rounded-0">
                            <div class="card-header">
                                <h6 class="fw-bold mb-0">Basic Details</h6>
                            </div>
                            <div class="card-body">

                                <!-- Logo Upload -->
                                <div class="mb-4">
                                    <label class="form-label">Company Logo</label>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar avatar-xxl border border-dashed bg-light me-3 flex-shrink-0"
                                            id="logo-preview">
                                            <i class="ti ti-photo text-primary"></i>
                                        </div>
                                        <div class="d-inline-flex flex-column align-items-start">
                                            <div class="drag-upload-btn btn btn-sm btn-primary position-relative mb-2">
                                                <i class="ti ti-photo me-1"></i>Upload Logo
                                                <input type="file" name="logo" id="logo"
                                                    class="form-control position-absolute top-0 start-0 w-100 h-100 opacity-0"
                                                    accept="image/*">
                                            </div>
                                            <span class="text-dark fs-12">JPG or PNG format, max 2MB.</span>
                                            <div class="invalid-feedback d-block" id="logo-error"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Admin Assignment -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Assign to Admin <span
                                                    class="text-danger">*</span></label>
                                            <select name="admin_user_id" id="admin_user_id" class="form-select" required>
                                                <option value="">-- Select Admin --</option>
                                                @forelse ($adminUsers as $admin)
                                                    <option value="{{ $admin->id }}">
                                                        {{ $admin->name }} ({{ $admin->email }})
                                                    </option>
                                                @empty
                                                    <option disabled>No available admins (all admins already have companies)</option>
                                                @endforelse
                                            </select>
                                            @if($adminUsers->isEmpty())
                                                <small class="text-warning">
                                                    <i class="ti ti-info-circle me-1"></i>
                                                    All admin users already have companies assigned. Each admin can only have one company.
                                                </small>
                                            @endif
                                            <div class="invalid-feedback" id="admin_user_id-error"></div>
                                        </div>
                                    </div>

                                    <!-- Company Name -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Company Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="company_name" id="company_name" class="form-control"
                                                required>
                                            <div class="invalid-feedback" id="company_name-error"></div>
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" name="email" id="email" class="form-control" required>
                                            <div class="invalid-feedback" id="email-error"></div>
                                        </div>
                                    </div>

                                    <!-- Phone -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Phone Number</label>
                                            <input type="text" name="phone" id="phone" class="form-control">
                                            <div class="invalid-feedback" id="phone-error"></div>
                                        </div>
                                    </div>

                                    <!-- Contact Person -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Contact Person</label>
                                            <input type="text" name="contact_person" id="contact_person"
                                                class="form-control">
                                            <div class="invalid-feedback" id="contact_person-error"></div>
                                        </div>
                                    </div>

                                    <!-- Website -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Website</label>
                                            <input type="url" name="website" id="website" class="form-control"
                                                placeholder="https://example.com">
                                            <div class="invalid-feedback" id="website-error"></div>
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Status <span class="text-danger">*</span></label>
                                            <select name="status" id="status" class="form-select" required>
                                                <option value="active" selected>Active</option>
                                                <option value="inactive">Inactive</option>
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
                                            <textarea name="address" id="address" class="form-control" rows="3"></textarea>
                                            <div class="invalid-feedback" id="address-error"></div>
                                        </div>
                                    </div>

                                    <!-- City -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">City</label>
                                            <input type="text" name="city" id="city" class="form-control">
                                            <div class="invalid-feedback" id="city-error"></div>
                                        </div>
                                    </div>

                                    <!-- State -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">State</label>
                                            <input type="text" name="state" id="state" class="form-control">
                                            <div class="invalid-feedback" id="state-error"></div>
                                        </div>
                                    </div>

                                    <!-- Country -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Country</label>
                                            <input type="text" name="country" id="country" class="form-control">
                                            <div class="invalid-feedback" id="country-error"></div>
                                        </div>
                                    </div>

                                    <!-- Postal Code -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Postal Code</label>
                                            <input type="text" name="postal_code" id="postal_code"
                                                class="form-control">
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
                                    <textarea name="notes" id="notes" class="form-control" rows="4"
                                        placeholder="Any additional notes about the company..."></textarea>
                                    <div class="invalid-feedback" id="notes-error"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex align-items-center justify-content-end">
                            <button type="button" class="btn btn-light me-2"
                                onclick="window.history.back()">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <span class="spinner-border spinner-border-sm me-1 d-none" id="loading-spinner"></span>
                                Add Company
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
            const form = document.getElementById('company-form');
            const submitBtn = document.getElementById('submit-btn');
            const loadingSpinner = document.getElementById('loading-spinner');
            const alertContainer = document.getElementById('alert-container');

            // Real-time validation
            const validationFields = ['admin_user_id', 'company_name', 'email', 'phone', 'website', 'city', 'state',
                'country', 'postal_code', 'contact_person'
            ];

            validationFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (field) {
                    field.addEventListener('blur', function() {
                        validateField(fieldName, this.value);
                    });
                }
            });

            // Logo preview
            document.getElementById('logo').addEventListener('change', function(e) {
                const file = e.target.files[0];
                const preview = document.getElementById('logo-preview');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML =
                            `<img src={{ asset('${e.target.result}') }} class="img-fluid rounded" style="max-width: 100%; max-height: 100px;">`;
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.innerHTML = '<i class="ti ti-photo text-primary"></i>';
                }
            });

            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(form);

                // Show loading state
                submitBtn.disabled = true;
                loadingSpinner.classList.remove('d-none');

                // Clear previous errors
                clearErrors();

                fetch('{{ route('superAdmin.company.store') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('success', data.message);
                            form.reset();
                            document.getElementById('logo-preview').innerHTML =
                                '<i class="ti ti-photo text-primary"></i>';

                            // Redirect after success
                            setTimeout(() => {
                                window.location.href =
                                '{{ route('superAdmin.company.index') }}';
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
            function validateField(fieldName, value) {
                fetch('{{ route('superAdmin.company.validate-field') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            field: fieldName,
                            value: value
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
