@extends('EmployeeManagemntsystem.Layout.App')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <!-- Breadcrumb -->
        <div class="card">
            <div class="card-body">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb p-0 mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('HR.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('HR.leave-types.index') }}">Leave Types</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $leaveType->name }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Leave Type Details -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $leaveType->name }}</h4>
                        <small>{{ $leaveType->description }}</small>
                    </div>
                    <div>
                        <span class="badge {{ $leaveType->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $leaveType->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Basic Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Code:</strong></td>
                                <td>{{ $leaveType->code }}</td>
                            </tr>
                            <tr>
                                <td><strong>Max Days/Year:</strong></td>
                                <td>{{ $leaveType->max_days_per_year }}</td>
                            </tr>
                            <tr>
                                <td><strong>Carry Forward:</strong></td>
                                <td>{{ $leaveType->carry_forward_limit ?? 'Not allowed' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Type:</strong></td>
                                <td>
                                    <span class="badge {{ $leaveType->is_paid ? 'bg-success' : 'bg-warning' }}">
                                        {{ $leaveType->is_paid ? 'Paid' : 'Unpaid' }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Requirements</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Min Notice Days:</strong></td>
                                <td>{{ $leaveType->min_notice_days ?? 'No requirement' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Medical Certificate:</strong></td>
                                <td>
                                    <span class="badge {{ $leaveType->requires_medical_certificate ? 'bg-info' : 'bg-secondary' }}">
                                        {{ $leaveType->requires_medical_certificate ? 'Required' : 'Not Required' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Weekend Included:</strong></td>
                                <td>{{ $leaveType->weekend_included ? 'Yes' : 'No' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Applicable Roles:</strong></td>
                                <td>
                                    @if($leaveType->applicable_roles)
                                        @foreach($leaveType->applicable_roles as $role)
                                            <span class="badge bg-light text-dark">{{ $role }}</span>
                                        @endforeach
                                    @else
                                        All Roles
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="card">
            <div class="card-header">
                <h5>Usage Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3 class="text-primary">{{ $statistics['total_applications'] }}</h3>
                            <p class="text-muted">Total Applications</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3 class="text-success">{{ $statistics['approved_applications'] }}</h3>
                            <p class="text-muted">Approved</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3 class="text-warning">{{ $statistics['pending_applications'] }}</h3>
                            <p class="text-muted">Pending</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3 class="text-info">{{ $statistics['total_days_used'] }}</h3>
                            <p class="text-muted">Days Used</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex gap-2">
                    <a href="{{ route('HR.leave-types.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Back to List
                    </a>
                    <div class="alert alert-info mb-0 py-2 px-3">
                        <small><i class="ti ti-info-circle me-1"></i>HR users have read-only access to leave types</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection