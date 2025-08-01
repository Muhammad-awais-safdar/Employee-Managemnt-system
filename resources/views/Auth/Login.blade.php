@extends('Auth.Layout')

@section('AuthLayout')
    <div class="col-lg-4 mx-auto">
        <form id="loginForm" class="d-flex justify-content-center align-items-center" method="POST">
            @csrf
            <div class="d-flex flex-column justify-content-lg-center p-4 p-lg-0 pb-0 flex-fill">
                <div class="mx-auto mb-4 text-center">
                    <img src="{{ asset('assets/img/logo.svg') }}" class="img-fluid" width="100" alt="Employee Management System Logo">
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
                                <input type="text" name="email" class="form-control" placeholder="Enter Email Address">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group input-group-flat">
                                <span class="input-group-text">
                                    <i class="ti ti-lock-open fs-14 text-dark"></i>
                                </span>
                                <input type="password" name="password" class="pass-input form-control ps-1" placeholder="************">
                                <span class="toggle-password input-group-text ti ti-eye-off fs-14 text-dark"></span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="form-check form-check-md mb-0">
                                <input class="form-check-input" name="remember" id="remember" type="checkbox">
                                <label for="remember" class="form-check-label mt-0 text-dark">Remember Me</label>
                            </div>
                        </div>
                        <div class="mb-2">
                            <button type="submit" id="loginBtn" class="btn btn-primary w-100">Login</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#loginForm').submit(function (e) {
                e.preventDefault();
                const $btn = $('#loginBtn');
                const $form = $(this);
                
                $btn.prop('disabled', true).text('Logging in...');

                $.ajax({
                    url: $form.attr('action') || "{{ route('loginpost') }}",
                    method: "POST",
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).text('Login');
                        let response = xhr.responseJSON || {};
                        
                        if (xhr.status === 422 && response.errors) {
                            // Validation errors
                            $.each(response.errors, function (key, messages) {
                                if (Array.isArray(messages)) {
                                    messages.forEach(function(message) {
                                        showToast('error', message);
                                    });
                                } else {
                                    showToast('error', messages);
                                }
                            });
                        } else if (response.message) {
                            // Other error with message
                            showToast('error', response.message);
                        } else {
                            // Generic error
                            showToast('error', 'An unexpected error occurred. Please try again.');
                        }
                    }
                });
            });
            
            // Helper function to show toast messages
            function showToast(type, message) {
                if (typeof toastr === 'undefined') {
                    alert(type.toUpperCase() + ': ' + message);
                    return;
                }
                
                switch(type) {
                    case 'success':
                        toastr.success(message);
                        break;
                    case 'error':
                        toastr.error(message);
                        break;
                    case 'warning':
                        toastr.warning(message);
                        break;
                    default:
                        toastr.info(message);
                }
            }
        });
    </script>
@endpush
