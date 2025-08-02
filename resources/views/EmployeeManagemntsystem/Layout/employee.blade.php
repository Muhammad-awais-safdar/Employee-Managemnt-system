<!DOCTYPE html>
<html lang="en">



<head>

    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description"
        content="Dleohr is a clean and modern human resource management admin dashboard template which is based on HTML 5, Bootstrap 5. Try Demo and Buy Now!">
    <meta name="keywords"
        content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
    <title> @section('title') | Dleohr</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Muhammad Awais">

	<meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon -->
    <link rel="shortcut icon" href={{ asset('assets/img/favicon.png') }}>

    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href={{ asset('assets/img/apple-icon.png') }}>

    <!-- Theme Config Js -->
    <script src={{ asset('assets/js/theme-script.js') }}></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href={{ asset('assets/css/bootstrap.min.css') }}>

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href={{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}>
    <link rel="stylesheet" href={{ asset('assets/plugins/fontawesome/css/all.min.css') }}>

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href={{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}>

    <!-- Simplebar CSS -->
    <link rel="stylesheet" href={{ asset('assets/plugins/simplebar/simplebar.min.css') }}>

    <!-- Main CSS -->
    <link rel="stylesheet" href={{ asset('assets/css/style.css') }} id="app-style">

</head>

<body class="dashboard-vertical">

    <!-- Begin Wrapper -->
    <div class="main-wrapper">

        <!-- Topbar Start -->
        <header class="navbar-header d-lg-none">
            <div class="page-container topbar-menu">
                <div class="d-flex align-items-center gap-2">

                    @php
                        $user = Auth::user();
                        $companyLogo = null;
                        $companyName = 'Employee Management System';
                        $dashboardRoute = route('Employee.dashboard');
                        
                        if ($user && $user->company && $user->company->logo) {
                            $companyLogo = asset('storage/' . $user->company->logo);
                            $companyName = $user->company->name;
                        }
                        
                        // Fallback logos
                        $defaultLogo = asset('assets/img/logo.svg');
                        $defaultSmallLogo = asset('assets/img/logo-small.svg');
                        $defaultDarkLogo = asset('assets/img/logo-white.svg');
                    @endphp

                    <!-- Logo -->
                    <a href="#{{ $dashboardRoute }}" class="logo">

                        <!-- Logo Normal -->
                        <span class="logo-light">
                            <span class="logo-lg">
                                <img src="{{ $companyLogo ?: $defaultLogo }}" alt="{{ $companyName }} logo" style="max-height: 40px; width: auto;">
                            </span>
                            <span class="logo-sm">
                                <img src="{{ $companyLogo ?: $defaultSmallLogo }}" alt="{{ $companyName }} small logo" style="max-height: 30px; width: auto;">
                            </span>
                        </span>

                        <!-- Logo Dark -->
                        <span class="logo-dark">
                            <span class="logo-lg">
                                <img src="{{ $companyLogo ?: $defaultDarkLogo }}" alt="{{ $companyName }} dark logo" style="max-height: 40px; width: auto;">
                            </span>
                        </span>
                    </a>

                    <!-- Sidebar Mobile Button -->
                    <a id="mobile_btn" class="mobile-btn" href="##sidebar">
                        <i class="ti ti-menu-deep fs-24"></i>
                    </a>

                    <button class="sidenav-toggle-btn btn p-0" id="toggle_btn2">
                        <i class="ti ti-chevron-left-pipe"></i>
                    </button>

                    <!-- Search -->
                    <div class="me-auto d-flex align-items-center header-search d-lg-flex d-none">
                        <div class="input-icon-start position-relative">
                            <span class="input-icon-addon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="Search Keyword">
                            <span class="input-icon-addon text-dark fs-18 d-inline-flex p-0 header-search-icon"><i
                                    class="ti ti-command"></i></span>
                        </div>
                    </div>

                </div>

                <div class="d-flex align-items-center">

                    <!-- Search for Mobile -->
                    <div class="header-item d-flex d-lg-none me-2">
                        <button class="topbar-link btn btn-icon" data-bs-toggle="modal" data-bs-target="#searchModal"
                            type="button">
                            <i class="ti ti-search fs-16"></i>
                        </button>
                    </div>

                    <!-- Flag -->
                    <div class="header-item">
                        <div class="dropdown me-2">
                            <button class="topbar-link btn" data-bs-toggle="dropdown" data-bs-offset="0,24"
                                type="button" aria-haspopup="false" aria-expanded="false">
                                <img src={{ asset('assets/img/flags/us.svg') }} alt="Language" height="16">
                            </button>

                            <div class="dropdown-menu dropdown-menu-end">

                                <!-- item-->
                                <a href="#javascript:void(0);" class="dropdown-item">
                                    <img src={{ asset('assets/img/flags/us.svg') }} alt="" class="me-1"
                                        height="16"> <span class="align-middle">English</span>
                                </a>

                                <!-- item-->
                                <a href="#javascript:void(0);" class="dropdown-item">
                                    <img src={{ asset('assets/img/flags/de.svg') }} alt="" class="me-1"
                                        height="16"> <span class="align-middle">German</span>
                                </a>

                                <!-- item-->
                                <a href="#javascript:void(0);" class="dropdown-item">
                                    <img src={{ asset('assets/img/flags/fr.svg') }} alt="" class="me-1"
                                        height="16"> <span class="align-middle">French</span>
                                </a>

                                <!-- item-->
                                <a href="#javascript:void(0);" class="dropdown-item">
                                    <img src={{ asset('assets/img/flags/ae.svg') }} alt="" class="me-1"
                                        height="16"> <span class="align-middle">Arabic</span>
                                </a>

                            </div>
                        </div>
                    </div>

                    <!-- Full Screen -->
                    <div class="header-item">
                        <div class="me-2">
                            <a href="#javascript:void(0);" class="btn topbar-link" id="btnFullscreen"><i
                                    class="ti ti-maximize fs-16"></i></a>
                        </div>
                    </div>

                    <!-- Calendar -->
                    <div class="header-item">
                        <div class="me-2">
                            <a href="##" class="btn topbar-link"><i
                                    class="ti ti-calendar-star fs-16"></i></a>
                        </div>
                    </div>


                    <!-- Settings -->
                    <div class="header-item">
                        <div>
                            <a href="##" class="btn topbar-link"><i class="ti ti-settings fs-16"></i></a>
                        </div>
                    </div>

                </div>
            </div>
        </header>
        <!-- Topbar End -->

        <!-- Topbar Start -->
        <header class="navbar-header navbar-header-two">
            <div class="page-container topbar-menu">
                <div class="row flex-fill align-items-center">
                    <div class="col-lg-4">
                        <div class="navbar-logo">
                            <a href="#{{ route('Employee.dashboard') }}">
                                <img src="{{ (Auth::user() && Auth::user()->company && Auth::user()->company->logo) ? asset('storage/' . Auth::user()->company->logo) : asset('assets/img/logo-white.svg') }}" 
                                     alt="{{ (Auth::user() && Auth::user()->company) ? Auth::user()->company->name : 'Employee Management System' }}" 
                                     style="max-height: 45px; width: auto;">
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-8 text-end">
                        <div class="d-flex align-items-center justify-content-end">
                            <!-- Search -->
                            <div class="align-items-center header-search d-lg-flex d-none me-2">
                                <div class="input-icon-start position-relative">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" class="form-control" placeholder="Search Keyword">
                                    <span
                                        class="input-icon-addon text-dark fs-18 d-inline-flex p-0 header-search-icon"><i
                                            class="ti ti-command"></i></span>
                                </div>
                            </div>
                            <!-- Calendar -->

                            <!-- Light/Dark Mode Button -->
                            <div class="header-item d-none d-sm-flex me-2">
                                <button class="btn btn-icon topbar-link rounded-circle border-0 bg-white"
                                    id="light-dark-mode" type="button">
                                    <i class="ti ti-moon fs-16"></i>
                                </button>
                            </div>

                            <div class="header-item">
                                <div class="me-2">
                                    <a href="##"
                                        class="btn topbar-link rounded-circle border-0 bg-white"><i
                                            class="ti ti-calendar-star fs-16"></i></a>
                                </div>
                            </div>
                            <div class="dropdown profile-dropdown d-flex align-items-center justify-content-center">
                                <a href="#javascript:void(0);"
                                    class="topbar-link dropdown-toggle drop-arrow-none position-relative"
                                    data-bs-toggle="dropdown" data-bs-offset="0,22" aria-haspopup="false"
                                    aria-expanded="false">
                                    <img src={{ asset('assets/img/users/user-01.jpg') }} width="32"
                                        class="rounded-circle d-flex" alt="user-image">
                                    <span class="online text-success"><i
                                            class="ti ti-circle-filled d-flex bg-white rounded-circle border border-1 border-white"></i></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-md p-2">

                                    <div class="d-flex align-items-center bg-light rounded-3 p-2 mb-2">
                                        <img src={{ asset('assets/img/users/user-01.jpg') }} class="rounded-circle"
                                            width="42" height="42" alt="">
                                        <div class="ms-2">
                                            <p class="fw-medium text-dark mb-0">{{ Auth::user()->name }}</p>
                                            <span class="d-block fs-13">
                                                    {{ Auth::user()?->getRoleNames()->implode(', ') ?? 'No roles' }}

                                            </span>
                                        </div>
                                    </div>

                                    <!-- Item-->
                                    <a href="##" class="dropdown-item">
                                        <i class="ti ti-settings me-1 align-middle"></i>
                                        <span class="align-middle">Settings</span>
                                    </a>

                                    <!-- item -->
                                    <div
                                        class="form-check form-switch form-check-reverse d-flex align-items-center justify-content-between dropdown-item mb-0">
                                        <label class="form-check-label" for="notify"><i
                                                class="ti ti-bell me-1"></i>Notifications</label>
                                        <input class="form-check-input me-0" type="checkbox" role="switch"
                                            id="notify">
                                    </div>

                                    <!-- Item-->
                                    <div class="pt-2 mt-2 border-top">
                                        <a href={{ route('logout') }} class="dropdown-item text-danger">
                                            <i class="ti ti-logout me-1 fs-17 align-middle"></i>
                                            <span class="align-middle">Log Out</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Topbar End -->

        <!-- Search Modal -->
        <div class="modal fade" id="searchModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content bg-transparent">
                    <div class="card shadow-none mb-0">
                        <div class="px-3 py-2 d-flex flex-row align-items-center" id="search-top">
                            <i class="ti ti-search fs-22"></i>
                            <input type="search" class="form-control border-0" placeholder="Search">
                            <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close"><i
                                    class="ti ti-x fs-22"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidenav Menu Start -->
        <div class="sidebar d-lg-none" id="sidebar">

            <!-- Start Logo -->
            <div class="sidebar-logo">
                <div>
                    @php
                        $employeeUser = Auth::user();
                        $employeeCompanyLogo = null;
                        $employeeCompanyName = 'Employee Management System';
                        
                        if ($employeeUser && $employeeUser->company && $employeeUser->company->logo) {
                            $employeeCompanyLogo = asset('storage/' . $employeeUser->company->logo);
                            $employeeCompanyName = $employeeUser->company->name;
                        }
                    @endphp

                    <!-- Logo Normal -->
                    <a href="#{{ route('Employee.dashboard') }}" class="logo logo-normal">
                        <img src="{{ $employeeCompanyLogo ?: asset('assets/img/logo.svg') }}" alt="{{ $employeeCompanyName }} Logo" style="max-height: 45px; width: auto;">
                    </a>

                    <!-- Logo Small -->
                    <a href="#{{ route('Employee.dashboard') }}" class="logo-small">
                        <img src="{{ $employeeCompanyLogo ?: asset('assets/img/logo-small.svg') }}" alt="{{ $employeeCompanyName }} Logo" style="max-height: 35px; width: auto;">
                    </a>

                    <!-- Logo Dark -->
                    <a href="#{{ route('Employee.dashboard') }}" class="dark-logo">
                        <img src="{{ $employeeCompanyLogo ?: asset('assets/img/logo-white.svg') }}" alt="{{ $employeeCompanyName }} Logo" style="max-height: 45px; width: auto;">
                    </a>
                </div>
                <button class="sidenav-toggle-btn btn p-0" id="toggle_btn">
                    <i class="ti ti-chevron-left-pipe"></i>
                </button>

                <!-- Sidebar Menu Close -->
                <button class="sidebar-close">
                    <i class="ti ti-x align-middle"></i>
                </button>
            </div>
            <!-- End Logo -->

            <!-- Sidenav Menu -->
            <div class="sidebar-inner" data-simplebar>
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title"><span>Main Menu</span></li>
                        <li>
                            <ul>
                                <li class="submenu">
                                    <a href="#javascript:void(0);" class="active subdrop">
                                        <i class="ti ti-layout-dashboard"></i><span>Dashboard</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="#{{ route('dashboard') }}">Dashboard 1</a></li>
                                        <li><a href="##">Dashboard 2</a></li>
                                        <li><a href="##" class="active">Dashboard 3</a></li>
                                        <li><a href="##">Dashboard 4</a></li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="#javascript:void(0);">
                                        <i class="ti ti-apps"></i><span>Applications</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="#chat">Chat</a></li>
                                        <li class="submenu submenu-two">
                                            <a href="##">Calls<span class="menu-arrow inside-submenu"></span></a>
                                            <ul>
                                                <li><a href="#voice-call">Voice Call</a></li>
                                                <li><a href="#video-call">Video Call</a></li>
                                                <li><a href="#outgoing-call">Outgoing Call</a></li>
                                                <li><a href="#incoming-call">Incoming Call</a></li>
                                                <li><a href="#call-history">Call History</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="#calendar">Calendar</a></li>
                                        <li><a href="#contacts">Contacts</a></li>
                                        <li><a href="#email">Email</a></li>
                                        <li class="submenu submenu-two">
                                            <a href="##">Invoices<span
                                                    class="menu-arrow inside-submenu"></span></a>
                                            <ul>
                                                <li><a href="#invoice">Invoices</a></li>
                                                <li><a href="#invoice-details">Invoice Details</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="#todo">To Do</a></li>
                                        <li><a href="#notes">Notes</a></li>
                                        <li><a href="#kanban-view">Kanban Board</a></li>
                                        <li><a href="#file-manager">File Manager</a></li>
                                        <li><a href="#social-feed">Social Feed</a></li>
                                        <li><a href="#search-list">Search Result</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="menu-title"><span>Peoples & Teams</span></li>
                        <li>
                            <ul>
                                <li>
                                    <a href="#companies">
                                        <i class="ti ti-building-community"></i><span>Companies</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#employees">
                                        <i class="ti ti-users-group"></i><span>Employee</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#leaves">
                                        <i class="ti ti-calendar-star"></i><span>Leaves</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#reviews">
                                        <i class="ti ti-user-bolt"></i><span>Reviews</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="menu-title"><span>Utilities & Reports</span></li>
                        <li>
                            <ul>
                                <li>
                                    <a href="##">
                                        <i class="ti ti-calendar-event"></i><span>Calendar</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#team-report">
                                        <i class="ti ti-report"></i><span>Reports</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#manage">
                                        <i class="ti ti-settings-2"></i><span>Manage</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="menu-title"><span>Settings</span></li>
                        <li>
                            <ul>
                                <li>
                                    <a href="##">
                                        <i class="ti ti-settings"></i><span>Settings</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="menu-title"><span>Authentication</span></li>
                        <li>
                            <ul>
                                <li>
                                    <a href="#login">
                                        <i class="ti ti-login"></i><span>Login</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#register">
                                        <i class="ti ti-report"></i><span>Register</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#forgot-password">
                                        <i class="ti ti-lock-exclamation"></i><span>Forgot Password</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#reset-password">
                                        <i class="ti ti-restore"></i><span>Reset Password</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#email-verification">
                                        <i class="ti ti-mail-check"></i><span>Email Verification</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#two-step-verification">
                                        <i class="ti ti-discount-check"></i><span>2 Step Verification</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#lock-screen">
                                        <i class="ti ti-lock-square-rounded"></i><span>Lock Screen</span>
                                    </a>
                                </li>
                                <li class="submenu">
                                    <a href="#javascript:void(0);">
                                        <i class="ti ti-exclamation-mark-off"></i><span>Error Pages</span><span
                                            class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="#error-404">404 Error</a></li>
                                        <li><a href="#error-500">500 Error</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="menu-title"><span>UI Interface</span></li>
                        <li>
                            <ul>
                                <li class="submenu">
                                    <a href="#javascript:void(0);">
                                        <i class="ti ti-chart-pie"></i><span>Base UI</span><span
                                            class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="#ui-accordion">Accordion</a></li>
                                        <li><a href="#ui-alerts">Alerts</a></li>
                                        <li><a href="#ui-avatar">Avatar</a></li>
                                        <li><a href="#ui-badges">Badges</a></li>
                                        <li><a href="#ui-breadcrumb">Breadcrumb</a></li>
                                        <li><a href="#ui-buttons">Buttons</a></li>
                                        <li><a href="#ui-buttons-group">Button Group</a></li>
                                        <li><a href="#ui-cards">Card</a></li>
                                        <li><a href="#ui-carousel">Carousel</a></li>
                                        <li><a href="#ui-collapse">Collapse</a></li>
                                        <li><a href="#ui-dropdowns">Dropdowns</a></li>
                                        <li><a href="#ui-ratio">Ratio</a></li>
                                        <li><a href="#ui-grid">Grid</a></li>
                                        <li><a href="#ui-images">Images</a></li>
                                        <li><a href="#ui-links">Links</a></li>
                                        <li><a href="#ui-list-group">List Group</a></li>
                                        <li><a href="#ui-modals">Modals</a></li>
                                        <li><a href="#ui-offcanvas">Offcanvas</a></li>
                                        <li><a href="#ui-pagination">Pagination</a></li>
                                        <li><a href="#ui-placeholders">Placeholders</a></li>
                                        <li><a href="#ui-popovers">Popovers</a></li>
                                        <li><a href="#ui-progress">Progress</a></li>
                                        <li><a href="#ui-scrollspy">Scrollspy</a></li>
                                        <li><a href="#ui-spinner">Spinner</a></li>
                                        <li><a href="#ui-nav-tabs">Tabs</a></li>
                                        <li><a href="#ui-toasts">Toasts</a></li>
                                        <li><a href="#ui-tooltips">Tooltips</a></li>
                                        <li><a href="#ui-typography">Typography</a></li>
                                        <li><a href="#ui-utilities">Utilities</a></li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="#javascript:void(0);">
                                        <i class="ti ti-radar"></i><span>Advanced UI</span><span
                                            class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="#extended-dragula">Dragula</a></li>
                                        <li><a href="#ui-clipboard">Clipboard</a></li>
                                        <li><a href="#ui-rangeslider">Range Slider</a></li>
                                        <li><a href="#ui-sweetalerts">Sweet Alerts</a></li>
                                        <li><a href="#ui-lightbox">Lightbox</a></li>
                                        <li><a href="#ui-rating">Rating</a></li>
                                        <li><a href="#ui-scrollbar">Scrollbar</a></li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="#javascript:void(0);">
                                        <i class="ti ti-forms"></i><span>Forms</span><span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li class="submenu submenu-two">
                                            <a href="#javascript:void(0);">Form Elements<span
                                                    class="menu-arrow inside-submenu"></span></a>
                                            <ul>
                                                <li><a href="#form-basic-inputs">Basic Inputs</a></li>
                                                <li><a href="#form-checkbox-radios">Checkbox & Radios</a></li>
                                                <li><a href="#form-input-groups">Input Groups</a></li>
                                                <li><a href="#form-grid-gutters">Grid & Gutters</a></li>
                                                <li><a href="#form-mask">Input Masks</a></li>
                                                <li><a href="#form-fileupload">File Uploads</a></li>
                                            </ul>
                                        </li>
                                        <li class="submenu submenu-two">
                                            <a href="#javascript:void(0);">Layouts<span
                                                    class="menu-arrow inside-submenu"></span></a>
                                            <ul>
                                                <li><a href="#form-horizontal">Horizontal Form</a></li>
                                                <li><a href="#form-vertical">Vertical Form</a></li>
                                                <li><a href="#form-floating-labels">Floating Labels</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="#form-validation">Form Validation</a></li>
                                        <li><a href="#form-select2">Select2</a></li>
                                        <li><a href="#form-wizard">Form Wizard</a></li>
                                        <li><a href="#form-pickers">Form Picker</a></li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="#javascript:void(0);">
                                        <i class="ti ti-table-row"></i><span>Tables</span><span
                                            class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="#tables-basic">Basic Tables </a></li>
                                        <li><a href="#data-tables">Data Table </a></li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="#javascript:void(0);">
                                        <i class="ti ti-chart-donut"></i>
                                        <span>Charts</span><span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="#chart-apex">Apex Charts</a></li>
                                        <li><a href="#chart-c3">Chart C3</a></li>
                                        <li><a href="#chart-js">Chart Js</a></li>
                                        <li><a href="#chart-morris">Morris Charts</a></li>
                                        <li><a href="#chart-flot">Flot Charts</a></li>
                                        <li><a href="#chart-peity">Peity Charts</a></li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="#javascript:void(0);">
                                        <i class="ti ti-icons"></i>
                                        <span>Icons</span><span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="#icon-fontawesome">Fontawesome Icons</a></li>
                                        <li><a href="#icon-tabler">Tabler Icons</a></li>
                                        <li><a href="#icon-bootstrap">Bootstrap Icons</a></li>
                                        <li><a href="#icon-remix">Remix Icons</a></li>
                                        <li><a href="#icon-feather">Feather Icons</a></li>
                                        <li><a href="#icon-ionic">Ionic Icons</a></li>
                                        <li><a href="#icon-material">Material Icons</a></li>
                                        <li><a href="#icon-pe7">Pe7 Icons</a></li>
                                        <li><a href="#icon-simpleline">Simpleline Icons</a></li>
                                        <li><a href="#icon-themify">Themify Icons</a></li>
                                        <li><a href="#icon-weather">Weather Icons</a></li>
                                        <li><a href="#icon-typicons">Typicons Icons</a></li>
                                        <li><a href="#icon-flag">Flag Icons</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="menu-title"><span>Help</span></li>
                        <li>
                            <ul>
                                <li>
                                    <a href="#javascript:void(0);"><i
                                            class="ti ti-file-dots"></i><span>Documentation</span></a>
                                </li>
                                <li>
                                    <a href="#javascript:void(0);"><i
                                            class="ti ti-status-change"></i><span>Changelog</span><span
                                            class="badge bg-danger ms-2 badge-md rounded-2 fs-12 fw-medium">v2.0</span></a>
                                </li>
                                <li class="submenu">
                                    <a href="#javascript:void(0);">
                                        <i class="ti ti-versions"></i><span>Multi Level</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="#javascript:void(0);">Multilevel 1</a></li>
                                        <li class="submenu submenu-two">
                                            <a href="#javascript:void(0);">Multilevel 2<span
                                                    class="menu-arrow inside-submenu"></span></a>
                                            <ul>
                                                <li><a href="#javascript:void(0);">Multilevel 2.1</a></li>
                                                <li class="submenu submenu-two submenu-three">
                                                    <a href="#javascript:void(0);">Multilevel 2.2<span
                                                            class="menu-arrow inside-submenu inside-submenu-two"></span></a>
                                                    <ul>
                                                        <li><a href="#javascript:void(0);">Multilevel 2.2.1</a></li>
                                                        <li><a href="#javascript:void(0);">Multilevel 2.2.2</a></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                                        <li><a href="#javascript:void(0);">Multilevel 3</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="sidebar-footer">
                    <div class="bg-light p-2 rounded d-flex align-items-center">
                        <a href="##" class="avatar avatar-md me-2"><img
                                src={{ asset('assets/img/users/avatar-2.jpg') }} alt=""></a>
                        <div>
                            <h6 class="fs-14 fw-semibold mb-1"><a href="##">Joseph Smith</a></h6>
                            <p class="fs-13 mb-0"><a
                                    href="#https://dleohr.dreamstechnologies.com/cdn-cgi/l/email-protection"
                                    class="__cf_email__"
                                    data-cfemail="7d1c191014133d18051c100d1118531e1210">[email&#160;protected]</a></p>
                        </div>
                    </div>
                </div>
                <div class="p-3 pt-0">
                    <a href="#login" class="btn btn-danger w-100"><i class="ti ti-logout-2 me-1"></i>Logout</a>
                </div>
            </div>

        </div>
        <!-- Sidenav Menu End -->

        <!-- ========================
   Start Page Content
  ========================= -->

        <div class="page-wrapper ms-0">

            <!-- Start Content -->
            <div class="content pb-0">
                <div class="row">
                    <div class="col-lg-3 theiaStickySidebar">
                        <div>
                            <div class="card">
                                <div class="card-body">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb breadcrumb-divide p-0 mb-2">
                                            <li class="breadcrumb-item d-flex align-items-center fw-medium"><a
                                                    href="#{{ route('dashboard') }}">Home</a></li>
                                            <li class="breadcrumb-item active fw-medium" aria-current="page">Dashboard
                                            </li>
                                        </ol>
                                    </nav>
                                    <h5 class="fw-bold mb-0">Employee Dashboard</h5>
                                </div> <!-- end card body -->
                            </div> <!-- end card -->

                            <div class="card">
                                <div class="card-body text-center">
                                    <span class="avatar avatar-xxl avatar-rounded mb-2">
                                        <img src={{ asset('assets/img/managers/manager-01.jpg') }} alt="">
                                    </span>
                                    <h6 class="fw-bold mb-1">{{Auth::user()->name}}</h6>
                                    <p class="mb-0">{{ date('d-m-Y') }}</p>
                                </div> <!-- end card body -->
                            </div> <!-- end card -->

                            <div class="card vertical-sedebar">
                                <div class="card-body">
                                    <div class="row g-0 horizontal-nav">
                                        <div class="col-lg-6">
                                            <a href={{ route('Employee.dashboard') }}
                                                class="d-flex flex-column align-items-center active p-3">
                                                <i class="ti ti-home fs-20 mb-1"></i>Dashboard
                                            </a>
                                        </div>
                                        <div class="col-lg-6">
                                            <a href={{ route('Employee.attendance.index') }}
                                                class="d-flex flex-column align-items-center p-3">
                                                <i class="ti ti-users fs-20 mb-1"></i>Attendence
                                            </a>
                                        </div>
                                        <div class="col-lg-6">
                                            <a href="#companies"
                                                class="d-flex flex-column align-items-center p-3">
                                                <i class="ti ti-building-community fs-20 mb-1"></i>Companies
                                            </a>
                                        </div>
                                        <div class="col-lg-6">
                                            <a href="##"
                                                class="d-flex flex-column align-items-center p-3">
                                                <i class="ti ti-calendar fs-20 mb-1"></i>Calendar
                                            </a>
                                        </div>
                                        <div class="col-lg-6">
                                            <a href={{ route('Employee.leave.index') }} class="d-flex flex-column align-items-center p-3">
                                                <i class="ti ti-calendar-star fs-20 mb-1"></i>Leaves
                                            </a>
                                        </div>
                                        <div class="col-lg-6">
                                            <a href="#reviews" class="d-flex flex-column align-items-center p-3">
                                                <i class="ti ti-stars fs-20 mb-1"></i>Reviews
                                            </a>
                                        </div>
                                        <div class="col-lg-6">
                                            <a href="#team-report"
                                                class="d-flex flex-column align-items-center p-3">
                                                <i class="ti ti-report fs-20 mb-1"></i>Reports
                                            </a>
                                        </div>
                                        <div class="col-lg-6">
                                            <a href="#manage" class="d-flex flex-column align-items-center p-3">
                                                <i class="ti ti-settings-2 fs-20 mb-1"></i>Manage
                                            </a>
                                        </div>
                                        <div class="col-lg-6">
                                            <a href="##" class="d-flex flex-column align-items-center p-3">
                                                <i class="ti ti-user-edit fs-20 mb-1"></i>Profile
                                            </a>
                                        </div>
                                        <div class="col-lg-6">
                                            <a href="##" class="d-flex flex-column align-items-center p-3">
                                                <i class="ti ti-settings fs-20 mb-1"></i>Settings
                                            </a>
                                        </div>
                                    </div>
                                </div> <!-- end card body -->
                            </div> <!-- end card -->

                        </div>
                    </div> <!-- end col -->
                    @yield('content')
                </div>
            </div>
            <!-- End Content -->

            <!-- Footer Start -->
            <div
                class="footer d-flex align-items-center justify-content-between flex-column flex-sm-row row-gap-2 border-top py-2 px-3">
                <p class="text-dark mb-0">2025 &copy; <a href="#javascript:void(0);" class="link-primary">Awais</a>,
                    All Rights
                    Reserved</p>
                <p class="text-dark mb-0">Design & Developed by <a href="##"
                        target="_blank" class="link-primary">Muhammad Awais</a></p>
            </div>
            <!-- Footer End -->

        </div>

        <!-- ========================
            End Page Content
            ========================= -->

    </div>
    <!-- End Wrapper -->
    <script src={{ asset('assets/js/jquery-3.7.1.min.js') }}></script>

    <!-- Bootstrap Core JS -->
    <script src={{ asset('assets/js/bootstrap.bundle.min.js') }}></script>

    <!-- Simplebar JS -->
    <script src={{ asset('assets/plugins/simplebar/simplebar.min.js') }}></script>

    <!-- Chart JS -->
    <script src={{ asset('assets/plugins/apexchart/apexcharts.min.js') }}></script>
    <script src={{ asset('assets/plugins/apexchart/chart-data.js') }}></script>

    <!-- Sticky Sidebar JS -->
    <script src={{ asset('assets/plugins/theia-sticky-sidebar/ResizeSensor.js') }}
            ></script>
    <script src={{ asset('assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}
            ></script>
    <!-- jQuery JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="#https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css">
    
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
    
    <script>
        // Toastr configuration
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    </script>
    @stack('scripts')   
    <!-- Main JS -->
    <script src={{ asset('assets/js/script.js') }}></script>
</body>



</html>
