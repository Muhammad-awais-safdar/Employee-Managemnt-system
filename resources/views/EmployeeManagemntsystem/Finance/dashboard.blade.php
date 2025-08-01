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
                                                <div class="avatar avtar-lg bg-primary mb-2">
                                                    <i class="ti ti-users fs-24"></i>
                                                </div>
                                                <h6 class="fs-14 fw-semibold mb-1">Total Employees</h6>
                                                <h4 class="fw-bold mb-0">{{ $financialStats['total_employees'] ?? 0 }}</h4>
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
                                                    <i class="ti ti-currency-dollar fs-24"></i>
                                                </div>
                                                <h6 class="fs-14 fw-semibold mb-1">Monthly Payroll</h6>
                                                <h4 class="fw-bold mb-0">₹{{ number_format($financialStats['monthly_payroll'] ?? 0) }}</h4>
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
                                                    <i class="ti ti-receipt fs-24"></i>
                                                </div>
                                                <h6 class="fs-14 fw-semibold mb-1">Monthly Expenses</h6>
                                                <h4 class="fw-bold mb-0">₹{{ number_format($financialStats['monthly_expenses'] ?? 0) }}</h4>
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
                                                <div class="avatar avtar-lg bg-danger mb-2">
                                                    <i class="ti ti-clock-pause fs-24"></i>
                                                </div>
                                                <h6 class="fs-14 fw-semibold mb-1">Pending Payments</h6>
                                                <h4 class="fw-bold mb-0">{{ $financialStats['pending_payments'] ?? 0 }}</h4>
                                            </div>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div><!-- end col -->

                        </div>
                        <!-- end row -->

                        <!-- Finance Quick Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="fw-bold mb-0">Quick Actions</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-3 col-sm-6">
                                                <a href="{{ route('Finance.salaries.index') }}" class="quick-action-card p-3 text-center border rounded h-100 d-block text-decoration-none">
                                                    <i class="ti ti-currency-dollar fs-24 text-primary mb-2"></i>
                                                    <h6 class="mb-1">Manage Salaries</h6>
                                                    <small class="text-muted">Set and update employee salaries</small>
                                                </a>
                                            </div>
                                            <div class="col-md-3 col-sm-6">
                                                <a href="{{ route('Finance.payroll.index') }}" class="quick-action-card p-3 text-center border rounded h-100 d-block text-decoration-none">
                                                    <i class="ti ti-calculator fs-24 text-success mb-2"></i>
                                                    <h6 class="mb-1">Process Payroll</h6>
                                                    <small class="text-muted">Calculate and process employee salaries</small>
                                                </a>
                                            </div>
                                            <div class="col-md-3 col-sm-6">
                                                <a href="{{ route('Finance.expenses.index') }}" class="quick-action-card p-3 text-center border rounded h-100 d-block text-decoration-none">
                                                    <i class="ti ti-receipt fs-24 text-warning mb-2"></i>
                                                    <h6 class="mb-1">Manage Expenses</h6>
                                                    <small class="text-muted">Review and approve expense claims</small>
                                                </a>
                                            </div>
                                            <div class="col-md-3 col-sm-6">
                                                <a href="{{ route('Finance.reports.index') }}" class="quick-action-card p-3 text-center border rounded h-100 d-block text-decoration-none">
                                                    <i class="ti ti-chart-bar fs-24 text-info mb-2"></i>
                                                    <h6 class="mb-1">Financial Reports</h6>
                                                    <small class="text-muted">Generate financial reports and analytics</small>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <a href={{ asset('employee-details.html') }} class="avatar avatar-sm avatar-rounded flex-shrink-0">
                                            <img src={{ asset('assets/img/employees/employee-01.jpg') }} alt="img">
                                        </a>
                                        <div class="ms-2">
                                            <h6 class="fs-14 mb-1"><a href={{ asset('employee-details.html') }}>John Carter</a></h6>
                                            <p class="fs-13 mb-0 text-truncate">Added New Project HRMS Dashboard</p>
                                        </div>
                                    </div>
                                    <span class="badge badge-soft-primary"><i class="ti ti-clock-hour-3 me-1"></i>06:20 PM</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <a href={{ asset('employee-details.html') }} class="avatar avatar-sm avatar-rounded flex-shrink-0">
                                            <img src={{ asset('assets/img/employees/employee-02.jpg') }} alt="img">
                                        </a>
                                        <div class="ms-2">
                                            <h6 class="fs-14 mb-1"><a href={{ asset('employee-details.html') }}>Sophia White</a></h6>
                                            <p class="fs-13 mb-0 text-truncate">Commented on Uploaded Document</p>
                                        </div>
                                    </div>
                                    <span class="badge badge-soft-primary"><i class="ti ti-clock-hour-3 me-1"></i>04:00 PM</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <a href={{ asset('employee-details.html') }} class="avatar avatar-sm avatar-rounded flex-shrink-0">
                                            <img src={{ asset('assets/img/employees/employee-03.jpg') }} alt="img">
                                        </a>
                                        <div class="ms-2">
                                            <h6 class="fs-14 mb-1"><a href={{ asset('employee-details.html') }}>Michael Johnson</a></h6>
                                            <p class="fs-13 mb-0 text-truncate">Approved Task Projects</p>
                                        </div>
                                    </div>
                                    <span class="badge badge-soft-primary"><i class="ti ti-clock-hour-3 me-1"></i>02:30 PM</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <a href={{ asset('employee-details.html') }} class="avatar avatar-sm avatar-rounded flex-shrink-0">
                                            <img src={{ asset('assets/img/employees/employee-04.jpg') }} alt="img">
                                        </a>
                                        <div class="ms-2">
                                            <h6 class="fs-14 mb-1"><a href={{ asset('employee-details.html') }}>Emily Clark</a></h6>
                                            <p class="fs-13 mb-0 text-truncate">Requesting Access to Module Tickets</p>
                                        </div>
                                    </div>
                                    <span class="badge badge-soft-primary"><i class="ti ti-clock-hour-3 me-1"></i>12:10 PM</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <a href={{ asset('employee-details.html') }} class="avatar avatar-sm avatar-rounded flex-shrink-0">
                                            <img src={{ asset('assets/img/employees/employee-05.jpg') }} alt="img">
                                        </a>
                                        <div class="ms-2">
                                            <h6 class="fs-14 mb-1"><a href={{ asset('employee-details.html') }}>David Anderson</a></h6>
                                            <p class="fs-13 mb-0 text-truncate">Downloaded App Reports</p>
                                        </div>
                                    </div>
                                    <span class="badge badge-soft-primary"><i class="ti ti-clock-hour-3 me-1"></i>10:40 AM</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <a href={{ asset('employee-details.html') }} class="avatar avatar-sm avatar-rounded flex-shrink-0">
                                            <img src={{ asset('assets/img/employees/employee-06.jpg') }} alt="img">
                                        </a>
                                        <div class="ms-2">
                                            <h6 class="fs-14 mb-1"><a href={{ asset('employee-details.html') }}>Olivia Haris</a></h6>
                                            <p class="fs-13 mb-0 text-truncate">Completed ticket module in HRMS</p>
                                        </div>
                                    </div>
                                    <span class="badge badge-soft-primary"><i class="ti ti-clock-hour-3 me-1"></i>09:50 AM</span>
                                </div>
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
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href={{ asset('employee-details.html') }} class="avatar avatar-sm avatar-rounded">
                                                            <img src={{ asset('assets/img/employees/employee-03.jpg') }} alt="img">
                                                        </a>
                                                        <div class="ms-2">
                                                            <h6 class="fs-14 mb-0"><a href={{ asset('employee-details.html') }}>Braun Kelton</a></h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span class="badge badge-md badge-soft-teal">PHP</span></td>
                                                <td><a href="https://dleohr.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="60021201150e200518010d100c054e030f0d">[email&#160;protected]</a></td>
                                            </tr>
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
                     
@endsection