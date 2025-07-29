@extends('EmployeeManagemntsystem.Layout.App')
@section('content')
    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
        <div class="flex-grow-1">
            <h5 class="fw-bold">Companies</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-divide p-0 mb-0">
                    <li class="breadcrumb-item d-flex align-items-center">
                        <a href="{{ route('superAdmin.dashboard') }}">
                            <i class="ti ti-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Companies</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('superAdmin.company.create') }}" class="btn btn-primary">
                <i class="ti ti-circle-plus me-1"></i>New Company
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    <div id="alert-container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
        <div class="datatable-search">
            <div class="input-group">
                <span class="input-group-text"><i class="ti ti-search"></i></span>
                <input type="text" class="form-control" id="search-companies" placeholder="Search companies...">
            </div>
        </div>
        <div class="d-flex align-items-center">
            <div class="dropdown me-2">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="ti ti-filter me-1"></i>Filter by Status
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item status-filter" href="#" data-status="all">All</a></li>
                    <li><a class="dropdown-item status-filter" href="#" data-status="active">Active</a></li>
                    <li><a class="dropdown-item status-filter" href="#" data-status="inactive">Inactive</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-nowrap" id="companies-table">
            <thead class="thead-light">
                <tr>
                    <th>Logo</th>
                    <th>Company Name</th>
                    <th>Contact Person</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                <tr id="company-row-{{ $company->id }}">
                    <td>
                        <div class="avatar avatar-sm rounded-circle bg-light border">
                            @if($company->logo)
                                <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->company_name }}" class="w-100 h-100 object-fit-cover rounded-circle">
                            @else
                                <div class="d-flex align-items-center justify-content-center w-100 h-100 bg-light text-muted">
                                    <i class="ti ti-building fs-5"></i>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div>
                            <h6 class="mb-0">
                                <a href="{{ route('superAdmin.company.show', $company->id) }}" class="text-dark fw-medium">
                                    {{ $company->company_name }}
                                </a>
                            </h6>
                            @if($company->website)
                                <small class="text-muted">
                                    <a href="{{ $company->website }}" target="_blank" class="text-decoration-none">
                                        <i class="ti ti-external-link me-1"></i>{{ $company->website }}
                                    </a>
                                </small>
                            @endif
                        </div>
                    </td>
                    <td>{{ $company->contact_person ?: '-' }}</td>
                    <td>
                        @if($company->user)
                            <a href="mailto:{{ $company->user->email }}" class="text-decoration-none">{{ $company->user->email }}</a>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($company->phone)
                            <a href="tel:{{ $company->phone }}" class="text-decoration-none">{{ $company->phone }}</a>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($company->city || $company->country)
                            <small class="text-muted">
                                {{ $company->city }}{{ $company->city && $company->country ? ', ' : '' }}{{ $company->country }}
                            </small>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input status-toggle" type="checkbox" 
                                   data-company-id="{{ $company->id }}" 
                                   {{ $company->status == 'active' ? 'checked' : '' }}>
                            <label class="form-check-label status-label-{{ $company->id }}">
                                {{ ucfirst($company->status) }}
                            </label>
                        </div>
                    </td>
                    <td>
                        <div class="d-inline-flex align-items-center">
                            <a href="{{ route('superAdmin.company.show', $company->id) }}" 
                               class="btn btn-icon btn-sm btn-outline-primary me-1" 
                               title="View Details">
                                <i class="ti ti-eye"></i>
                            </a>
                            <a href="{{ route('superAdmin.company.edit', $company->id) }}" 
                               class="btn btn-icon btn-sm btn-outline-secondary me-1" 
                               title="Edit Company">
                                <i class="ti ti-edit"></i>
                            </a>
                            <button type="button" 
                                    class="btn btn-icon btn-sm btn-outline-danger delete-company" 
                                    data-company-id="{{ $company->id }}" 
                                    data-company-name="{{ $company->company_name }}"
                                    title="Delete Company">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="text-muted">
                            <i class="ti ti-building fs-1 d-block mb-2"></i>
                            <p class="mb-2">No companies found</p>
                            <a href="{{ route('superAdmin.company.create') }}" class="btn btn-primary btn-sm">
                                <i class="ti ti-plus me-1"></i>Add First Company
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($companies->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $companies->links() }}
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="ti ti-trash text-danger" style="font-size: 48px;"></i>
                        <h5 class="mt-3">Are you sure?</h5>
                        <p class="text-muted">Do you want to delete company "<span id="company-name-to-delete"></span>"?</p>
                        <p class="text-muted small">This action cannot be undone.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete">
                        <span class="spinner-border spinner-border-sm me-1 d-none" id="delete-spinner"></span>
                        Delete Company
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let companyToDelete = null;
        
        // Status Toggle Functionality
        document.querySelectorAll('.status-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const companyId = this.dataset.companyId;
                const newStatus = this.checked ? 'active' : 'inactive';
                const label = document.querySelector(`.status-label-${companyId}`);
                
                // Show loading state
                this.disabled = true;
                label.textContent = 'Updating...';
                
                fetch(`{{ route('superAdmin.company.index') }}/${companyId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        status: newStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        label.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                        showAlert('success', data.message);
                    } else {
                        // Revert toggle state
                        this.checked = !this.checked;
                        label.textContent = this.checked ? 'Active' : 'Inactive';
                        showAlert('danger', data.message || 'Failed to update status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Revert toggle state
                    this.checked = !this.checked;
                    label.textContent = this.checked ? 'Active' : 'Inactive';
                    showAlert('danger', 'An error occurred while updating status');
                })
                .finally(() => {
                    this.disabled = false;
                });
            });
        });

        // Delete Functionality
        document.querySelectorAll('.delete-company').forEach(button => {
            button.addEventListener('click', function() {
                companyToDelete = this.dataset.companyId;
                const companyName = this.dataset.companyName;
                
                document.getElementById('company-name-to-delete').textContent = companyName;
                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            });
        });

        // Confirm Delete
        document.getElementById('confirm-delete').addEventListener('click', function() {
            if (!companyToDelete) return;
            
            const deleteBtn = this;
            const deleteSpinner = document.getElementById('delete-spinner');
            
            // Show loading state
            deleteBtn.disabled = true;
            deleteSpinner.classList.remove('d-none');
            
            fetch(`{{ route('superAdmin.company.index') }}/${companyToDelete}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the table row
                    const row = document.getElementById(`company-row-${companyToDelete}`);
                    if (row) {
                        row.remove();
                    }
                    
                    // Hide modal
                    bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                    
                    showAlert('success', data.message);
                    
                    // Check if table is empty
                    const tbody = document.querySelector('#companies-table tbody');
                    if (tbody.children.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="ti ti-building fs-1 d-block mb-2"></i>
                                        <p class="mb-2">No companies found</p>
                                        <a href="{{ route('superAdmin.company.create') }}" class="btn btn-primary btn-sm">
                                            <i class="ti ti-plus me-1"></i>Add First Company
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }
                } else {
                    showAlert('danger', data.message || 'Failed to delete company');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while deleting the company');
            })
            .finally(() => {
                deleteBtn.disabled = false;
                deleteSpinner.classList.add('d-none');
                companyToDelete = null;
            });
        });

        // Search Functionality
        const searchInput = document.getElementById('search-companies');
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#companies-table tbody tr:not(.no-data)');
            
            rows.forEach(row => {
                const companyName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const contactPerson = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const email = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                
                if (companyName.includes(searchTerm) || contactPerson.includes(searchTerm) || email.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Status Filter
        document.querySelectorAll('.status-filter').forEach(filter => {
            filter.addEventListener('click', function(e) {
                e.preventDefault();
                const filterStatus = this.dataset.status;
                const rows = document.querySelectorAll('#companies-table tbody tr:not(.no-data)');
                
                rows.forEach(row => {
                    const statusToggle = row.querySelector('.status-toggle');
                    if (!statusToggle) return;
                    
                    const rowStatus = statusToggle.checked ? 'active' : 'inactive';
                    
                    if (filterStatus === 'all' || filterStatus === rowStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Update button text
                this.closest('.dropdown').querySelector('.dropdown-toggle').innerHTML = 
                    `<i class="ti ti-filter me-1"></i>Filter: ${this.textContent}`;
            });
        });

        // Alert function
        function showAlert(type, message) {
            const alertContainer = document.getElementById('alert-container');
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            alertContainer.innerHTML = alertHtml;
            
            // Auto dismiss after 5 seconds
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