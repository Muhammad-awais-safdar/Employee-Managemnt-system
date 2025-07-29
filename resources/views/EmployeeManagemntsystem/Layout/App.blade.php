
<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from dleohr.dreamstechnologies.com/html/template/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 25 Jul 2025 08:05:57 GMT -->
<head>

	<!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Dleohr is a clean and modern human resource management admin dashboard template which is based on HTML 5, Bootstrap 5. Try Demo and Buy Now!">
	<meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
    <title>Dashboard | Dleohr</title>
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

                        <!-- Logo Normal -->
                        <span class="logo-light">
                            <span class="logo-lg"><img src={{ asset('assets/img/logo.svg') }} alt="logo"></span>
                            <span class="logo-sm"><img src={{ asset('assets/img/logo-small.svg') }} alt="small logo"></span>
                        </span>

                        <!-- Logo Dark -->
                        <span class="logo-dark">
                            <span class="logo-lg"><img src={{ asset('assets/img/logo-white.svg') }} alt="dark logo"></span>
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
                    <div class="header-item">
						<div class="dropdown me-2">
						
							<button class="topbar-link btn btn-icon topbar-link dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown" data-bs-offset="0,24" type="button" aria-haspopup="false" aria-expanded="false">
								<i class="ti ti-bell fs-16 animate-ring fs-16"></i>
								<span class="notification-badge"></span>
							</button>
							
							<div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-lg" style="min-height: 300px;">
							
								<div class="p-2 border-bottom">
									<div class="row align-items-center">
										<div class="col">
											<h6 class="m-0 fs-16 fw-semibold"> Notifications</h6>
										</div>
									</div>
								</div>
								
								<!-- Notification Body -->
								<div class="notification-body position-relative z-2 rounded-0" data-simplebar>
								 
									<!-- Item-->
									<div class="dropdown-item notification-item py-3 text-wrap border-bottom" id="notification-1">
										<div class="d-flex">
											<div class="me-2 position-relative flex-shrink-0">
												<img src={{ asset('assets/img/users/avatar-2.jpg') }} class="avatar-md rounded-circle" alt="">
											</div>
											<div class="flex-grow-1">
												<p class="mb-0 fw-medium text-dark">Daniel Martinz</p>
												<p class="mb-1 text-wrap">
													<span class="fw-medium text-dark">Daniel Martinz</span> equested Sick Leave from May 28 2025 to May 29 2025
												</p>
												<div class="d-flex justify-content-between align-items-center">
													<span class="fs-12"><i class="ti ti-clock me-1"></i>4 min ago</span>
													<div class="notification-action d-flex align-items-center float-end gap-2">
														<a href="javascript:void(0);" class="notification-read rounded-circle bg-danger" data-bs-toggle="tooltip" title="" data-bs-original-title="Make as Read" aria-label="Make as Read"></a>
														<button class="btn rounded-circle p-0" data-dismissible="#notification-1">
															<i class="ti ti-x"></i>
														</button>
													</div>
												</div>
											</div>
										</div>
									</div>
							
									<!-- Item-->
									<div class="dropdown-item notification-item py-3 text-wrap border-bottom" id="notification-2">
										<div class="d-flex">
											<div class="me-2 position-relative flex-shrink-0">
												<img src={{ asset('assets/img/users/user-02.jpg') }} class="avatar-md rounded-circle" alt="">
											</div>
											<div class="flex-grow-1">
												<p class="mb-0 fw-medium text-dark">Emily Clark</p>
												<p class="mb-1 text-wrap">
                                                    Leave for  <span class="fw-medium text-dark"> Emily Clark</span>  has been approved.
												</p>
												<div class="d-flex justify-content-between align-items-center">
													<span class="fs-12"><i class="ti ti-clock me-1"></i>8 min ago</span>
													<div class="notification-action d-flex align-items-center float-end gap-2">
														<a href="javascript:void(0);" class="notification-read rounded-circle bg-danger" data-bs-toggle="tooltip" title="" data-bs-original-title="Make as Read" aria-label="Make as Read"></a>
														<button class="btn rounded-circle p-0" data-dismissible="#notification-2">
															<i class="ti ti-x"></i>
														</button>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<!-- Item-->
									<div class="dropdown-item notification-item py-3 text-wrap border-bottom" id="notification-3">
										<div class="d-flex">
											<div class="me-2 position-relative flex-shrink-0">
												<img src={{ asset('assets/img/users/user-04.jpg') }} class="avatar-md rounded-circle" alt="">
											</div>
											<div class="flex-grow-1">
												<p class="mb-0 fw-medium text-dark"> David</p>
												<p class="mb-1 text-wrap">
                                                    Leave request from  <span class="fw-medium text-dark">David Anderson</span>has been rejected.
												</p>
												<div class="d-flex justify-content-between align-items-center">
													<span class="fs-12"><i class="ti ti-clock me-1"></i>15 min ago</span>
													<div class="notification-action d-flex align-items-center float-end gap-2">
														<a href="javascript:void(0);" class="notification-read rounded-circle bg-danger" data-bs-toggle="tooltip" title="" data-bs-original-title="Make as Read" aria-label="Make as Read"></a>
														<button class="btn rounded-circle p-0" data-dismissible="#notification-3">
															<i class="ti ti-x"></i>
														</button>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<!-- Item-->
									<div class="dropdown-item notification-item py-3 text-wrap" id="notification-4">
										<div class="d-flex">
											<div class="me-2 position-relative flex-shrink-0">
												<img src={{ asset('assets/img/users/user-24.jpg') }} class="avatar-md rounded-circle" alt="">
											</div>
											<div class="flex-grow-1">
												<p class="mb-0 fw-medium text-dark">Ann McClure</p>
												<p class="mb-1 text-wrap">
                                                    cancelled her appointment scheduled for <span class="fw-medium text-dark">February 5, 2024</span>
												</p>
												<div class="d-flex justify-content-between align-items-center">
													<span class="fs-12"><i class="ti ti-clock me-1"></i>20 min ago</span>
													<div class="notification-action d-flex align-items-center float-end gap-2">
														<a href="javascript:void(0);" class="notification-read rounded-circle bg-danger" data-bs-toggle="tooltip" title="" data-bs-original-title="Make as Read" aria-label="Make as Read"></a>
														<button class="btn rounded-circle p-0" data-dismissible="#notification-4">
															<i class="ti ti-x"></i>
														</button>
													</div>
												</div>
											</div>
										</div>
									</div>
									 
								</div>
								
								<!-- View All-->
								<div class="p-2 rounded-bottom border-top text-center">
									<a href={{ asset('notifications.html') }} class="text-center text-decoration-underline fs-14 mb-0">
										View All Notifications
									</a>
								</div>
								
							</div>
						</div>
					</div>

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
</body>
</html>