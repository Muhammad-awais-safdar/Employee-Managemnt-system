@extends('EmployeeManagemntsystem.Layout.App')

@section('title', 'Team Leave Calendar')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Team Leave Calendar</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('HR.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Leave Calendar</li>
                    </ul>
                </div>
            </div>
        </div>

        @include('EmployeeManagemntsystem.Admin.leave.calendar')
    </div>
</div>
@endsection