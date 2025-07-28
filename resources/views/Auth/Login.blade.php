@extends('Auth.Layout')

@section('AuthLayout')
    <div class="col-lg-4 mx-auto">
        <form id="loginForm" class="d-flex justify-content-center align-items-center" method="POST">
            @csrf
            <div class="d-flex flex-column justify-content-lg-center p-4 p-lg-0 pb-0 flex-fill">
                <div class="mx-auto mb-4 text-center">
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <script src='https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>

    <script>
        $('#loginForm').submit(function (e) {
            e.preventDefault();

            const $btn = $('#loginBtn');
            $btn.prop('disabled', true).text('Logging in...');

            $.ajax({
                url: "{{ route('loginpost') }}",
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    email: $('input[name=email]').val(),
                    password: $('input[name=password]').val(),
                    remember: $('#remember').is(':checked') ? 'on' : ''
                },
                success: function (res) {
                    console.log(res);
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: res.message || 'Login successful',
                        confirmButtonColor: '#3085d6'
                    }).then(() => {
                        window.location.href = res.redirect;
                    });
                },
                error: function (xhr) {
                    $btn.prop('disabled', false).text('Login');

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            toastar.error(value[0]);
                        });
                    } else if (xhr.status === 403) {
                        toastar.error(xhr.responseJSON.message);
                    } else {
                        toastar.error('An unexpected error occurred.');
                    }
                }
            });
        });
    </script>
@endpush
