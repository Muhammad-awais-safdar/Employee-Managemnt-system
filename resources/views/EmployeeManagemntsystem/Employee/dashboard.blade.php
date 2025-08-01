@extends('EmployeeManagemntsystem.Layout.employee')
@section('content')
    <!-- start row -->


    <div class="col-lg-9">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <a href="index-3.html" class="btn btn-outline-white me-2">Admin Dashboard</a>
                    <a href="employee-dashboard-3.html" class="btn btn-primary">Employee Dashboard</a>
                </div>
            </div> <!-- end card body -->
        </div> <!-- end card -->

        <!-- start row -->
        <div class="row">

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0">Permission</h6>
                        <a href="leaves.html" class="btn btn-icon btn-sm btn-outline-white border-0"><i
                                class="ti ti-calendar-share"></i></a>
                    </div> <!-- end card header -->
                    <div class="card-body">
                        <div class="row g-0">
                            <div class="col-6">
                                <div class="text-center border-end">
                                    <span class="badge bg-light text-dark border fs-13 fw-medium mb-2">09.00 Hrs</span>
                                    <p class="mb-0 text-dark">Approved Hours</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <span class="badge bg-light text-dark border fs-13 fw-medium mb-2">11.00 Hrs</span>
                                    <p class="mb-0 text-dark">Remaining Hours</p>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end card body -->
                </div> <!-- end card -->

            </div> <!-- end col -->

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0">Leaves</h6>
                        <a href="leaves.html" class="btn btn-icon btn-sm btn-outline-white border-0"><i
                                class="ti ti-calendar-share"></i></a>
                    </div> <!-- end card header -->
                    <div class="card-body">
                        <div class="row g-0">
                            <div class="col-6">
                                <div class="text-center border-end">
                                    <span class="badge bg-light text-dark border fs-13 fw-medium mb-2">4.5 Days</span>
                                    <p class="mb-0 text-dark">Days Taken</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <span class="badge bg-light text-dark border fs-13 fw-medium mb-2">7.5 Days</span>
                                    <p class="mb-0 text-dark">Days Remaining</p>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end card body -->
                </div> <!-- end card -->

            </div> <!-- end col -->

        </div>
        <!-- end row -->

        <!-- start row -->
        <div class="row">

            <div class="col-lg-5 d-flex">
                <div class="card flex-fill">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold">Today</h6>
                        <a href="#" class="btn btn-sm btn-icon btn-outline-white border-0"><i
                                class="ti ti-refresh"></i></a>
                    </div> <!-- end card header -->
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <span
                                    class="avatar avatar-sm bg-soft-primary rounded-circle text-primary flex-shrink-0 me-2">
                                    <i class="ti ti-cake fs-16"></i>
                                </span>
                                <p class="mb-0">Daniel Martinz’s Birthday</p>
                            </div>
                            <span class="avatar avatar-sm avatar-rounded"><img
                                    src={{ asset('assets/img/employees/employee-10.jpg') }} alt=""></span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <span
                                    class="avatar avatar-sm bg-soft-primary rounded-circle text-primary flex-shrink-0 me-2">
                                    <i class="ti ti-cake fs-16"></i>
                                </span>
                                <p class="mb-0">Amelia Curr’s Birthday</p>
                            </div>
                            <span class="avatar avatar-sm avatar-rounded"><img
                                    src={{ asset('assets/img/employees/employee-09.jpg') }} alt=""></span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <span
                                    class="avatar avatar-sm bg-soft-primary rounded-circle text-primary flex-shrink-0 me-2">
                                    <i class="ti ti-cake fs-16"></i>
                                </span>
                                <p class="mb-0">Emma Lewis’s Birthday</p>
                            </div>
                            <span class="avatar avatar-sm avatar-rounded"><img
                                    src={{ asset('assets/img/employees/employee-08.jpg') }} alt=""></span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <span
                                    class="avatar avatar-sm bg-soft-secondary rounded-circle text-secondary flex-shrink-0 me-2">
                                    <i class="ti ti-calendar-star fs-16"></i>
                                </span>
                                <p class="mb-0">Madison Andrew is off sick today</p>
                            </div>
                            <span class="avatar avatar-sm avatar-rounded"><img
                                    src={{ asset('assets/img/managers/manager-09.jpg') }} alt=""></span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <span
                                    class="avatar avatar-sm bg-soft-secondary rounded-circle text-secondary flex-shrink-0 me-2">
                                    <i class="ti ti-calendar-star fs-16"></i>
                                </span>
                                <p class="mb-0">Victoria Celestie is off sick today</p>
                            </div>
                            <span class="avatar avatar-sm avatar-rounded"><img
                                    src={{ asset('assets/img/managers/manager-10.jpg') }} alt=""></span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <span
                                    class="avatar avatar-sm bg-soft-secondary rounded-circle text-secondary flex-shrink-0 me-2">
                                    <i class="ti ti-calendar-star fs-16"></i>
                                </span>
                                <p class="mb-0">Daniel Patrick is off sick today</p>
                            </div>
                            <span class="avatar avatar-sm avatar-rounded"><img
                                    src={{ asset('assets/img/managers/manager-03.jpg') }} alt=""></span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <span
                                    class="avatar avatar-sm bg-soft-secondary rounded-circle text-secondary flex-shrink-0 me-2">
                                    <i class="ti ti-calendar-star fs-16"></i>
                                </span>
                                <p class="mb-0">Jessica Renee is off sick today</p>
                            </div>
                            <span class="avatar avatar-sm avatar-rounded"><img
                                    src={{ asset('assets/img/managers/manager-06.jpg') }} alt=""></span>
                        </div>
                    </div> <!-- end card body -->
                </div> <!-- end card -->

            </div> <!-- end col -->

            <div class="col-lg-7 d-flex">
                <div class="card shadow flex-fill">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0">Team Leads</h6>
                        <a href="manage-team-lead.html" class="btn btn-sm btn-outline-white">Manage Team</a>
                    </div> <!-- end card header -->
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
                                                <a href="employee-details.html" class="avatar avatar-sm avatar-rounded">
                                                    <img src={{ asset('assets/img/employees/employee-03.jpg') }}
                                                        alt="img">
                                                </a>
                                                <div class="ms-2">
                                                    <h6 class="fs-14 mb-0"><a href="employee-details.html">Braun
                                                            Kelton</a></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-md badge-soft-primary">PHP</span></td>
                                        <td><a href="https://dleohr.dreamstechnologies.com/cdn-cgi/l/email-protection"
                                                class="__cf_email__"
                                                data-cfemail="75170714001b35100d14180519105b161a18">[email&#160;protected]</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <a href="employee-details.html" class="avatar avatar-sm avatar-rounded">
                                                    <img src={{ asset('assets/img/employees/employee-06.jpg') }}
                                                        alt="img">
                                                </a>
                                                <div class="ms-2">
                                                    <h6 class="fs-14 mb-0"><a href="employee-details.html">Sarah
                                                            Michelle</a></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-md badge-soft-pink">IOS</span></td>
                                        <td><a href="https://dleohr.dreamstechnologies.com/cdn-cgi/l/email-protection"
                                                class="__cf_email__"
                                                data-cfemail="03706271626b43667b626e736f662d606c6e">[email&#160;protected]</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <a href="employee-details.html" class="avatar avatar-sm avatar-rounded">
                                                    <img src={{ asset('assets/img/managers/manager-07.jpg') }}
                                                        alt="img">
                                                </a>
                                                <div class="ms-2">
                                                    <h6 class="fs-14 mb-0"><a href="employee-details.html">Daniel
                                                            Patrick</a></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-md badge-soft-orange">HTML</span></td>
                                        <td><a href="https://dleohr.dreamstechnologies.com/cdn-cgi/l/email-protection"
                                                class="__cf_email__"
                                                data-cfemail="6c080d020509002c09140d011c0009420f0301">[email&#160;protected]</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <a href="javascript:void(0);" class="avatar avatar-sm avatar-rounded">
                                                    <img src={{ asset('assets/img/employees/employee-08.jpg') }}
                                                        alt="img">
                                                </a>
                                                <div class="ms-2">
                                                    <h6 class="fs-14 mb-0"><a href="javascript:void(0);">Emily Clark</a>
                                                    </h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-md badge-soft-success">UI/UX</span></td>
                                        <td><a href="https://dleohr.dreamstechnologies.com/cdn-cgi/l/email-protection"
                                                class="__cf_email__"
                                                data-cfemail="fb9e96929782bb9e839a968b979ed5989496">[email&#160;protected]</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <a href="employee-details.html" class="avatar avatar-sm avatar-rounded">
                                                    <img src={{ asset('assets/img/managers/manager-05.jpg') }}
                                                        alt="img">
                                                </a>
                                                <div class="ms-2">
                                                    <h6 class="fs-14 mb-0"><a href="employee-details.html">Ryan
                                                            Christopher</a></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-md badge-soft-info">React</span></td>
                                        <td><a href="https://dleohr.dreamstechnologies.com/cdn-cgi/l/email-protection"
                                                class="__cf_email__"
                                                data-cfemail="75070c141b35100d14180519105b161a18">[email&#160;protected]</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card body -->
                </div> <!-- end card -->
            </div><!-- end col -->
        </div>
        <!-- end row -->

        <!-- start row -->
        <div class="row">

            <div class="col-xl-5 d-flex">
                <div class="card shadow flex-fill">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0">Recent Activities</h6>
                        <a href="#" class="btn btn-sm btn-icon btn-outline-white border-0"><i
                                class="ti ti-refresh"></i></a>
                    </div> <!-- end card header -->
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <a href="employee-details.html" class="avatar avatar-sm avatar-rounded flex-shrink-0">
                                    <img src={{ asset('assets/img/employees/employee-01.jpg') }} alt="img">
                                </a>
                                <div class="ms-2">
                                    <h6 class="fs-14 mb-1"><a href="employee-details.html">John Carter</a></h6>
                                    <p class="fs-13 mb-0 text-truncate">Added New Project HRMS Dashboard</p>
                                </div>
                            </div>
                            <span class="badge badge-soft-primary"><i class="ti ti-clock-hour-3 me-1"></i>06:20 PM</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <a href="employee-details.html" class="avatar avatar-sm avatar-rounded flex-shrink-0">
                                    <img src={{ asset('assets/img/employees/employee-02.jpg') }} alt="img">
                                </a>
                                <div class="ms-2">
                                    <h6 class="fs-14 mb-1"><a href="employee-details.html">Sophia White</a></h6>
                                    <p class="fs-13 mb-0 text-truncate">Commented on Uploaded Document</p>
                                </div>
                            </div>
                            <span class="badge badge-soft-primary"><i class="ti ti-clock-hour-3 me-1"></i>04:00 PM</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <a href="employee-details.html" class="avatar avatar-sm avatar-rounded flex-shrink-0">
                                    <img src={{ asset('assets/img/employees/employee-03.jpg') }} alt="img">
                                </a>
                                <div class="ms-2">
                                    <h6 class="fs-14 mb-1"><a href="employee-details.html">Michael Johnson</a></h6>
                                    <p class="fs-13 mb-0 text-truncate">Approved Task Projects</p>
                                </div>
                            </div>
                            <span class="badge badge-soft-primary"><i class="ti ti-clock-hour-3 me-1"></i>02:30 PM</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <a href="employee-details.html" class="avatar avatar-sm avatar-rounded flex-shrink-0">
                                    <img src={{ asset('assets/img/employees/employee-04.jpg') }} alt="img">
                                </a>
                                <div class="ms-2">
                                    <h6 class="fs-14 mb-1"><a href="employee-details.html">Emily Clark</a></h6>
                                    <p class="fs-13 mb-0 text-truncate">Requesting Access to Module Tickets</p>
                                </div>
                            </div>
                            <span class="badge badge-soft-primary"><i class="ti ti-clock-hour-3 me-1"></i>12:10 PM</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <a href="employee-details.html" class="avatar avatar-sm avatar-rounded flex-shrink-0">
                                    <img src={{ asset('assets/img/employees/employee-05.jpg') }} alt="img">
                                </a>
                                <div class="ms-2">
                                    <h6 class="fs-14 mb-1"><a href="employee-details.html">David Anderson</a></h6>
                                    <p class="fs-13 mb-0 text-truncate">Downloaded App Reports</p>
                                </div>
                            </div>
                            <span class="badge badge-soft-primary"><i class="ti ti-clock-hour-3 me-1"></i>10:40 AM</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <a href="employee-details.html" class="avatar avatar-sm avatar-rounded flex-shrink-0">
                                    <img src={{ asset('assets/img/employees/employee-06.jpg') }} alt="img">
                                </a>
                                <div class="ms-2">
                                    <h6 class="fs-14 mb-1"><a href="employee-details.html">Olivia Haris</a></h6>
                                    <p class="fs-13 mb-0 text-truncate">Completed ticket module in HRMS</p>
                                </div>
                            </div>
                            <span class="badge badge-soft-primary"><i class="ti ti-clock-hour-3 me-1"></i>09:50 AM</span>
                        </div>
                    </div> <!-- end card body -->
                </div> <!-- end card -->
            </div> <!-- end col -->

            <div class="col-xl-7 d-flex">
                <div class="card shadow flex-fill">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0">Upcoming Leaves</h6>
                        <a href="leaves.html" class="btn btn-sm btn-outline-white">Manage Leave</a>
                    </div> <!-- end card header -->
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
                                                <a href="employee-details.html" class="avatar avatar-sm avatar-rounded">
                                                    <img src={{ asset('assets/img/employees/employee-09.jpg') }}
                                                        alt="img">
                                                </a>
                                                <div class="ms-2">
                                                    <h6 class="fs-14 mb-0"><a href="employee-details.html">Daniel
                                                            Martinz</a></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>17 Apr 2025</td>
                                        <td><span class="badge badge-soft-teal">Sick Leave</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <a href="employee-details.html" class="avatar avatar-sm avatar-rounded">
                                                    <img src={{ asset('assets/img/employees/employee-04.jpg') }}
                                                        alt="img">
                                                </a>
                                                <div class="ms-2">
                                                    <h6 class="fs-14 mb-0"><a href="employee-details.html">Emily Clark</a>
                                                    </h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>20 Apr 2025</td>
                                        <td><span class="badge badge-soft-info">Casual Leave</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <a href="employee-details.html" class="avatar avatar-sm avatar-rounded">
                                                    <img src={{ asset('assets/img/managers/manager-03.jpg') }}
                                                        alt="img">
                                                </a>
                                                <div class="ms-2">
                                                    <h6 class="fs-14 mb-0"><a href="employee-details.html">Daniel
                                                            Patrick</a></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>22 Apr 2025</td>
                                        <td><span class="badge badge-soft-orange">Annual Leave</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <a href="javascript:void(0);" class="avatar avatar-sm avatar-rounded">
                                                    <img src={{ asset('assets/img/employees/employee-02.jpg') }}
                                                        alt="img">
                                                </a>
                                                <div class="ms-2">
                                                    <h6 class="fs-14 mb-0"><a href="javascript:void(0);">Sophia White</a>
                                                    </h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>28 Apr 2025</td>
                                        <td><span class="badge badge-soft-teal">Sick Leave</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <a href="employee-details.html" class="avatar avatar-sm avatar-rounded">
                                                    <img src={{ asset('assets/img/managers/manager-09.jpg') }}
                                                        alt="img">
                                                </a>
                                                <div class="ms-2">
                                                    <h6 class="fs-14 mb-0"><a href="employee-details.html">Madison
                                                            Andrew</a></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>30 Apr 2025</td>
                                        <td><span class="badge badge-soft-info">Casual Leave</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card body -->
                </div> <!-- end card -->
            </div> <!-- end col -->

        </div>
        <!-- end row -->

    </div> <!-- end col -->


    <!-- end row -->
@endsection
