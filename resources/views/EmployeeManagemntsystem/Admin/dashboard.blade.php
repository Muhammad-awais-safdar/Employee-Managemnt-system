@extends('EmployeeManagemntsystem.Layout.App')
@section('content')
    <!-- start row -->
                <div class="row">
                    <div class="col-lg-8 col-xl-9">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb breadcrumb-divide p-0 mb-0">
                                           @php
                                                $role = auth()->check() ? Auth::user()->getRoleNames()->first() : null;
                                                $dashboardRoute = $role && Route::has($role . '.dashboard') ? route($role . '.dashboard') : route('login');
                                            @endphp
                                            <li class="breadcrumb-item d-flex align-items-center"><a href={{ $dashboardRoute }}>Home</a></li>
                                            <li class="breadcrumb-item fw-medium active" aria-current="page">Dashboard</li>
                                        </ol>
                                    </nav>
                                    <h5 class="fw-bold mb-0">{{ Str::title(Auth::user()->getRoleNames()->first()) }} Dashboard</h5>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->

                        <!-- start row -->
                        <div class="row">

                            <div class="col-md-6 col-xl-3 d-flex">
                                <div class="card flex-fill">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <div class="avatar avtar-lg bg-teal mb-2">
                                                    <i class="ti ti-users text-white fs-3"></i>
                                                </div>
                                                <h6 class="fs-14 fw-semibold mb-2">Total Employees</h6>
                                                <h4 class="fw-bold text-primary mb-1">{{ $stats['total_users'] }}</h4>
                                                <small class="text-muted">
                                                    <span class="text-{{ $stats['user_growth_percentage'] >= 0 ? 'success' : 'danger' }}">
                                                        <i class="ti ti-trending-{{ $stats['user_growth_percentage'] >= 0 ? 'up' : 'down' }}"></i>
                                                        {{ abs($stats['user_growth_percentage']) }}%
                                                    </span>
                                                    from last month
                                                </small>
                                            </div>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div><!-- end col -->

                            <div class="col-md-6 col-xl-3 d-flex">
                                <div class="card flex-fill">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <div class="avatar avtar-lg bg-warning mb-2">
                                                    <i class="ti ti-layout-grid text-white fs-3"></i>
                                                </div>
                                                <h6 class="fs-14 fw-semibold mb-2">Departments</h6>
                                                <h4 class="fw-bold text-warning mb-1">{{ $stats['total_departments'] }}</h4>
                                                <small class="text-muted">
                                                    <span class="text-success">
                                                        {{ $stats['active_departments'] }} active
                                                    </span>
                                                </small>
                                            </div>
                                            <a href="{{ route('Admin.departments.index') }}" class="btn btn-sm btn-outline-warning">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div><!-- end col -->

                            <div class="col-md-6 col-xl-3 d-flex">
                                <div class="card flex-fill">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <div class="avatar avtar-lg bg-orange mb-2">
                                                    <i class="ti ti-users-group text-white fs-3"></i>
                                                </div>
                                                <h6 class="fs-14 fw-semibold mb-2">Active Users</h6>
                                                <h4 class="fw-bold text-orange mb-1">{{ $stats['active_users'] }}</h4>
                                                <small class="text-muted">
                                                    {{ $stats['inactive_users'] }} inactive
                                                </small>
                                            </div>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div><!-- end col -->

                            <div class="col-md-6 col-xl-3 d-flex">
                                <div class="card flex-fill">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <div class="avatar avtar-lg bg-success mb-2">
                                                    <i class="ti ti-shield-check text-white fs-3"></i>
                                                </div>
                                                <h6 class="fs-14 fw-semibold mb-2">HR Staff</h6>
                                                <h4 class="fw-bold text-success mb-1">{{ $stats['hr_users'] + $stats['admin_users'] }}</h4>
                                                <small class="text-muted">
                                                    {{ $stats['admin_users'] }} Admin, {{ $stats['hr_users'] }} HR
                                                </small>
                                            </div>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div><!-- end col -->

                        </div>
                        <!-- end row -->

                        <!-- Quick Admin Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card admin-actions-card">
                                    <div class="card-header bg-gradient-primary text-white">
                                        <h6 class="fw-bold mb-0 text-white">
                                            <i class="ti ti-settings me-2"></i>Quick Admin Settings
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <a href="{{ route('Admin.working-hours.index') }}" class="admin-action-item">
                                                    <div class="action-icon bg-primary">
                                                        <i class="ti ti-clock-cog"></i>
                                                    </div>
                                                    <div class="action-content">
                                                        <h6 class="action-title">Working Hours</h6>
                                                        <p class="action-desc">Configure attendance policy</p>
                                                    </div>
                                                    <div class="action-arrow">
                                                        <i class="ti ti-chevron-right"></i>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-md-3">
                                                <a href="{{ route('Admin.departments.index') }}" class="admin-action-item">
                                                    <div class="action-icon bg-warning">
                                                        <i class="ti ti-building"></i>
                                                    </div>
                                                    <div class="action-content">
                                                        <h6 class="action-title">Departments</h6>
                                                        <p class="action-desc">Manage company departments</p>
                                                    </div>
                                                    <div class="action-arrow">
                                                        <i class="ti ti-chevron-right"></i>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-md-3">
                                                <a href="{{ route('Admin.users.index') }}" class="admin-action-item">
                                                    <div class="action-icon bg-success">
                                                        <i class="ti ti-users"></i>
                                                    </div>
                                                    <div class="action-content">
                                                        <h6 class="action-title">Employee Management</h6>
                                                        <p class="action-desc">Add and manage employees</p>
                                                    </div>
                                                    <div class="action-arrow">
                                                        <i class="ti ti-chevron-right"></i>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-md-3">
                                                <a href="{{ route('Admin.attendance.reports') }}" class="admin-action-item">
                                                    <div class="action-icon bg-info">
                                                        <i class="ti ti-chart-bar"></i>
                                                    </div>
                                                    <div class="action-content">
                                                        <h6 class="action-title">Attendance Reports</h6>
                                                        <p class="action-desc">View detailed reports</p>
                                                    </div>
                                                    <div class="action-arrow">
                                                        <i class="ti ti-chevron-right"></i>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="row g-3 mt-1">
                                            <div class="col-md-6">
                                                <a href="#" onclick="showSalaryManagement()" class="admin-action-item">
                                                    <div class="action-icon bg-success">
                                                        <i class="ti ti-currency-dollar"></i>
                                                    </div>
                                                    <div class="action-content">
                                                        <h6 class="action-title">Employee Salaries</h6>
                                                        <p class="action-desc">Set and manage salaries</p>
                                                    </div>
                                                    <div class="action-arrow">
                                                        <i class="ti ti-chevron-right"></i>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="#" onclick="showIncrementRequests()" class="admin-action-item">
                                                    <div class="action-icon bg-orange">
                                                        <i class="ti ti-trending-up"></i>
                                                    </div>
                                                    <div class="action-content">
                                                        <h6 class="action-title">Increment Requests</h6>
                                                        <p class="action-desc">Approve salary increments <span class="badge badge-sm badge-soft-danger ms-1">{{ $pendingIncrements ?? 0 }}</span></p>
                                                    </div>
                                                    <div class="action-arrow">
                                                        <i class="ti ti-chevron-right"></i>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end admin actions -->

                        <!-- start row -->
                        <div class="row">

                            <div class="col-md-6 col-xl-4 d-flex">
                                <div class="card flex-fill">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <div class="mb-1">
                                                    <p class="mb-1 text-dark">Total Applications</p>
                                                    <h6 class="fs-16 fw-semibold mb-1">5,358</h6>
                                                </div>
                                                <p class="fs-12 text-truncate text-dark mb-0"><span class="text-success me-1"><i class="ti ti-trending-up"></i></span>+1.4% from last week</p>
                                            </div>
                                            <div id="circle_chart_4"></div>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div><!-- end col -->

                            <div class="col-md-6 col-xl-4 d-flex">
                                <div class="card flex-fill">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <div class="mb-1">
                                                    <p class="mb-1 text-dark">Total Shortlisted</p>
                                                    <h6 class="fs-16 fw-semibold mb-1">4,280</h6>
                                                </div>
                                                <p class="fs-12 text-truncate text-dark mb-0"><span class="text-success me-1"><i class="ti ti-trending-up"></i></span>+1.4% from last week</p>
                                            </div>
                                            <div id="circle_chart_5"></div>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div><!-- end col -->

                            <div class="col-md-6 col-xl-4 d-flex">
                                <div class="card flex-fill">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <div class="mb-1">
                                                    <p class="mb-1 text-dark">Total Rejected</p>
                                                    <h6 class="fs-16 fw-semibold mb-1">1078</h6>
                                                </div>
                                                <p class="fs-12 text-truncate tex-dark mb-0"><span class="text-success me-1"><i class="ti ti-trending-up"></i></span>+1.4% from last week</p>
                                            </div>
                                            <div id="circle_chart_6"></div>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div><!-- end col -->

                        </div>
                        <!-- end row -->

                    </div><!-- end col -->

                    <div class="col-lg-4 col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="index-profile text-center">
                                    <img src={{ asset('assets/img/users/user-05.jpg') }} alt="img" class="avatar avatar-xxl rounded-circle shadow">
                                    <div class="text-center mb-0">
                                        <h5 class="fw-bold mb-1">Welcome Admin</h5>
                                        <p class="mb-0">17 Apr 2025</p>
                                    </div>
                                </div>
                                <div class="index-profile-links">
                                    <a href={{ asset('index.html') }} class="dashboard-toggle active">Admin Dashboard</a>
                                    <a href={{ asset('employee-dashboard.html') }} class="dashboard-toggle">Employee Dashboard</a>
                                </div>
                            </div>
                        </div>
                    </div><!-- end col -->

                </div>
                <!-- end row -->

                <!-- start row -->
                <div class="row">
                    
                    <div class="col-md-6 col-xl-4 d-flex">
                        <div class="card flex-fill">
                            <div class="card-header">
                                <h5 class="fw-bold mb-0">Total Employees</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-center">
                                    <div id="polarchart"></div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <h6 class="fs-14 mb-3"><i class="ti ti-square-filled text-indigo me-1"></i>Design</h6>
                                        <h6 class="fs-14 mb-0"><i class="ti ti-square-filled text-warning me-1"></i>Development</h6>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="fs-14 mb-3"><i class="ti ti-square-filled text-success me-1"></i>Business</h6>
                                        <h6 class="fs-14 mb-0"><i class="ti ti-square-filled text-orange me-1"></i>Testing</h6>
                                    </div>
                                </div>
                                
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-md-6 col-xl-5 d-flex">
                        <div class="card flex-fill">
                            <div class="card-header">
                                <h5 class="fw-bold mb-0">Total Applications</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <div id="applications_chart"></div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <h6 class="fs-14 mb-0"><i class="ti ti-square-filled text-indigo me-1"></i>Total</h6>
                                            <div class="d-flex">
                                                <span class="span-divider me-3">5358</span>
                                                <span>44%</span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h6 class="fs-14 mb-0"><i class="ti ti-square-filled text-warning me-1"></i>Shortlisted</h6>
                                            <div class="d-flex">
                                                <span class="span-divider me-3">857</span>
                                                <span>16%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <h6 class="fs-14 mb-0"><i class="ti ti-square-filled text-success me-1"></i>Selected </h6>
                                            <div class="d-flex">
                                                <span class="span-divider me-3">1714</span>
                                                <span>32%</span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h6 class="fs-14 mb-0"><i class="ti ti-square-filled text-orange me-1"></i>Rejected</h6>
                                            <div class="d-flex">
                                                <span class="span-divider me-3">428</span>
                                                <span>08%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                    <div class="col-md-12 col-xl-3 d-flex">
                        <div class="card flex-fill">
                            <div class="card-header">
                                <h5 class="fw-bold mb-0">Employee Strucuture</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex d-xl-block align-items-center justify-content-center flex-wrap text-center">
                                    <div>
                                        <div id="chart_male"></div>
                                        <p class="text-center fw-semibold text-dark mb-0">Male</p>
                                    </div>
                                    <div>
                                        <div id="chart_female"></div>
                                        <p class="text-center fw-semibold text-dark mb-0">Female</p>
                                    </div>
                                </div>
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div><!-- end col -->

                </div>
                <!-- end row -->

                <!-- start row -->
                <div class="row">

                    <div class="col-lg-5 d-flex">
                        <div class="card shadow flex-fill">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h6 class="fw-bold mb-0">Recent Activities</h6>
                                <a href="#" class="btn btn-sm btn-icon btn-outline-white border-0"><i class="ti ti-refresh"></i></a>
                            </div>
                            <div class="card-body">
                                @forelse($recentActivities as $activity)
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm avatar-rounded flex-shrink-0 bg-primary text-white">
                                            {{ strtoupper(substr($activity['user']->name, 0, 1)) }}
                                        </div>
                                        <div class="ms-2">
                                            <h6 class="fs-14 mb-1">{{ $activity['user']->name }}</h6>
                                            <p class="fs-13 mb-0 text-truncate">{{ $activity['action'] }}</p>
                                        </div>
                                    </div>
                                    <span class="badge badge-soft-primary"><i class="ti ti-clock-hour-3 me-1"></i>{{ $activity['time'] }}</span>
                                </div>
                                @empty
                                <div class="text-center py-3">
                                    <i class="ti ti-activity text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">No recent activities</p>
                                </div>
                                @endforelse
                            </div> <!-- end card body -->
                        </div> <!-- end card -->
                    </div><!-- end col -->

                    <div class="col-lg-7 d-flex">
                        <div class="card shadow flex-fill">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h6 class="fw-bold mb-0">Team Leads</h6>
                                <a href={{ asset('manage-team-lead.html') }} class="btn btn-sm btn-outline-white">Manage Team</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-nowrap border">
                                        <thead>
                                            <tr>
                                                <th>Lead Name</th>
                                                <th>Team</th>
                                                <th>Email</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($teamLeads as $lead)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm avatar-rounded bg-primary text-white">
                                                            {{ strtoupper(substr($lead->name, 0, 1)) }}
                                                        </div>
                                                        <div class="ms-2">
                                                            <h6 class="fs-14 mb-0">{{ $lead->name }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-md badge-soft-primary">
                                                        {{ $lead->department->name ?? 'No Department' }}
                                                    </span>
                                                </td>
                                                <td>{{ $lead->email }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-3">
                                                    <i class="ti ti-user-x text-muted" style="font-size: 2rem;"></i>
                                                    <p class="text-muted mt-2">No team leads found</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href={{ asset('employee-details.html') }} class="avatar avatar-sm avatar-rounded">
                                                            <img src={{ asset('assets/img/employees/employee-06.jpg') }} alt="img">
                                                        </a>
                                                        <div class="ms-2">
                                                            <h6 class="fs-14 mb-0"><a href={{ asset('employee-details.html') }}>Sarah Michelle</a></h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span class="badge badge-md badge-soft-pink">IOS</span></td>
                                                <td><a href="https://dleohr.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f7849685969fb7928f969a879b92d994989a">[email&#160;protected]</a></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href={{ asset('employee-details.html') }} class="avatar avatar-sm avatar-rounded">
                                                            <img src={{ asset('assets/img/managers/manager-07.jpg') }} alt="img">
                                                        </a>
                                                        <div class="ms-2">
                                                            <h6 class="fs-14 mb-0"><a href={{ asset('employee-details.html') }}>Daniel Patrick</a></h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span class="badge badge-md badge-soft-orange">HTML</span></td>
                                                <td><a href="https://dleohr.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="066267686f636a46637e676b766a632865696b">[email&#160;protected]</a></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href="javascript:void(0);" class="avatar avatar-sm avatar-rounded">
                                                            <img src={{ asset('assets/img/employees/employee-08.jpg') }} alt="img">
                                                        </a>
                                                        <div class="ms-2">
                                                            <h6 class="fs-14 mb-0"><a href="javascript:void(0);">Emily Clark</a></h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span class="badge badge-md badge-soft-success">UI/UX</span></td>
                                                <td><a href="https://dleohr.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="492c24202530092c31282439252c672a2624">[email&#160;protected]</a></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href={{ asset('employee-details.html') }} class="avatar avatar-sm avatar-rounded">
                                                            <img src={{ asset('assets/img/managers/manager-05.jpg') }} alt="img">
                                                        </a>
                                                        <div class="ms-2">
                                                            <h6 class="fs-14 mb-0"><a href={{ asset('employee-details.html') }}>Ryan Christopher</a></h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span class="badge badge-md badge-soft-info">React</span></td>
                                                <td><a href="https://dleohr.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="89fbf0e8e7c9ecf1e8e4f9e5eca7eae6e4">[email&#160;protected]</a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div> <!-- end card body -->
                        </div> <!-- end card -->
                    </div><!-- end col -->

                    <div class="col-lg-7 d-flex">
                        <div class="card shadow flex-fill">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h6 class="fw-bold mb-0">Upcoming Leaves</h6>
                                <a href={{ asset('leaves.html') }} class="btn btn-sm btn-outline-white">Manage Leave</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-nowrap border">
                                        <thead>
                                            <tr>
                                                <th>Employee</th>
                                                <th>Date</th>
                                                <th>Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href={{ asset('employee-details.html') }} class="avatar avatar-sm avatar-rounded">
                                                            <img src={{ asset('assets/img/employees/employee-09.jpg') }} alt="img">
                                                        </a>
                                                        <div class="ms-2">
                                                            <h6 class="fs-14 mb-0"><a href={{ asset('employee-details.html') }}>Daniel Martinz</a></h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>17 Apr 2025</td>
                                                <td><span class="badge badge-md badge-soft-teal">Sick Leave</span></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href={{ asset('employee-details.html') }} class="avatar avatar-sm avatar-rounded">
                                                            <img src={{ asset('assets/img/employees/employee-04.jpg') }} alt="img">
                                                        </a>
                                                        <div class="ms-2">
                                                            <h6 class="fs-14 mb-0"><a href={{ asset('employee-details.html') }}>Emily Clark</a></h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>20 Apr 2025</td>
                                                <td><span class="badge badge-md badge-soft-primary">Casual Leave</span></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href={{ asset('employee-details.html') }} class="avatar avatar-sm avatar-rounded">
                                                            <img src={{ asset('assets/img/managers/manager-03.jpg') }} alt="img">
                                                        </a>
                                                        <div class="ms-2">
                                                            <h6 class="fs-14 mb-0"><a href={{ asset('employee-details.html') }}>Daniel Patrick</a></h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>22 Apr 2025</td>
                                                <td><span class="badge badge-md badge-soft-orange">Annual Leave</span></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href="javascript:void(0);" class="avatar avatar-sm avatar-rounded">
                                                            <img src={{ asset('assets/img/employees/employee-02.jpg') }} alt="img">
                                                        </a>
                                                        <div class="ms-2">
                                                            <h6 class="fs-14 mb-0"><a href="javascript:void(0);">Sophia White</a></h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>28 Apr 2025</td>
                                                <td><span class="badge badge-md badge-soft-teal">Sick Leave</span></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href={{ asset('employee-details.html') }} class="avatar avatar-sm avatar-rounded">
                                                            <img src={{ asset('assets/img/managers/manager-09.jpg') }} alt="img">
                                                        </a>
                                                        <div class="ms-2">
                                                            <h6 class="fs-14 mb-0"><a href={{ asset('employee-details.html') }}>Madison Andrew</a></h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>30 Apr 2025</td>
                                                <td><span class="badge badge-md badge-soft-primary">Casual Leave</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div> <!-- end card body -->
                        </div> <!-- end card -->
                    </div><!-- end col -->

                    <div class="col-lg-5 d-flex">
                        <div class="card shadow flex-fill">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-bold">Today</h6>
                                <a href="#" class="btn btn-sm btn-icon btn-outline-white border-0"><i class="ti ti-refresh"></i></a>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-soft-primary rounded-circle text-primary flex-shrink-0 me-2">
                                            <i class="ti ti-cake fs-16"></i>
                                        </span>
                                        <p class="mb-0">Daniel Martinz’s  Birthday</p>
                                    </div>
                                    <span class="avatar avatar-sm avatar-rounded"><img src={{ asset('assets/img/employees/employee-10.jpg') }} alt=""></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-soft-primary rounded-circle text-primary flex-shrink-0 me-2">
                                            <i class="ti ti-cake fs-16"></i>
                                        </span>
                                        <p class="mb-0">Amelia Curr’s  Birthday</p>
                                    </div>
                                    <span class="avatar avatar-sm avatar-rounded"><img src={{ asset('assets/img/employees/employee-09.jpg') }} alt=""></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-soft-primary rounded-circle text-primary flex-shrink-0 me-2">
                                            <i class="ti ti-cake fs-16"></i>
                                        </span>
                                        <p class="mb-0">Emma Lewis’s  Birthday</p>
                                    </div>
                                    <span class="avatar avatar-sm avatar-rounded"><img src={{ asset('assets/img/employees/employee-08.jpg') }} alt=""></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-soft-secondary rounded-circle text-secondary flex-shrink-0 me-2">
                                            <i class="ti ti-calendar-star fs-16"></i>
                                        </span>
                                        <p class="mb-0">Madison Andrew is off sick today</p>
                                    </div>
                                    <span class="avatar avatar-sm avatar-rounded"><img src={{ asset('assets/img/managers/manager-09.jpg') }} alt=""></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-soft-secondary rounded-circle text-secondary flex-shrink-0 me-2">
                                            <i class="ti ti-calendar-star fs-16"></i>
                                        </span>
                                        <p class="mb-0">Victoria Celestie is off sick today</p>
                                    </div>
                                    <span class="avatar avatar-sm avatar-rounded"><img src={{ asset('assets/img/managers/manager-10.jpg') }} alt=""></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-soft-secondary rounded-circle text-secondary flex-shrink-0 me-2">
                                            <i class="ti ti-calendar-star fs-16"></i>
                                        </span>
                                        <p class="mb-0">Daniel Patrick is off sick today</p>
                                    </div>
                                    <span class="avatar avatar-sm avatar-rounded"><img src={{ asset('assets/img/managers/manager-03.jpg') }} alt=""></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-soft-secondary rounded-circle text-secondary flex-shrink-0 me-2">
                                            <i class="ti ti-calendar-star fs-16"></i>
                                        </span>
                                        <p class="mb-0">Jessica Renee is off sick today</p>
                                    </div>
                                    <span class="avatar avatar-sm avatar-rounded"><img src={{ asset('assets/img/managers/manager-06.jpg') }} alt=""></span>
                                </div>
                            </div>
                        </div>
                    </div><!-- end col -->

                    <div class="col-xl-5 d-flex">
                        <div class="card shadow flex-fill">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h6 class="fw-bold mb-0">To Do List</h6>
                                <a href={{ asset('todo-list.html') }} class="btn btn-sm btn-outline-white">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="check_1" class="form-check-input me-2" checked>
                                        <label for="check_1">
                                            <span class="d-flex align-items-center mb-1">
                                                <span class="fs-14 fw-semibold text-dark text-decoration-line-through me-2">New Employee Intro</span>
                                                <span class="badge badge-md badge-soft-danger">High</span>
                                            </span>
                                            <span class="fs-13 mb-0">Scheduled for 04:00 PM on 18 Apr 2025</span>
                                        </label>
                                    </div>
                                    <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-outline-white" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="ti ti-trash"></i></a>
                                </div>
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="check_2" class="form-check-input me-2">
                                        <label for="check_2">
                                            <span class="d-flex align-items-center mb-1">
                                                <span class="fs-14 fw-semibold text-dark me-2">New Employee Intro</span>
                                                <span class="badge badge-md badge-soft-info">Medium</span>
                                            </span>
                                            <span class="fs-13 mb-0">Scheduled for 04:00 PM on 18 Apr 2025</span>
                                        </label>
                                    </div>
                                    <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-outline-white" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="ti ti-trash"></i></a>
                                </div>
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="check_3" class="form-check-input me-2">
                                        <label for="check_3">
                                            <span class="d-flex align-items-center mb-1">
                                                <span class="fs-14 fw-semibold text-dark me-2">New Employee Intro</span>
                                                <span class="badge badge-md badge-soft-success">Low</span>
                                            </span>
                                            <span class="fs-13 mb-0">Scheduled for 04:00 PM on 18 Apr 2025</span>
                                        </label>
                                    </div>
                                    <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-outline-white" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="ti ti-trash"></i></a>
                                </div>
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="check_4" class="form-check-input me-2">
                                        <label for="check_4">
                                            <span class="d-flex align-items-center mb-1">
                                                <span class="fs-14 fw-semibold text-dark me-2">New Employee Intro</span>
                                                <span class="badge badge-md badge-soft-danger">High</span>
                                            </span>
                                            <span class="fs-13 mb-0">Scheduled for 04:00 PM on 18 Apr 2025</span>
                                        </label>
                                    </div>
                                    <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-outline-white" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="ti ti-trash"></i></a>
                                </div>
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="check_5" class="form-check-input me-2">
                                        <label for="check_5">
                                            <span class="d-flex align-items-center mb-1">
                                                <span class="fs-14 fw-semibold text-dark me-2">New Employee Intro</span>
                                                <span class="badge badge-md badge-soft-info">Medium</span>
                                            </span>
                                            <span class="fs-13 mb-0">Scheduled for 04:00 PM on 18 Apr 2025</span>
                                        </label>
                                    </div>
                                    <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-outline-white" data-bs-toggle="modal" data-bs-target="#delete_modal"><i class="ti ti-trash"></i></a>
                                </div>
                            </div> <!-- end card body -->
                        </div> <!-- end card -->
                    </div><!-- end col -->

                    <div class="col-xl-7">
                        <div class="card shadow">
                            <div class="card-header">
                                <h6 class="fw-bold mb-0">Total Salary By Unit</h6>
                            </div>
                            <div class="card-body">
                                <div id="salary-chart"></div>
                            </div> <!-- end card body -->
                        </div> <!-- end card -->
                    </div><!-- end col -->

                </div>
                <!-- end row -->

<!-- Salary Management Modal -->
<div class="modal fade" id="salaryManagementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Employee Salary Management</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="salaryTable">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Current Salary</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="salaryTableBody">
                            <!-- Dynamic content will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Set Salary Modal -->
<div class="modal fade" id="setSalaryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Employee Salary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="setSalaryForm">
                <div class="modal-body">
                    <input type="hidden" id="employee_id" name="employee_id">
                    <div class="mb-3">
                        <label class="form-label">Employee Name</label>
                        <input type="text" class="form-control" id="employee_name" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Salary</label>
                        <input type="text" class="form-control" id="current_salary" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Salary <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="new_salary" name="salary" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Effective Date</label>
                        <input type="date" class="form-control" id="effective_date" name="effective_date" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" id="admin_notes" name="notes" rows="3" placeholder="Optional notes about salary change"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Salary</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Increment Requests Modal -->
<div class="modal fade" id="incrementRequestsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Salary Increment Requests</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="incrementRequestsTable">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Requested By</th>
                                <th>Current Salary</th>
                                <th>Requested Salary</th>
                                <th>Increment</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="incrementRequestsTableBody">
                            <!-- Dynamic content will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve/Reject Increment Modal -->
<div class="modal fade" id="reviewIncrementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review Increment Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="reviewIncrementForm">
                <div class="modal-body">
                    <input type="hidden" id="request_id" name="request_id">
                    <div class="mb-3">
                        <label class="form-label">Employee</label>
                        <input type="text" class="form-control" id="review_employee_name" readonly>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Current Salary</label>
                            <input type="text" class="form-control" id="review_current_salary" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Requested Salary</label>
                            <input type="text" class="form-control" id="review_requested_salary" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Increment Amount</label>
                        <input type="text" class="form-control" id="review_increment_amount" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea class="form-control" id="review_reason" readonly rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin Notes</label>
                        <textarea class="form-control" id="review_admin_notes" name="admin_notes" rows="3" placeholder="Add your notes about this request"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Effective Date (if approved)</label>
                        <input type="date" class="form-control" id="review_effective_date" name="effective_date" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="reviewIncrement('rejected')">Reject</button>
                    <button type="button" class="btn btn-success" onclick="reviewIncrement('approved')">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Salary Management Functions
function showSalaryManagement() {
    loadSalaryData();
    $('#salaryManagementModal').modal('show');
}

function showIncrementRequests() {
    loadIncrementRequests();
    $('#incrementRequestsModal').modal('show');
}

function loadSalaryData() {
    fetch('/admin/salary-management/employees')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('salaryTableBody');
            tbody.innerHTML = '';
            
            data.employees.forEach(employee => {
                const row = `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm bg-primary text-white me-2">
                                    ${employee.name.charAt(0).toUpperCase()}
                                </div>
                                <div>
                                    <h6 class="mb-0">${employee.name}</h6>
                                    <small class="text-muted">${employee.email}</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge badge-soft-info">${employee.department || 'N/A'}</span></td>
                        <td><strong>$${parseFloat(employee.salary || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</strong></td>
                        <td>${employee.salary_updated_at || 'Never'}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="showSetSalary(${employee.id}, '${employee.name}', ${employee.salary || 0})">
                                <i class="ti ti-edit"></i> Set Salary
                            </button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        })
        .catch(error => {
            console.error('Error loading salary data:', error);
            showAlert('Error loading salary data', 'danger');
        });
}

function showSetSalary(employeeId, employeeName, currentSalary) {
    document.getElementById('employee_id').value = employeeId;
    document.getElementById('employee_name').value = employeeName;
    document.getElementById('current_salary').value = '$' + parseFloat(currentSalary).toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('new_salary').value = currentSalary;
    document.getElementById('new_salary').focus();
    
    $('#salaryManagementModal').modal('hide');
    $('#setSalaryModal').modal('show');
}

function loadIncrementRequests() {
    fetch('/admin/increment-requests')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('incrementRequestsTableBody');
            tbody.innerHTML = '';
            
            data.requests.forEach(request => {
                const statusBadge = request.status === 'pending' ? 'badge-soft-warning' : 
                                  request.status === 'approved' ? 'badge-soft-success' : 'badge-soft-danger';
                
                const row = `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm bg-primary text-white me-2">
                                    ${request.employee.name.charAt(0).toUpperCase()}
                                </div>
                                <div>
                                    <h6 class="mb-0">${request.employee.name}</h6>
                                    <small class="text-muted">${request.employee.email}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <small>${request.requested_by.name}</small><br>
                            <small class="text-muted">${new Date(request.created_at).toLocaleDateString()}</small>
                        </td>
                        <td><strong>$${parseFloat(request.current_salary).toLocaleString('en-US', {minimumFractionDigits: 2})}</strong></td>
                        <td><strong>$${parseFloat(request.requested_salary).toLocaleString('en-US', {minimumFractionDigits: 2})}</strong></td>
                        <td>
                            <span class="text-success">+$${parseFloat(request.increment_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</span><br>
                            <small class="text-muted">(${request.increment_percentage}%)</small>
                        </td>
                        <td><small>${request.reason.substring(0, 50)}${request.reason.length > 50 ? '...' : ''}</small></td>
                        <td><span class="badge ${statusBadge}">${request.status.charAt(0).toUpperCase() + request.status.slice(1)}</span></td>
                        <td>
                            ${request.status === 'pending' ? 
                                `<button class="btn btn-sm btn-outline-primary" onclick="showReviewIncrement(${request.id}, '${request.employee.name}', ${request.current_salary}, ${request.requested_salary}, ${request.increment_amount}, '${request.reason}')">
                                    <i class="ti ti-eye"></i> Review
                                </button>` : 
                                `<small class="text-muted">Reviewed</small>`
                            }
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        })
        .catch(error => {
            console.error('Error loading increment requests:', error);
            showAlert('Error loading increment requests', 'danger');
        });
}

function showReviewIncrement(requestId, employeeName, currentSalary, requestedSalary, incrementAmount, reason) {
    document.getElementById('request_id').value = requestId;
    document.getElementById('review_employee_name').value = employeeName;
    document.getElementById('review_current_salary').value = '$' + parseFloat(currentSalary).toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('review_requested_salary').value = '$' + parseFloat(requestedSalary).toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('review_increment_amount').value = '$' + parseFloat(incrementAmount).toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('review_reason').value = reason;
    
    $('#incrementRequestsModal').modal('hide');
    $('#reviewIncrementModal').modal('show');
}

function reviewIncrement(decision) {
    const formData = new FormData(document.getElementById('reviewIncrementForm'));
    formData.append('decision', decision);
    
    fetch('/admin/increment-requests/review', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            $('#reviewIncrementModal').modal('hide');
            loadIncrementRequests();
            $('#incrementRequestsModal').modal('show');
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error reviewing increment:', error);
        showAlert('Error processing request', 'danger');
    });
}

// Set Salary Form Handler
document.getElementById('setSalaryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/admin/salary-management/update', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            $('#setSalaryModal').modal('hide');
            loadSalaryData();
            $('#salaryManagementModal').modal('show');
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error updating salary:', error);
        showAlert('Error updating salary', 'danger');
    });
});

function showAlert(message, type) {
    // Create and show alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>

<style>
.admin-actions-card {
    border: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-radius: 15px;
    overflow: hidden;
    margin-bottom: 30px;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%) !important;
}

.admin-action-item {
    display: flex;
    align-items: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    height: 100%;
}

.admin-action-item:hover {
    background: white;
    border-color: #4f46e5;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(79, 70, 229, 0.15);
    color: inherit;
    text-decoration: none;
}

.action-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    margin-right: 15px;
    flex-shrink: 0;
}

.action-content {
    flex-grow: 1;
}

.action-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 4px;
}

.action-desc {
    font-size: 0.875rem;
    color: #64748b;
    margin: 0;
}

.action-arrow {
    color: #94a3b8;
    font-size: 1.25rem;
    transition: all 0.3s ease;
    margin-left: 10px;
}

.admin-action-item:hover .action-arrow {
    color: #4f46e5;
    transform: translateX(3px);
}

@media (max-width: 768px) {
    .admin-action-item {
        padding: 15px;
    }
    
    .action-icon {
        width: 40px;
        height: 40px;
        font-size: 1.25rem;
        margin-right: 12px;
    }
    
    .action-title {
        font-size: 0.9rem;
    }
    
    .action-desc {
        font-size: 0.8rem;
    }
}
</style>
                     
@endsection