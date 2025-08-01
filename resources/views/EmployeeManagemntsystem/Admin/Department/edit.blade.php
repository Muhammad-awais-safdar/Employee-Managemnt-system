@extends('EmployeeManagemntsystem.Layout.App')

@section('title', 'Edit Department')

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
                    <h4 class="fw-bold text-purple mb-1">Edit Department</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('Admin.dashboard') }}" class="text-purple">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('Admin.departments.index') }}" class="text-purple">Departments</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('Admin.departments.show', $department) }}" class="text-purple">{{ $department->name }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('Admin.departments.show', $department) }}" class="btn btn-outline-info">
                        <i class="ti ti-eye me-2"></i>View Department
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <form action="{{ route('Admin.departments.update', $department) }}" method="POST">
        @csrf
        @method('PUT')
        
        @include('EmployeeManagemntsystem.Admin.Department._form', ['department' => $department])
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