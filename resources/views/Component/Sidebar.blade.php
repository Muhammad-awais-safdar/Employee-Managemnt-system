  <div class="sidebar" id="sidebar">

      <!-- Start Logo -->
      <div class="sidebar-logo">
          <div>
              @php
                  $user = Auth::user();
                  $companyLogo = null;
                  $companyName = 'Employee Management System';
                  $dashboardRoute = '#';
                  
                  if ($user && $user->company && $user->company->logo) {
                      $companyLogo = asset('storage/' . $user->company->logo);
                      $companyName = $user->company->name;
                  }
                  
                  // Set dashboard route based on user role
                  if ($user) {
                      $role = $user->getRoleNames()->first();
                      if ($role && Route::has($role . '.dashboard')) {
                          $dashboardRoute = route($role . '.dashboard');
                      }
                  }
                  
                  // Fallback logos
                  $defaultLogo = asset('assets/img/logo.svg');
                  $defaultSmallLogo = asset('assets/img/logo-small.svg');
                  $defaultDarkLogo = asset('assets/img/logo-white.svg');
              @endphp

              <!-- Logo Normal -->
              <a href="{{ $dashboardRoute }}" class="logo logo-normal">
                  <img src="{{ $companyLogo ?: $defaultLogo }}" alt="{{ $companyName }} Logo" style="max-height: 45px; width: auto;">
              </a>

              <!-- Logo Small -->
              <a href="{{ $dashboardRoute }}" class="logo-small">
                  <img src="{{ $companyLogo ?: $defaultSmallLogo }}" alt="{{ $companyName }} Logo" style="max-height: 35px; width: auto;">
              </a>

              <!-- Logo Dark -->
              <a href="{{ $dashboardRoute }}" class="dark-logo">
                  <img src="{{ $companyLogo ?: $defaultDarkLogo }}" alt="{{ $companyName }} Logo" style="max-height: 45px; width: auto;">
              </a>
          </div>
          <button class="sidenav-toggle-btn btn p-0" id="toggle_btn">
              <i class="ti ti-chevron-left-pipe"></i>
          </button>

          <!-- Sidebar Menu Close -->
          <button class="sidebar-close">
              <i class="ti ti-x align-middle"></i>
          </button>
      </div>
      <!-- End Logo -->

      <!-- Sidenav Menu -->
      <div class="sidebar-inner" data-simplebar>
          <div id="sidebar-menu" class="sidebar-menu">
              <ul>
                  <li class="menu-title"><span>Main Menu</span></li>
                  <li>
                      <ul>
                          <li class="submenu">
                              <a href="javascript:void(0);" class="active subdrop">
                                  <i class="ti ti-layout-dashboard"></i><span>Dashboard</span>
                                  <span class="menu-arrow"></span>
                              </a>
                              <ul>
                                  @php
                                      $role = auth()->check() ? Auth::user()->getRoleNames()->first() : null;
                                      $dashboardRoute = $role && Route::has($role . '.dashboard') ? route($role . '.dashboard') : route('login');
                                  @endphp
                                  <li><a href="{{ $dashboardRoute }}" class="active">Dashboard</a></li>
                              </ul>
                          </li>
                      </ul>
                  </li>
                  <li class="menu-title"><span>Peoples & Teams</span></li>
                  <li>@
                      <ul>
                          @if (Auth::user()->hasRole('superAdmin'))
                              <li>
                                  <a href={{ route('superAdmin.company.index') }}>
                                      <i class="ti ti-building-community"></i><span>Companies</span>
                                  </a>
                              </li>
                          @endif
                          @if (Auth::user()->hasRole('superAdmin') || Auth::user()->hasRole('Admin'))
                              <li>
                                  <a href="{{ route('Admin.departments.index') }}">
                                      <i class="ti ti-layout-grid"></i><span>Departments</span>
                                  </a>
                              </li>
                              <li>
                              <a href={{ route('Admin.working-hours.index') }}>
                                  <i class="ti ti-settings"></i><span>Settings</span>
                              </a>
                          </li>
                          @endif
                          @if (Auth::user()->hasRole('HR'))
                              <li>
                                  <a href="{{ route('HR.departments.assignments') }}">
                                      <i class="ti ti-users-group"></i><span>Department Assignments</span>
                                  </a>
                              </li>
                          @endif
                          <li>
                              <a href="#">
                                  <i class="ti ti-users-group"></i><span>Employee</span>
                              </a>
                          </li>
                          <li>
                              @php
                                  $role = auth()->check() ? Auth::user()->getRoleNames()->first() : null;
                                  //   dd($role);
                                  $dashboardRoute =
                                      $role && Route::has($role . '.users.index')
                                          ? route($role . '.users.index')
                                          : route('login');
                              @endphp
                              <a href="{{ $dashboardRoute }}">
                                  <i class="ti ti-users-group"></i><span>Users</span>
                              </a>
                          </li>
                          <li>
                              @php
                                  $role = auth()->check() ? Auth::user()->getRoleNames()->first() : null;
                                  $attendanceRoute = $role && Route::has($role . '.attendance.index') 
                                      ? route($role . '.attendance.index') 
                                      : '#';
                              @endphp
                              <a href="{{ $attendanceRoute }}">
                                  <i class="ti ti-clock"></i><span>Attendance</span>
                              </a>
                          </li>
                          <li>
                              @php
                                  $role = auth()->check() ? Auth::user()->getRoleNames()->first() : null;
                                  $leaveRoute = $role && Route::has($role . '.leave.index') 
                                      ? route($role . '.leave.index') 
                                      : '#';
                              @endphp
                              <a href="{{ $leaveRoute }}">
                                  <i class="ti ti-calendar-star"></i><span>Leaves</span>
                              </a>
                          </li>
                        
                      </ul>
                  </li>
               
                  <li class="menu-title"><span>Settings</span></li>
                  <li>
                      <ul>
                          <li>
                              @php
                                  $role = auth()->check() ? Auth::user()->getRoleNames()->first() : null;
                                  $profileRoute = $role && Route::has($role . '.profile.index') 
                                      ? route($role . '.profile.index') 
                                      : route('profile.index');
                              @endphp
                              <a href="{{ $profileRoute }}">
                                  <i class="ti ti-user-circle"></i><span>My Profile</span>
                              </a>
                          </li>
                       
                      </ul>
                  </li>
              
              </ul>
          </div>
          <div class="sidebar-footer">
              <div class="bg-light p-2 rounded d-flex align-items-center">
                  <a href="#" class="avatar avatar-md me-2"><img
                          src={{ asset('assets/img/users/avatar-2.jpg') }} alt=""></a>
                  <div>
                      <h6 class="fs-14 fw-semibold mb-1"><a href="#">Joseph Smith</a></h6>
                      <p class="fs-13 mb-0"><a href="https://dleohr.dreamstechnologies.com/cdn-cgi/l/email-protection"
                              class="__cf_email__"
                              data-cfemail="a2c3c6cfcbcce2c7dac3cfd2cec78cc1cdcf">[email&#160;protected]</a></p>
                  </div>
              </div>
          </div>
          <div class="p-3 pt-0">
              <a href={{ route('logout') }} class="btn btn-danger w-100"><i
                      class="ti ti-logout-2 me-1"></i>Logout</a>
          </div>
      </div>

  </div>

  </div>
