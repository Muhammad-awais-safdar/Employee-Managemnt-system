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
    <title>Login | Dleohr</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Dreams Technologies">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

    <!-- Apple Icon -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <!-- Custom Toastr CSS -->
    <style>
        /* Fix toastr visibility */
        #toast-container > div {
            opacity: 1;
            -ms-filter: none;
            filter: none;
        }
        .toast {
            background-color: #2a3042;
            color: #fff;
        }
        .toast-success {
            background-color: #51a351 !important;
        }
        .toast-error {
            background-color: #bd362f !important;
        }
        .toast-info {
            background-color: #2f96b4 !important;
        }
        .toast-warning {
            background-color: #f89406 !important;
        }
    </style>
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">


</head>

<body>

    <!-- Begin Wrapper -->
    <div class="main-wrapper auth-bg position-relative overflow-hidden">

        <!-- Start Content -->
        <div class="container-fuild position-relative z-1">
            <div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">

                <!-- start row -->
                <div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap py-3">
                    @yield('AuthLayout')
                </div>
                <!-- end row -->

            </div>
        </div>
        <!-- End Content -->
    </div>
    <!-- End Wrapper -->

    <!-- jQuery -->
    <script src='{{ asset('assets/js/jquery-3.7.1.min.js') }}'></script>
    <!-- Bootstrap Core JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Main JS -->
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js'></script>
    <script>
        // Ensure toastr is properly initialized
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                closeButton: true,
                progressBar: true,
                timeOut: 5000,
                positionClass: 'toast-top-right',
                showMethod: 'fadeIn',
                hideMethod: 'fadeOut',
                closeMethod: 'fadeOut',
                preventDuplicates: true
            };
            
            // Test toast - uncomment to verify toastr is working
            // toastr.success('Toast is working!');
        } else {
            console.error('Toastr not loaded properly');
        }
    </script>
    @stack('scripts')

</body>

</html>
