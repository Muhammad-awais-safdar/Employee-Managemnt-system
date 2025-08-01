@props(['department' => null])

<div class="row">
    <!-- Basic Information -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="ti ti-info-circle me-2"></i>Basic Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="name" class="form-label fw-semibold">
                            Department Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', $department->name ?? '') }}" 
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Enter department name"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="description" class="form-label fw-semibold">
                            Description
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="4"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Enter department description">{{ old('description', $department->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Provide a brief description of the department's purpose and responsibilities.</div>
                    </div>

                    @if(auth()->user()->hasRole('superAdmin') && isset($companies) && $companies->count() > 0)
                    <div class="col-md-12 mb-3">
                        <label for="company_id" class="form-label fw-semibold">
                            Company <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-building"></i></span>
                            <select name="company_id" 
                                    id="company_id" 
                                    class="form-select @error('company_id') is-invalid @enderror"
                                    required>
                                <option value="">Select Company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" 
                                            {{ old('company_id', $department->company_id ?? '') == $company->id ? 'selected' : '' }}>
                                        {{ $company->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('company_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Select the company this department belongs to.</div>
                    </div>
                    @endif

                    <div class="col-md-6 mb-3">
                        <label for="location" class="form-label fw-semibold">
                            Location
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-map-pin"></i></span>
                            <input type="text" 
                                   name="location" 
                                   id="location" 
                                   value="{{ old('location', $department->location ?? '') }}" 
                                   class="form-control @error('location') is-invalid @enderror"
                                   placeholder="Enter department location">
                        </div>
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label fw-semibold">
                            Status
                        </label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="status"ss
                                   value="1"
                                   {{ old('status', $department->status ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="status">
                                <span class="status-text">{{ old('status', $department->status ?? true) ? 'Active' : 'Inactive' }}</span>
                            </label>
                        </div>
                        <div class="form-text">Toggle to activate or deactivate this department.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="ti ti-address-book me-2"></i>Contact Information
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="phone" class="form-label fw-semibold">
                        Phone Number
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="ti ti-phone"></i></span>
                        <input type="tel" 
                               name="phone" 
                               id="phone" 
                               value="{{ old('phone', $department->phone ?? '') }}" 
                               class="form-control @error('phone') is-invalid @enderror"
                               placeholder="+1 (555) 123-4567">
                    </div>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">
                        Email Address
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="ti ti-mail"></i></span>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email', $department->email ?? '') }}" 
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="department@company.com">
                    </div>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if(isset($department) && $department->exists)
                <div class="border-top pt-3 mt-4">
                    <h6 class="fw-semibold mb-3">Department Statistics</h6>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Company:</span>
                        <small class="text-muted">{{ $department->company->company_name ?? 'N/A' }}</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Total Users:</span>
                        <span class="badge bg-info-subtle text-info">{{ $department->users_count ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Created:</span>
                        <small class="text-muted">{{ $department->formatted_created_at }}</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Last Updated:</span>
                        <small class="text-muted">{{ $department->formatted_updated_at }}</small>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Form Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('Admin.departments.index') }}" 
                       class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-2"></i>Back to Departments
                    </a>
                    <div class="d-flex gap-2">
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="ti ti-refresh me-2"></i>Reset
                        </button>
                        <button type="submit" class="btn btn-purple">
                            <i class="ti ti-device-floppy me-2"></i>
                            {{ isset($department) && $department->exists ? 'Update Department' : 'Create Department' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status toggle text update
    const statusToggle = document.getElementById('status');
    const statusText = document.querySelector('.status-text');
    
    if (statusToggle && statusText) {
        statusToggle.addEventListener('change', function() {
            statusText.textContent = this.checked ? 'Active' : 'Inactive';
        });
    }

    // Phone number formatting
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 6) {
                value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
            } else if (value.length >= 3) {
                value = value.replace(/(\d{3})(\d{0,3})/, '($1) $2');
            }
            this.value = value;
        });
    }

    // Form validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const nameInput = document.getElementById('name');
            if (!nameInput.value.trim()) {
                e.preventDefault();
                nameInput.focus();
                nameInput.classList.add('is-invalid');
                
                // Create or update error message
                let errorDiv = nameInput.nextElementSibling;
                if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    nameInput.parentNode.insertBefore(errorDiv, nameInput.nextSibling);
                }
                errorDiv.textContent = 'Department name is required.';
            }
        });
    }
});
</script>
@endpush

<style>
.card-header {
    border-bottom: 1px solid #e3e6f0;
}

.form-check-input:checked {
    background-color: #3E007C;
    border-color: #3E007C;
}

.form-check-input:focus {
    border-color: #3E007C;
    box-shadow: 0 0 0 0.25rem rgba(62, 0, 124, 0.25);
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #e3e6f0;
    color: #6c757d;
}

.form-control:focus {
    border-color: #3E007C;
    box-shadow: 0 0 0 0.25rem rgba(62, 0, 124, 0.25);
}

.btn-purple {
    background-color: #3E007C;
    border-color: #3E007C;
    color: white;
}

.btn-purple:hover {
    background-color: #7100E2;
    border-color: #7100E2;
    color: white;
}

.btn-outline-purple {
    color: #3E007C;
    border-color: #3E007C;
}

.btn-outline-purple:hover {
    background-color: #3E007C;
    border-color: #3E007C;
    color: white;
}
</style>