
<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from dleohr.dreamstechnologies.com/html/template/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 25 Jul 2025 08:05:57 GMT -->
<head>

	<!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Dleohr is a clean and modern human resource management admin dashboard template which is based on HTML 5, Bootstrap 5. Try Demo and Buy Now!">
	<meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
    <title> @section('title') | Dleohr</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Dreams Technologies">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	
    <!-- Favicon -->
    <link rel="shortcut icon" href={{ asset('assets/img/favicon.png') }}>

    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href={{ asset('assets/img/apple-icon.png') }}>

    <!-- Theme Config Js -->
    <script src={{ asset('assets/js/theme-script.js') }} ></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href={{ asset('assets/css/bootstrap.min.css') }}>

    <!-- ChartC3 CSS -->
    <link rel="stylesheet" href={{ asset('assets/plugins/c3-chart/c3.min.css') }}>

    <!-- Fontawesome CSS -->
	<link rel="stylesheet" href={{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}>
	<link rel="stylesheet" href={{ asset('assets/plugins/fontawesome/css/all.min.css') }}>

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href={{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}>

    <!-- Simplebar CSS -->
    <link rel="stylesheet" href={{ asset('assets/plugins/simplebar/simplebar.min.css') }}>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- Main CSS -->
    <link rel="stylesheet" href={{ asset('assets/css/style.css') }} id="app-style">

</head>

<body>

    <!-- Begin Wrapper -->
    <div class="main-wrapper">

        <!-- Topbar Start -->
        <header class="navbar-header">
            <div class="page-container topbar-menu">
                <div class="d-flex align-items-center gap-2">

                    <!-- Logo -->
                    <a href={{ asset('index.html') }} class="logo">

                        @php
                            $user = Auth::user();
                            $companyLogo = null;
                            $companyName = 'Employee Management System';
                            
                            if ($user && $user->company && $user->company->logo) {
                                $companyLogo = asset('storage/' . $user->company->logo);
                                $companyName = $user->company->name;
                            }
                            
                            // Fallback logos
                            $defaultLogo = asset('assets/img/logo.svg');
                            $defaultSmallLogo = asset('assets/img/logo-small.svg');
                            $defaultDarkLogo = asset('assets/img/logo-white.svg');
                        @endphp

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
                    <a id="mobile_btn" class="mobile-btn" href="#sidebar">
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
                           <span class="input-icon-addon text-dark fs-18 d-inline-flex p-0 header-search-icon"><i class="ti ti-command"></i></span>
                        </div>
                    </div>
					
                </div>

                <div class="d-flex align-items-center">
				
                    <!-- Search for Mobile -->
                    <div class="header-item d-flex d-lg-none me-2">
                        <button class="topbar-link btn btn-icon" data-bs-toggle="modal" data-bs-target="#searchModal" type="button">
                            <i class="ti ti-search fs-16"></i>
                        </button>
                    </div>

					<!-- Flag -->
                    <div class="header-item">
						<div class="dropdown me-2">
                            <button class="topbar-link btn" data-bs-toggle="dropdown" data-bs-offset="0,24" type="button" aria-haspopup="false" aria-expanded="false">
                                <img src={{ asset('assets/img/flags/us.svg') }} alt="Language" height="16">
                            </button>
							
							<div class="dropdown-menu dropdown-menu-end">

                                <!-- item-->
								<a href="javascript:void(0);" class="dropdown-item">
									<img src={{ asset('assets/img/flags/us.svg') }} alt="" class="me-1" height="16"> <span class="align-middle">English</span>
								</a>

                                <!-- item-->
								<a href="javascript:void(0);" class="dropdown-item">
									<img src={{ asset('assets/img/flags/de.svg') }} alt="" class="me-1" height="16"> <span class="align-middle">German</span>
								</a>

                                <!-- item-->
								<a href="javascript:void(0);" class="dropdown-item">
									<img src={{ asset('assets/img/flags/fr.svg') }} alt="" class="me-1" height="16"> <span class="align-middle">French</span>
								</a>

                                <!-- item-->
								<a href="javascript:void(0);" class="dropdown-item">
									<img src={{ asset('assets/img/flags/ae.svg') }} alt="" class="me-1" height="16"> <span class="align-middle">Arabic</span>
								</a>
								
							</div>
						</div>
                    </div>

                    <!-- Full Screen -->
                    <div class="header-item">
                        <div class="me-2">
                            <a href="javascript:void(0);" class="btn topbar-link" id="btnFullscreen"><i class="ti ti-maximize fs-16"></i></a>
                        </div> 
                    </div>                  

                    <!-- Light/Dark Mode Button -->
                    <div class="header-item d-none d-sm-flex me-2">
                        <button class="topbar-link btn btn-icon topbar-link" id="light-dark-mode" type="button">
                            <i class="ti ti-moon fs-16"></i>
                        </button>
                    </div>

                    <!-- Calendar -->
                    <div class="header-item">
                        <div class="me-2">
                            <a href={{ asset('report-calendar.html') }} class="btn topbar-link"><i class="ti ti-calendar-star fs-16"></i></a>
                        </div>
                    </div>
					
					<!-- Notification Dropdown -->
                    @include('Component.NotificationDropdown')

                    <!-- Settings -->
                    <div class="header-item">
                        <div>
                            <a href={{ asset('settings.html') }} class="btn topbar-link"><i class="ti ti-settings fs-16"></i></a>
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
                            <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close"><i class="ti ti-x fs-22"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidenav Menu Start -->
      @include('Component.Sidebar')
        <!-- Sidenav Menu End -->

        <!-- ========================
			Start Page Content
		========================= -->
         
        <div class="page-wrapper">

            <!-- Start Content -->
            <div class="content pb-0">

            @yield('content')           
            </div>
            <!-- End Content -->

            <!-- Footer Start -->
            <div class="footer d-flex align-items-center justify-content-between flex-column flex-sm-row row-gap-2 border-top py-2 px-3">
                <p class="text-dark mb-0">2025 &copy; <a href="javascript:void(0);" class="link-primary">Dleo HR</a>, All Rights Reserved</p>
                <p class="text-dark mb-0">Design & Developed by <a href="https://dreamstechnologies.com/" target="_blank" class="link-primary">Dreams Technologies</a></p>
            </div>
            <!-- Footer End -->

        </div>

        <!-- ========================
			End Page Content
		========================= -->

         <!-- Start Modal  -->
        <div class="modal fade" id="delete_modal">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <div class="mb-3">
                            <span class="avatar bg-danger"><i class="ti ti-trash fs-24"></i></span>
                        </div>
                        <h6 class="mb-1">Delete Confirmation</h6>
                        <p class="mb-3">Are you sure want to delete todo?</p>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-outline-white w-100 me-2" data-bs-dismiss="modal">Cancel</a>
                            <a href={{ asset('index.html') }} class="btn btn-danger w-100">Yes, Delete</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal  -->

    </div>
    <!-- End Wrapper -->

    <!-- jQuery JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css">
    
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

    <!-- Bootstrap Core JS -->
    <script src={{ asset('assets/js/bootstrap.bundle.min.js') }} ></script>    

	<!-- Simplebar JS -->
	<script src={{ asset('assets/plugins/simplebar/simplebar.min.js') }} ></script>

    <!-- Chart JS -->
    <script src={{ asset('assets/plugins/apexchart/apexcharts.min.js') }} ></script>
    <script src={{ asset('assets/plugins/apexchart/chart-data.js') }} ></script>

    <!-- Chart JS -->
    <script src={{ asset('assets/plugins/c3-chart/d3.v5.min.js') }} ></script>
    <script src={{ asset('assets/plugins/c3-chart/c3.min.js') }} ></script>
    <script src={{ asset('assets/plugins/c3-chart/chart-data.js') }} ></script>

    <!-- Main JS -->
    <script src={{ asset('assets/js/script.js') }} ></script>
    @stack('scripts')
</body>
</html>