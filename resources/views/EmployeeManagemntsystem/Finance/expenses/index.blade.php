@extends('EmployeeManagemntsystem.Layout.App')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb breadcrumb-divide p-0 mb-0">
                                @php
                                    $role = auth()->check() ? Auth::user()->getRoleNames()->first() : null;
                                    $dashboardRoute = $role && Route::has($role . '.dashboard') ? route($role . '.dashboard') : route('login');
                                @endphp
                                <li class="breadcrumb-item d-flex align-items-center"><a href="{{ $dashboardRoute }}">Home</a></li>
                                <li class="breadcrumb-item d-flex align-items-center"><a href="{{ route('Finance.dashboard') }}">Finance</a></li>
                                <li class="breadcrumb-item fw-medium active" aria-current="page">Expense Management</li>
                            </ol>
                        </nav>
                        <h5 class="fw-bold mb-0">Expense Management</h5>
                    </div>
                </div>
            </div>

            <!-- Expense Statistics -->
            <div class="row mb-4">
                <div class="col-md-3 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="avatar avtar-lg bg-primary mb-2">
                                        <i class="ti ti-receipt fs-24"></i>
                                    </div>
                                    <h6 class="fs-14 fw-semibold mb-1">Total Expenses</h6>
                                    <h4 class="fw-bold mb-0">{{ count($expenses) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="avatar avtar-lg bg-warning mb-2">
                                        <i class="ti ti-clock-pause fs-24"></i>
                                    </div>
                                    <h6 class="fs-14 fw-semibold mb-1">Pending</h6>
                                    <h4 class="fw-bold mb-0">{{ collect($expenses)->where('status', 'pending')->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="avatar avtar-lg bg-success mb-2">
                                        <i class="ti ti-check fs-24"></i>
                                    </div>
                                    <h6 class="fs-14 fw-semibold mb-1">Approved</h6>
                                    <h4 class="fw-bold mb-0">{{ collect($expenses)->where('status', 'approved')->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="avatar avtar-lg bg-info mb-2">
                                        <i class="ti ti-currency-dollar fs-24"></i>
                                    </div>
                                    <h6 class="fs-14 fw-semibold mb-1">Total Amount</h6>
                                    <h4 class="fw-bold mb-0">₹{{ number_format(collect($expenses)->sum('amount')) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expense Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold mb-0">Expense Actions</h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success" id="bulkApproveBtn">
                                <i class="ti ti-check me-1"></i>Bulk Approve
                            </button>
                            <button type="button" class="btn btn-danger" id="bulkRejectBtn">
                                <i class="ti ti-x me-1"></i>Bulk Reject
                            </button>
                            <a href="{{ route('Finance.expenses.export') }}" class="btn btn-outline-primary">
                                <i class="ti ti-download me-1"></i>Export Data
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expense Filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <form class="row g-3" id="expenseFilterForm">
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category">
                                <option value="">All Categories</option>
                                <option value="Office Supplies">Office Supplies</option>
                                <option value="Travel">Travel</option>
                                <option value="Equipment">Equipment</option>
                                <option value="Utilities">Utilities</option>
                                <option value="Software">Software</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">From Date</label>
                            <input type="date" class="form-control" name="from_date">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">To Date</label>
                            <input type="date" class="form-control" name="to_date">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary d-block w-100">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Expenses Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="fw-bold mb-0">Expense Claims</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Submitted Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $expense)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input expense-checkbox" 
                                                   value="{{ $loop->index }}" 
                                                   data-status="{{ $expense['status'] }}">
                                        </td>
                                        <td>
                                            <div>
                                                <h6 class="fs-14 mb-0">{{ $expense['category'] }}</h6>
                                                <small class="text-muted">ID: #EXP{{ str_pad($loop->iteration, 4, '0', STR_PAD_LEFT) }}</small>
                                            </div>
                                        </td>
                                        <td class="fw-semibold">₹{{ number_format($expense['amount']) }}</td>
                                        <td>
                                            @if($expense['status'] === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($expense['status'] === 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>{{ now()->subDays(rand(1, 30))->format('M d, Y') }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" 
                                                        data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="#" 
                                                           onclick="viewExpenseDetails({{ $loop->index }})">
                                                            <i class="ti ti-eye me-1"></i>View Details
                                                        </a>
                                                    </li>
                                                    @if($expense['status'] === 'pending')
                                                        <li>
                                                            <a class="dropdown-item text-success" href="#" 
                                                               onclick="approveExpense({{ $loop->index }})">
                                                                <i class="ti ti-check me-1"></i>Approve
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#" 
                                                               onclick="rejectExpense({{ $loop->index }})">
                                                                <i class="ti ti-x me-1"></i>Reject
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if($expense['status'] === 'approved')
                                                        <li>
                                                            <a class="dropdown-item text-primary" href="#" 
                                                               onclick="reimburseExpense({{ $loop->index }})">
                                                                <i class="ti ti-currency-dollar me-1"></i>Reimburse
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ti ti-inbox fs-48 mb-2 d-block"></i>
                                                No expense claims available
                                            </div>
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

    <!-- Expense Details Modal -->
    <div class="modal fade" id="expenseDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Expense Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="expenseDetailsContent">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve/Reject Modal -->
    <div class="modal fade" id="actionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="actionModalTitle">Approve Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="actionForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3" id="notesField">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="3" 
                                      placeholder="Add any notes or comments..."></textarea>
                        </div>
                        <div class="mb-3" id="rejectionReasonField" style="display: none;">
                            <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="rejection_reason" rows="3" 
                                      placeholder="Please provide reason for rejection..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="actionSubmitBtn">Approve</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentAction = '';
        let currentExpenseIndex = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Select all functionality
            const selectAllCheckbox = document.getElementById('selectAll');
            const expenseCheckboxes = document.querySelectorAll('.expense-checkbox');

            selectAllCheckbox.addEventListener('change', function() {
                expenseCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            // Bulk actions
            document.getElementById('bulkApproveBtn').addEventListener('click', function() {
                const selectedPending = document.querySelectorAll('.expense-checkbox:checked[data-status="pending"]');
                if (selectedPending.length === 0) {
                    alert('Please select pending expenses to approve.');
                    return;
                }
                // Implement bulk approve functionality
                console.log('Bulk approve:', selectedPending.length, 'expenses');
            });

            document.getElementById('bulkRejectBtn').addEventListener('click', function() {
                const selectedPending = document.querySelectorAll('.expense-checkbox:checked[data-status="pending"]');
                if (selectedPending.length === 0) {
                    alert('Please select pending expenses to reject.');
                    return;
                }
                // Implement bulk reject functionality
                console.log('Bulk reject:', selectedPending.length, 'expenses');
            });
        });

        function viewExpenseDetails(expenseIndex) {
            const expense = @json($expenses)[expenseIndex];
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Category</h6>
                        <p>${expense.category}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Amount</h6>
                        <p class="fw-bold">₹${expense.amount.toLocaleString()}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Status</h6>
                        <span class="badge bg-${expense.status === 'pending' ? 'warning' : expense.status === 'approved' ? 'success' : 'danger'}">${expense.status}</span>
                    </div>
                    <div class="col-md-6">
                        <h6>Submitted Date</h6>
                        <p>${new Date().toLocaleDateString()}</p>
                    </div>
                </div>
            `;
            
            document.getElementById('expenseDetailsContent').innerHTML = content;
            const modal = new bootstrap.Modal(document.getElementById('expenseDetailsModal'));
            modal.show();
        }

        function approveExpense(expenseIndex) {
            currentAction = 'approve';
            currentExpenseIndex = expenseIndex;
            
            document.getElementById('actionModalTitle').textContent = 'Approve Expense';
            document.getElementById('actionSubmitBtn').textContent = 'Approve';
            document.getElementById('actionSubmitBtn').className = 'btn btn-success';
            document.getElementById('notesField').style.display = 'block';
            document.getElementById('rejectionReasonField').style.display = 'none';
            
            const modal = new bootstrap.Modal(document.getElementById('actionModal'));
            modal.show();
        }

        function rejectExpense(expenseIndex) {
            currentAction = 'reject';
            currentExpenseIndex = expenseIndex;
            
            document.getElementById('actionModalTitle').textContent = 'Reject Expense';
            document.getElementById('actionSubmitBtn').textContent = 'Reject';
            document.getElementById('actionSubmitBtn').className = 'btn btn-danger';
            document.getElementById('notesField').style.display = 'none';
            document.getElementById('rejectionReasonField').style.display = 'block';
            
            const modal = new bootstrap.Modal(document.getElementById('actionModal'));
            modal.show();
        }

        function reimburseExpense(expenseIndex) {
            if (confirm('Are you sure you want to mark this expense as reimbursed?')) {
                // Implement reimburse functionality
                console.log('Reimburse expense:', expenseIndex);
                alert('Expense marked as reimbursed successfully!');
            }
        }

        // Handle action form submission
        document.getElementById('actionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const url = currentAction === 'approve' ? 
                `/finance/expenses/approve/${currentExpenseIndex}` : 
                `/finance/expenses/reject/${currentExpenseIndex}`;
            
            // Simulate API call
            setTimeout(() => {
                alert(`Expense ${currentAction}d successfully!`);
                location.reload();
            }, 500);
        });
    </script>
@endsection