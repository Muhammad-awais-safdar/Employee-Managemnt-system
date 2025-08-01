@extends('EmployeeManagemntsystem.Layout.App')

@section('title', 'Create Department')

@push('styles')
<style>
.text-purple {
    color: #3E007C !important;
}

.breadcrumb-item + .breadcrumb-item::before {
    color: #6c757d;
}

.breadcrumb-item a {
    text-decoration: none;
}

.breadcrumb-item a:hover {
    color: #7100E2 !important;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold text-purple mb-1">Create Department</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('Admin.dashboard') }}" class="text-purple">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('Admin.departments.index') }}" class="text-purple">Departments</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Create</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <form action="{{ route('Admin.departments.store') }}" method="POST">
        @csrf
        
        @include('EmployeeManagemntsystem.Admin.Department._form')
    </form>
</div>

@if($errors->any())
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'error',
        title: 'Validation Error!',
        html: `
            <ul style="text-align: left; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        `,
        confirmButtonColor: '#3E007C'
    });
});
</script>
@endif
@endsection