
<!DOCTYPE html>
<html lang="en">
<head>

	<!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Dleohr is a clean and modern human resource management admin dashboard template which is based on HTML 5, Bootstrap 5. Try Demo and Buy Now!">
	<meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
    <title>Login | Dleohr</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Dreams Technologies">
	
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ route('assets/img/favicon.png') }}">

    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href="{{ route('assets/img/apple-icon.png') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ route('assets/css/bootstrap.min.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ route('assets/plugins/tabler-icons/tabler-icons.min.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ route('assets/css/style.css') }}" id="app-style">

</head>

<body>

    <!-- Begin Wrapper -->
    <div class="main-wrapper auth-bg position-relative overflow-hidden">

        <!-- Start Content -->
		<div class="container-fuild position-relative z-1">
			<div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">

				<!-- start row -->
				<div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap py-3">

					<div class="col-lg-4 mx-auto">
						<form action="https://dleohr.dreamstechnologies.com/html/template/index.html" class="d-flex justify-content-center align-items-center">
							<div class="d-flex flex-column justify-content-lg-center p-4 p-lg-0 pb-0 flex-fill">
								<div class=" mx-auto mb-4 text-center">
									<img src="{{ asset('assets/img/logo.svg') }}" class="img-fluid" width="100" alt="Logo">
								</div>
								<div class="card border-0 p-lg-3 rounded-4">
									<div class="card-body">
										<div class="text-center mb-3">
											<h5 class="mb-1 fw-bold">Sign In</h5>
											<p class="mb-0">Please enter below details to access the dashboard</p>
										</div>
										<div class="mb-3">
											<label class="form-label">Email Address</label>
											<div class="input-group input-group-flat">
												<span class="input-group-text">
													<i class="ti ti-mail fs-14 text-dark"></i>
												</span>
												<input type="text" class="form-control" placeholder="Enter Email Address">
											</div>
										</div>
										<div class="mb-3">
											<label class="form-label">Password</label>
											<div class="input-group input-group-flat">
												<span class="input-group-text">
													<i class="ti ti-lock-open fs-14 text-dark"></i>
												</span>
												<input type="password" class="pass-input form-control ps-1" placeholder="************">
												<span class="toggle-password input-group-text ti ti-eye-off fs-14 text-dark"></span>
											</div>
										</div>
										<div class="d-flex align-items-center justify-content-between mb-3">
											<div class="d-flex align-items-center">
												<div class="form-check form-check-md mb-0">
													<input class="form-check-input" id="remember_me" type="checkbox">
													<label for="remember_me" class="form-check-label mt-0 text-dark">Remember Me</label>
												</div>
											</div>
											<div class="text-end">
												<a href="{{ route('forgot-password.html') }}">Forgot Password?</a>
											</div>
										</div>
										<div class="mb-2">
											<button type="submit" class="btn btn-primary w-100">Login</button>
										</div>
										<div class="login-or position-relative mb-3">
											<span class="span-or fs-12">OR</span>
										</div>
										<div class="mb-3">
											<div class="d-flex align-items-center justify-content-center flex-wrap">
												<div class="text-center me-2 flex-fill">
													<a href="#"
														class="br-10 p-1 btn btn-outline-light border d-flex align-items-center justify-content-center">
														<img class="img-fluid m-1" src="{{ asset('assets/img/icons/facebook-logo.svg') }}" alt="Facebook">
													</a>
												</div>
												<div class="text-center me-2 flex-fill">
													<a href="#"
														class="br-10 p-1 btn btn-outline-light border d-flex align-items-center justify-content-center">
														<img class="img-fluid m-1" src="{{ asset('assets/img/icons/google-logo.svg') }}" alt="Google">
													</a>
												</div>
											</div>
										</div>
										<div class="text-center">
											<h6 class="fw-normal fs-14 text-dark mb-0">Don’t have an account yet?
												<a href="{{ route('register.html') }}" class="hover-a"> Register</a>
											</h6>
										</div>
									</div><!-- end card body -->
								</div><!-- end card -->
							</div>
						</form>
					</div><!-- end col -->

				</div>
				<!-- end row -->
				 
			</div>
		</div>
		<!-- End Content -->
    </div>
    <!-- End Wrapper -->

	<!-- jQuery -->
<script src='assets/js/jquery-3.7.1.min.js' type="6e07030c3bc7860727e345cf-text/javascript"></script>
	<!-- Bootstrap Core JS -->
<script src='assets/js/bootstrap.bundle.min.js' type="6e07030c3bc7860727e345cf-text/javascript"></script>
	<!-- Main JS -->
<script src='assets/js/script.js' type="6e07030c3bc7860727e345cf-text/javascript"></script>

</body>
</html>