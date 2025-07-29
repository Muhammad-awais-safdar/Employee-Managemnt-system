  <div class="sidebar" id="sidebar">

      <!-- Start Logo -->
      <div class="sidebar-logo">
          <div>
              <!-- Logo Normal -->
              <a href={{ asset('index.html') }} class="logo logo-normal">
                  <img src={{ asset('assets/img/logo.svg') }} alt="Logo">
              </a>

              <!-- Logo Small -->
              <a href={{ asset('index.html') }} class="logo-small">
                  <img src={{ asset('assets/img/logo-small.svg') }} alt="Logo">
              </a>

              <!-- Logo Dark -->
              <a href={{ asset('index.html') }} class="dark-logo">
                  <img src={{ asset('assets/img/logo-white.svg') }} alt="Logo">
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
                                  <li><a href={{ asset('index.html') }} class="active">Dashboard 1</a></li>
                                  <li><a href={{ asset('index-2.html') }}>Dashboard 2</a></li>
                                  <li><a href={{ asset('index-3.html') }}>Dashboard 3</a></li>
                                  <li><a href={{ asset('index-4.html') }}>Dashboard 4</a></li>
                              </ul>
                          </li>
                          <li class="submenu">
                              <a href="javascript:void(0);">
                                  <i class="ti ti-apps"></i><span>Applications</span>
                                  <span class="menu-arrow"></span>
                              </a>
                              <ul>
                                  <li><a href={{ asset('chat.html') }}>Chat</a></li>
                                  <li class="submenu submenu-two">
                                      <a href="#">Calls<span class="menu-arrow inside-submenu"></span></a>
                                      <ul>
                                          <li><a href={{ asset('voice-call.html') }}>Voice Call</a></li>
                                          <li><a href={{ asset('video-call.html') }}>Video Call</a></li>
                                          <li><a href={{ asset('outgoing-call.html') }}>Outgoing Call</a></li>
                                          <li><a href={{ asset('incoming-call.html') }}>Incoming Call</a></li>
                                          <li><a href={{ asset('call-history.html') }}>Call History</a></li>
                                      </ul>
                                  </li>
                                  <li><a href={{ asset('calendar.html') }}>Calendar</a></li>
                                  <li><a href={{ asset('contacts.html') }}>Contacts</a></li>
                                  <li><a href={{ asset('email.html') }}>Email</a></li>
                                  <li class="submenu submenu-two">
                                      <a href="#">Invoices<span class="menu-arrow inside-submenu"></span></a>
                                      <ul>
                                          <li><a href={{ asset('invoice.html') }}>Invoices</a></li>
                                          <li><a href={{ asset('invoice-details.html') }}>Invoice Details</a></li>
                                      </ul>
                                  </li>
                                  <li><a href={{ asset('todo.html') }}>To Do</a></li>
                                  <li><a href={{ asset('notes.html') }}>Notes</a></li>
                                  <li><a href={{ asset('kanban-view.html') }}>Kanban Board</a></li>
                                  <li><a href={{ asset('file-manager.html') }}>File Manager</a></li>
                                  <li><a href={{ asset('social-feed.html') }}>Social Feed</a></li>
                                  <li><a href={{ asset('search-list.html') }}>Search Result</a></li>
                              </ul>
                          </li>
                      </ul>
                  </li>
                  <li class="menu-title"><span>Peoples & Teams</span></li>
                  <li>
                      <ul>
                          @if (Auth::user()->hasRole('superAdmin'))
                              <li>
                                  <a href={{ route('superAdmin.company.index') }}>
                                      <i class="ti ti-building-community"></i><span>Companies</span>
                                  </a>
                              </li>
                          @endif
                          <li>
                              <a href={{ asset('employees.html') }}>
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
                              <a href={{ asset('leaves.html') }}>
                                  <i class="ti ti-calendar-star"></i><span>Leaves</span>
                              </a>
                          </li>
                          <li>
                              <a href={{ asset('reviews.html') }}>
                                  <i class="ti ti-user-bolt"></i><span>Reviews</span>
                              </a>
                          </li>
                      </ul>
                  </li>
                  <li class="menu-title"><span>Utilities & Reports</span></li>
                  <li>
                      <ul>
                          <li>
                              <a href={{ asset('report-calendar.html') }}>
                                  <i class="ti ti-calendar-event"></i><span>Calendar</span>
                              </a>
                          </li>
                          <li>
                              <a href={{ asset('team-report.html') }}>
                                  <i class="ti ti-report"></i><span>Reports</span>
                              </a>
                          </li>
                          <li>
                              <a href={{ asset('manage.html') }}>
                                  <i class="ti ti-settings-2"></i><span>Manage</span>
                              </a>
                          </li>
                      </ul>
                  </li>
                  <li class="menu-title"><span>Settings</span></li>
                  <li>
                      <ul>
                          <li>
                              <a href={{ asset('settings.html') }}>
                                  <i class="ti ti-settings"></i><span>Settings</span>
                              </a>
                          </li>
                      </ul>
                  </li>
                  <li class="menu-title"><span>Authentication</span></li>
                  <li>
                      <ul>
                          <li>
                              <a href={{ asset('login.html') }}>
                                  <i class="ti ti-login"></i><span>Login</span>
                              </a>
                          </li>
                          <li>
                              <a href={{ asset('register.html') }}>
                                  <i class="ti ti-report"></i><span>Register</span>
                              </a>
                          </li>
                          <li>
                              <a href={{ asset('forgot-password.html') }}>
                                  <i class="ti ti-lock-exclamation"></i><span>Forgot Password</span>
                              </a>
                          </li>
                          <li>
                              <a href={{ asset('reset-password.html') }}>
                                  <i class="ti ti-restore"></i><span>Reset Password</span>
                              </a>
                          </li>
                          <li>
                              <a href={{ asset('email-verification.html') }}>
                                  <i class="ti ti-mail-check"></i><span>Email Verification</span>
                              </a>
                          </li>
                          <li>
                              <a href={{ asset('two-step-verification.html') }}>
                                  <i class="ti ti-discount-check"></i><span>2 Step Verification</span>
                              </a>
                          </li>
                          <li>
                              <a href={{ asset('lock-screen.html') }}>
                                  <i class="ti ti-lock-square-rounded"></i><span>Lock Screen</span>
                              </a>
                          </li>
                          <li class="submenu">
                              <a href="javascript:void(0);">
                                  <i class="ti ti-exclamation-mark-off"></i><span>Error Pages</span><span
                                      class="menu-arrow"></span>
                              </a>
                              <ul>
                                  <li><a href={{ asset('error-404.html') }}>404 Error</a></li>
                                  <li><a href={{ asset('error-500.html') }}>500 Error</a></li>
                              </ul>
                          </li>
                      </ul>
                  </li>
                  <li class="menu-title"><span>UI Interface</span></li>
                  <li>
                      <ul>
                          <li class="submenu">
                              <a href="javascript:void(0);">
                                  <i class="ti ti-chart-pie"></i><span>Base UI</span><span class="menu-arrow"></span>
                              </a>
                              <ul>
                                  <li><a href={{ asset('ui-accordion.html') }}>Accordion</a></li>
                                  <li><a href={{ asset('ui-alerts.html') }}>Alerts</a></li>
                                  <li><a href={{ asset('ui-avatar.html') }}>Avatar</a></li>
                                  <li><a href={{ asset('ui-badges.html') }}>Badges</a></li>
                                  <li><a href={{ asset('ui-breadcrumb.html') }}>Breadcrumb</a></li>
                                  <li><a href={{ asset('ui-buttons.html') }}>Buttons</a></li>
                                  <li><a href={{ asset('ui-buttons-group.html') }}>Button Group</a></li>
                                  <li><a href={{ asset('ui-cards.html') }}>Card</a></li>
                                  <li><a href={{ asset('ui-carousel.html') }}>Carousel</a></li>
                                  <li><a href={{ asset('ui-collapse.html') }}>Collapse</a></li>
                                  <li><a href={{ asset('ui-dropdowns.html') }}>Dropdowns</a></li>
                                  <li><a href={{ asset('ui-ratio.html') }}>Ratio</a></li>
                                  <li><a href={{ asset('ui-grid.html') }}>Grid</a></li>
                                  <li><a href={{ asset('ui-images.html') }}>Images</a></li>
                                  <li><a href={{ asset('ui-links.html') }}>Links</a></li>
                                  <li><a href={{ asset('ui-list-group.html') }}>List Group</a></li>
                                  <li><a href={{ asset('ui-modals.html') }}>Modals</a></li>
                                  <li><a href={{ asset('ui-offcanvas.html') }}>Offcanvas</a></li>
                                  <li><a href={{ asset('ui-pagination.html') }}>Pagination</a></li>
                                  <li><a href={{ asset('ui-placeholders.html') }}>Placeholders</a></li>
                                  <li><a href={{ asset('ui-popovers.html') }}>Popovers</a></li>
                                  <li><a href={{ asset('ui-progress.html') }}>Progress</a></li>
                                  <li><a href={{ asset('ui-scrollspy.html') }}>Scrollspy</a></li>
                                  <li><a href={{ asset('ui-spinner.html') }}>Spinner</a></li>
                                  <li><a href={{ asset('ui-nav-tabs.html') }}>Tabs</a></li>
                                  <li><a href={{ asset('ui-toasts.html') }}>Toasts</a></li>
                                  <li><a href={{ asset('ui-tooltips.html') }}>Tooltips</a></li>
                                  <li><a href={{ asset('ui-typography.html') }}>Typography</a></li>
                                  <li><a href={{ asset('ui-utilities.html') }}>Utilities</a></li>
                              </ul>
                          </li>
                          <li class="submenu">
                              <a href="javascript:void(0);">
                                  <i class="ti ti-radar"></i><span>Advanced UI</span><span class="menu-arrow"></span>
                              </a>
                              <ul>
                                  <li><a href={{ asset('extended-dragula.html') }}>Dragula</a></li>
                                  <li><a href={{ asset('ui-clipboard.html') }}>Clipboard</a></li>
                                  <li><a href={{ asset('ui-rangeslider.html') }}>Range Slider</a></li>
                                  <li><a href={{ asset('ui-sweetalerts.html') }}>Sweet Alerts</a></li>
                                  <li><a href={{ asset('ui-lightbox.html') }}>Lightbox</a></li>
                                  <li><a href={{ asset('ui-rating.html') }}>Rating</a></li>
                                  <li><a href={{ asset('ui-scrollbar.html') }}>Scrollbar</a></li>
                              </ul>
                          </li>
                          <li class="submenu">
                              <a href="javascript:void(0);">
                                  <i class="ti ti-forms"></i><span>Forms</span><span class="menu-arrow"></span>
                              </a>
                              <ul>
                                  <li class="submenu submenu-two">
                                      <a href="javascript:void(0);">Form Elements<span
                                              class="menu-arrow inside-submenu"></span></a>
                                      <ul>
                                          <li><a href={{ asset('form-basic-inputs.html') }}>Basic Inputs</a></li>
                                          <li><a href={{ asset('form-checkbox-radios.html') }}>Checkbox & Radios</a>
                                          </li>
                                          <li><a href={{ asset('form-input-groups.html') }}>Input Groups</a></li>
                                          <li><a href={{ asset('form-grid-gutters.html') }}>Grid & Gutters</a></li>
                                          <li><a href={{ asset('form-mask.html') }}>Input Masks</a></li>
                                          <li><a href={{ asset('form-fileupload.html') }}>File Uploads</a></li>
                                      </ul>
                                  </li>
                                  <li class="submenu submenu-two">
                                      <a href="javascript:void(0);">Layouts<span
                                              class="menu-arrow inside-submenu"></span></a>
                                      <ul>
                                          <li><a href={{ asset('form-horizontal.html') }}>Horizontal Form</a></li>
                                          <li><a href={{ asset('form-vertical.html') }}>Vertical Form</a></li>
                                          <li><a href={{ asset('form-floating-labels.html') }}>Floating Labels</a></li>
                                      </ul>
                                  </li>
                                  <li><a href={{ asset('form-validation.html') }}>Form Validation</a></li>
                                  <li><a href={{ asset('form-select2.html') }}>Select2</a></li>
                                  <li><a href={{ asset('form-wizard.html') }}>Form Wizard</a></li>
                                  <li><a href={{ asset('form-pickers.html') }}>Form Picker</a></li>
                              </ul>
                          </li>
                          <li class="submenu">
                              <a href="javascript:void(0);">
                                  <i class="ti ti-table-row"></i><span>Tables</span><span class="menu-arrow"></span>
                              </a>
                              <ul>
                                  <li><a href={{ asset('tables-basic.html') }}>Basic Tables </a></li>
                                  <li><a href={{ asset('data-tables.html') }}>Data Table </a></li>
                              </ul>
                          </li>
                          <li class="submenu">
                              <a href="javascript:void(0);">
                                  <i class="ti ti-chart-donut"></i>
                                  <span>Charts</span><span class="menu-arrow"></span>
                              </a>
                              <ul>
                                  <li><a href={{ asset('chart-apex.html') }}>Apex Charts</a></li>
                                  <li><a href={{ asset('chart-c3.html') }}>Chart C3</a></li>
                                  <li><a href={{ asset('chart-js.html') }}>Chart Js</a></li>
                                  <li><a href={{ asset('chart-morris.html') }}>Morris Charts</a></li>
                                  <li><a href={{ asset('chart-flot.html') }}>Flot Charts</a></li>
                                  <li><a href={{ asset('chart-peity.html') }}>Peity Charts</a></li>
                              </ul>
                          </li>
                          <li class="submenu">
                              <a href="javascript:void(0);">
                                  <i class="ti ti-icons"></i>
                                  <span>Icons</span><span class="menu-arrow"></span>
                              </a>
                              <ul>
                                  <li><a href={{ asset('icon-fontawesome.html') }}>Fontawesome Icons</a></li>
                                  <li><a href={{ asset('icon-tabler.html') }}>Tabler Icons</a></li>
                                  <li><a href={{ asset('icon-bootstrap.html') }}>Bootstrap Icons</a></li>
                                  <li><a href={{ asset('icon-remix.html') }}>Remix Icons</a></li>
                                  <li><a href={{ asset('icon-feather.html') }}>Feather Icons</a></li>
                                  <li><a href={{ asset('icon-ionic.html') }}>Ionic Icons</a></li>
                                  <li><a href={{ asset('icon-material.html') }}>Material Icons</a></li>
                                  <li><a href={{ asset('icon-pe7.html') }}>Pe7 Icons</a></li>
                                  <li><a href={{ asset('icon-simpleline.html') }}>Simpleline Icons</a></li>
                                  <li><a href={{ asset('icon-themify.html') }}>Themify Icons</a></li>
                                  <li><a href={{ asset('icon-weather.html') }}>Weather Icons</a></li>
                                  <li><a href={{ asset('icon-typicons.html') }}>Typicons Icons</a></li>
                                  <li><a href={{ asset('icon-flag.html') }}>Flag Icons</a></li>
                              </ul>
                          </li>
                      </ul>
                  </li>
                  <li class="menu-title"><span>Help</span></li>
                  <li>
                      <ul>
                          <li>
                              <a href="javascript:void(0);"><i
                                      class="ti ti-file-dots"></i><span>Documentation</span></a>
                          </li>
                          <li>
                              <a href="javascript:void(0);"><i
                                      class="ti ti-status-change"></i><span>Changelog</span><span
                                      class="badge bg-danger ms-2 badge-md rounded-2 fs-12 fw-medium">v2.0</span></a>
                          </li>
                          <li class="submenu">
                              <a href="javascript:void(0);">
                                  <i class="ti ti-versions"></i><span>Multi Level</span>
                                  <span class="menu-arrow"></span>
                              </a>
                              <ul>
                                  <li><a href="javascript:void(0);">Multilevel 1</a></li>
                                  <li class="submenu submenu-two">
                                      <a href="javascript:void(0);">Multilevel 2<span
                                              class="menu-arrow inside-submenu"></span></a>
                                      <ul>
                                          <li><a href="javascript:void(0);">Multilevel 2.1</a></li>
                                          <li class="submenu submenu-two submenu-three">
                                              <a href="javascript:void(0);">Multilevel 2.2<span
                                                      class="menu-arrow inside-submenu inside-submenu-two"></span></a>
                                              <ul>
                                                  <li><a href="javascript:void(0);">Multilevel 2.2.1</a></li>
                                                  <li><a href="javascript:void(0);">Multilevel 2.2.2</a></li>
                                              </ul>
                                          </li>
                                      </ul>
                                  </li>
                                  <li><a href="javascript:void(0);">Multilevel 3</a></li>
                              </ul>
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
              <a href={{ asset('login.html') }} class="btn btn-danger w-100"><i
                      class="ti ti-logout-2 me-1"></i>Logout</a>
          </div>
      </div>

  </div>
