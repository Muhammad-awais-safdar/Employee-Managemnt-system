@extends('EmployeeManagemntsystem.Layout.App')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Users Management</h4>
                    @if($canCreate)
                        <a href="{{ route('superAdmin.users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create New User
                        </a>
                    @endif
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <ul class="nav nav-tabs" id="userTabs" role="tablist">
                        @foreach($usersByRole as $roleName => $users)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $loop->first ? 'active' : '' }}" 
                                        id="{{ Str::slug($roleName) }}-tab" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#{{ Str::slug($roleName) }}" 
                                        type="button" 
                                        role="tab" 
                                        aria-controls="{{ Str::slug($roleName) }}"
                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                    {{ ucfirst($roleName) }}
                                    <span class="badge bg-primary ms-1">{{ count($users) }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content p-3 border border-top-0 rounded-bottom" id="userTabsContent">
                        @foreach($usersByRole as $roleName => $users)
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                                 id="{{ Str::slug($roleName) }}" 
                                 role="tabpanel" 
                                 aria-labelledby="{{ Str::slug($roleName) }}-tab">
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th class="text-white">#</th>
                                                <th class="text-white">Name</th>
                                                <th class="text-white">Email</th>
                                                <th class="text-white">Company</th>
                                                <th class="text-white">Roles</th>
                                                <th class="text-white">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($users as $user)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>{{ $user->company->company_name ?? 'N/A' }}</td>
                                                    <td>
                                                        @foreach($user->roles as $role)
                                                            <span class="badge bg-primary">{{ $role->name }}</span>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('superAdmin.users.show', $user->id) }}" class="btn btn-info btn-sm" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($canEdit)
                                                        <a href="{{ route('superAdmin.users.edit', $user->id) }}" class="btn btn-primary btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @endif
                                                        @if($canDelete && $user->id !== auth()->id())
                                                        <form action="{{ route('superAdmin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                        @endif
                                                    </div>

                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">No users found with this role</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize Bootstrap tabs
    document.addEventListener('DOMContentLoaded', function() {
        // Activate the first tab
        var firstTab = document.querySelector('#userTabs .nav-link');
        if (firstTab) {
            new bootstrap.Tab(firstTab).show();
        }
        
        // Enable tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush

<style>
    .nav-tabs .nav-link {
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        font-weight: 600;
    }
    .table th {
        white-space: nowrap;
    }
    .btn-group .btn {
        margin-right: 2px;
    }
</style>
@endsection
