<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Dentist Clinic</title>
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css" />
    <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css" />
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css" />
    <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" href="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="apple-touch-icon" sizes="180x180" href="images/D.jpg">
    <link rel="icon" type="image/jpg" sizes="32x32" href="images/D.jpg">
    <link rel="icon" type="image/jpg" sizes="16x16" href="images/D.jpg">
  </head>
  <body>
    <div class="container-scroller">
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <div class="sidebar-brand-wrapper d-flex justify-content-center align-items-center">
            <a href="/" class=" sidebar-brand brand-logo pl-0 pt-3 gap-2">
                <img src="images/D.jpg" alt="Logo" style="width:40px; height:40px;">
                <span style="color: black;">entist Clinic</span>
            </a>
            <a class="sidebar-brand brand-logo-mini pl-2 pt-3" href="/"><img src="images/D.jpg" alt="logo" /></a>
        </div>
        <ul class="nav">
          <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
              <div class="nav-profile-image">
                @if(Auth::user()->profile_photo_path )
                <img src="{{ asset('images/' . Auth::user()->profile_photo_path) }}" alt="profile" />
                @else
                <img src="{{ asset('images/Unknown profile.webp' ) }}" alt="profile" />
                @endif
                <span class="login-status online"></span>
                <!--change to offline or busy as needed-->
              </div>
              <div class="nav-profile-text d-flex flex-column pr-3">
                @auth
                    <span class="font-weight-medium mb-0">
                      {{ Auth::user()->name }}
                    </span>
                    
                    @endauth
               
              </div>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/">
              <i class="mdi mdi-home menu-icon"></i>
              <span class="menu-title">Home</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
              <span class="menu-title">Doctors</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-basic">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('listDoctors') }}">List of doctors</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('showDoctors') }}">Add Doctors</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{route('reservation')}}">
            <i class="mdi mdi-table-large menu-icon"></i>
              <span class="menu-title">Reservations</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{route('AllUsers')}}">
                <i class="mdi mdi-contacts menu-icon"></i> 
              <span class="menu-title">Show Users</span>
            </a>
          </li>
          </li>
          <li class="nav-item sidebar-actions">
            <div class="nav-link">
              <div class="mt-4">
                <ul class="mt-4 pl-0">
                    <li>
                        @auth
                        <form action="{{ route('logout') }}" method="POST" >
                             @csrf
                             <button type="submit" style="background:none; border:none; color:inherit; cursor:pointer; font:inherit;  pr-5; line-height:normal; vertical-align:middle;">
                                 Logout
                             </button>
                         </form>
                        @endauth
                        </li>
                </ul>
              </div>
            </div>
          </li>
        </ul>
      </nav>
      <div class="container-fluid page-body-wrapper">
        <div id="theme-settings" class="settings-panel">
          <i class="settings-close mdi mdi-close"></i>
          <p class="settings-heading">SIDEBAR SKINS</p>
          <div class="sidebar-bg-options selected" id="sidebar-default-theme">
            <div class="img-ss rounded-circle bg-light border mr-3"></div> Default
          </div>
          <div class="sidebar-bg-options" id="sidebar-dark-theme">
            <div class="img-ss rounded-circle bg-dark border mr-3"></div> Dark
          </div>
          <p class="settings-heading mt-2">HEADER SKINS</p>
          <div class="color-tiles mx-0 px-4">
            <div class="tiles light"></div>
            <div class="tiles dark"></div>
          </div>
        </div>
        <nav class="navbar col-lg-12 col-12 p-lg-0 fixed-top d-flex flex-row">
          <div class="navbar-menu-wrapper d-flex align-items-stretch justify-content-between">
            <a class="navbar-brand brand-logo-mini align-self-center d-lg-none" href="index.html"><img src="assets/images/logo-mini.svg" alt="logo" /></a>
            <button class="navbar-toggler navbar-toggler align-self-center mr-2" type="button" data-toggle="minimize">
              <i class="mdi mdi-menu"></i>
            </button>
            <ul class="navbar-nav">
              <li class="nav-item dropdown">
                <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
                  <i class="mdi mdi-bell-outline"></i>
                  <span class="count count-varient1">{{ auth()->user()->unreadNotifications->count() }}</span>
                </a>
                <div class="dropdown-menu navbar-dropdown navbar-dropdown-large preview-list" aria-labelledby="notificationDropdown">
                  <h6 class="p-3 mb-0">Notifications</h6>
                  @forelse(auth()->user()->unreadNotifications as $notification)
                  <a class="dropdown-item preview-item" href="{{route('reservation')}}">
                    <div class="preview-item-content">
                      <p class="mb-0"><span class="text-small text-muted"> {{ $notification->data['message'] }}</span>
                      </p>
                      <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                  </a>
                      @empty
                      <div class="dropdown-item text-muted text-center">
                        No new notifications
                    </div> 
                  @endforelse
                  <div class="dropdown-divider"></div>
                  <p class="p-3 mb-0">View all activities</p>
                </div>
              </li>
              <li class="nav-item dropdown d-none d-sm-flex">
                <a class="nav-link count-indicator dropdown-toggle" id="messageDropdown" href="#" data-toggle="dropdown">
                  <i class="mdi mdi-email-outline"></i>
                  <span class="count count-varient2">1</span>
                </a>
                <div class="dropdown-menu navbar-dropdown navbar-dropdown-large preview-list" aria-labelledby="messageDropdown">
                  <h6 class="p-3 mb-0">Messages</h6>
                  <a class="dropdown-item preview-item">
                    <div class="preview-item-content flex-grow">
                      <span class="badge badge-pill badge-success">Request</span>
                      <p class="text-small text-muted ellipsis mb-0"> Suport needed for user123 </p>
                    </div>
                    <p class="text-small text-muted align-self-start"> 4:10 PM </p>
                  </a>
                  <h6 class="p-3 mb-0">See all activity</h6>
                </div>
              </li>
              <li class="nav-item nav-search border-0 ml-1 ml-md-3 ml-lg-5 d-none d-md-flex">
                <form class="nav-link form-inline mt-2 mt-md-0" action="{{ route('search') }}" method="GET">
                  <div class="input-group">
                    <input type="text" class="form-control" name="q" placeholder="Search" />
                    <div class="input-group-append">
                      <span class="input-group-text">
                        <i class="mdi mdi-magnify"></i>
                      </span>
                    </div>
                  </div>
                </form>
              </li>
            </ul>
            <ul class="navbar-nav navbar-nav-right ml-lg-auto">
              <li class="nav-item nav-profile dropdown border-0">
                <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-toggle="dropdown">
                  <img class="nav-profile-img mr-2" alt=""src="{{ asset('images/Unknown profile.webp' ) }}"/>
                  <span class="profile-name">{{ Auth::user()->name }}</span>
                </a>
                <div class="dropdown-menu navbar-dropdown w-100" aria-labelledby="profileDropdown">
                  <a class="dropdown-item" href="#">
                    <i class="mdi mdi-cached mr-2 text-success"></i>Profile </a>
                    
                        
                <form action="{{ route('logout') }}" method="POST">
                            @csrf
                <button type="submit" class="dropdown-item" >                                                          
                  <i class="mdi mdi-logout mr-2 text-primary"></i>  Logout</button>
                </form>
                </div>
              </li>
            </ul>
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
              <span class="mdi mdi-menu"></span>
            </button>
          </div>
        </nav>
        <div class="main-panel">
          <div class="content-wrapper pb-0">
            <div class="page-header flex-wrap">
              <h3 class="mb-0"> Hi, welcome back! <span class="pl-0 h6 pl-sm-2 text-muted d-inline-block">Your web analytics dashboard.</span>
              </h3>
            </div>
            <div class="row">
              <div class="col-xl-9 stretch-card grid-margin">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                    </div>
                    <div class="row">
                      <div class="col-sm-4">
                        <div class="card mb-3 mb-sm-0">
                          <div class="card-body py-3 px-4">
                            <p class="m-0 survey-head">Total Earnings</p>
                            <div class="d-flex justify-content-between align-items-end flot-bar-wrapper">
                              <div>
                                <h5 class="m-0 survey-value">{{$totalEarnings}} L.E</h5>
                              </div>
                              <div id="productChart" class="flot-chart"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-4">
                        <div class="card mb-3 mb-sm-0">
                          <div class="card-body py-3 px-4">
                            <p class="m-0 survey-head">Today Earnings</p>
                            <div class="d-flex justify-content-between align-items-end flot-bar-wrapper">
                              <div>
                                <h3 class="m-0 survey-value">{{$totalToday}} L.E</h3>
                              </div>
                              <div id="earningChart" class="flot-chart"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-4">
                        <div class="card">
                          <div class="card-body py-3 px-4">
                            <p class="m-0 survey-head">Today Reservations</p>
                            <div class="d-flex justify-content-between align-items-end flot-bar-wrapper">
                              <div>
                                <h5 class="m-0 survey-value">{{$todayReservationsCount}}</h5>
                              </div>
                              <div id="orderChart" class="flot-chart"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xl-8 col-sm-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body px-0 overflow-auto">
                    <h4 class="card-title pl-4">Recent Appointments</h4>
                    <div class="table-responsive">
                      <table class="table">
                        <thead class="bg-light">
                          <tr>
                            <th>Customer</th>
                            <th>Doctor</th>
                            <th>Invoice</th>
                            <th>Price</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($reservations as $reservation)
                          <tr>
                            <td>
                              <div class="d-flex align-items-center">
                                <img src="assets/images/faces/face16.jpg" alt="image" />
                                <div class="table-user-name ml-3">
                                  <p class="mb-0 font-weight-medium"> {{ $reservation->user->name  }} </p>
                                  <small>Payment on process</small>
                                </div>
                              </div>
                            </td>
                            <td>{{ $reservation->doctor->name }}</td>
                            <td>
                              <div class="badge badge-inverse-danger"> {{$reservation->status}} </div>
                            </td>
                            <td>{{$reservation->doctor->price}}</td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                    <a class="text-black mt-3 d-block pl-4" href="{{route('reservation')}}">
                      <span class="font-weight-medium h6">View all order history</span>
                      <i class="mdi mdi-chevron-right"></i></a>
                  </div>
                </div>
              </div>
              
            </div>
            <div class="row">
              <div class="col-xl-4 col-md-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title text-black">Recent Customers</h4>
                    <div class="row pt-2 pb-1">
                      <div class="col-12 col-sm-7">
                        @foreach($latestUsers as $user)
                        <div class="row">
                          <div class="col-4 col-md-4">
                            <img class="customer-img" src="assets/images/faces/face22.jpg" alt="" />
                          </div>
                          <div class="col-8 col-md-8 p-sm-0">
                            <h6 class="mb-0">{{$user->name}}</h6>
                            <p class="text-muted font-12"> {{ $user->created_at->format('h:i A') }} 
                              — {{ $user->created_at->diffForHumans() }}
                            </p>
                          </div>
                        </div>
                        @endforeach
                      </div>
                    </div>
                    <a class="text-black mt-3 d-block pl-4" href="{{route('AllUsers')}}">
                      <span class="font-weight-medium h6">View all Users</span>
                      <i class="mdi mdi-chevron-right"></i></a>
                  </div>
                </div>
              </div>
              <div class="col-xl-4 col-md-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title text-black">Doctors Survey</h4>
                    <p>Top Doctors Income</p>
                    @foreach($topDoctors as $doctor)
                    <div class="row border-bottom pb-3 pt-4 align-items-center mx-0">
                     
                      <div class="col-sm-9 pl-0">
                       
                        <div class="d-flex">
                          @if($doctor->image)
                          <img  src="{{ asset('storage/' . $doctor->image ) }} "
                          class="customer-img" alt=""  >
                      @else 
                      <img  src="{{ asset('images/Unknown profile.webp' ) }} "
                          class="customer-img" alt="">
                      @endif      
                          <div class="pl-2">
                            <h6 class="m-0">{{$doctor->name}}</h6>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-3 pl-0 pl-sm-3">
                        <div class="badge badge-inverse-success mt-3 mt-sm-0">{{ number_format($doctor->total_income, 2) }} L.E </div>
                      </div>
                    </div>
                    @endforeach
                  </div>
                </div>
              </div>
              <div class="col-xl-4 grid-margin stretch-card">
                <!--activity-->
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">
                      <span class="d-flex justify-content-between">
                        <span>Activity</span>
                        <span class="dropdown dropleft d-block">
                          <span id="dropdownMenuButton1" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <span><i class="mdi mdi-dots-horizontal"></i></span>
                          </span>
                          <span class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <a class="dropdown-item" href="#">Contact</a>
                            <a class="dropdown-item" href="#">Helpdesk</a>
                            <a class="dropdown-item" href="#">Chat with us</a>
                          </span>
                        </span>
                      </span>
                    </h4>
                    <ul class="gradient-bullet-list border-bottom">
                      @foreach($activities as $activity)
                      <li>
                        <h6 class="mb-0">{{ $activity->description }}</h6>
                        <p class="text-muted">{{ class_basename($activity->subject_type) ?? 'N/A' }}</p>
                        <p class="text-muted">
                          <span class="d-inline-block">{{ $activity->created_at->diffForHumans() }}</span>
                          <span class="d-inline-block">
                          </span>
                        </p>
                      </li>
                      @endforeach
                    </ul>
                    <a class="text-black mt-3 mb-0 d-block h6" href="#">View all <i class="mdi mdi-chevron-right"></i></a>
                  </div>
                </div>
            </div>
            </div>
          </div>
          <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
              <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © bootstrapdash.com 2020</span>
              <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center"> Free <a href="https://www.bootstrapdash.com/" target="_blank">Bootstrap dashboard template</a> from Bootstrapdash.com</span>
            </div>
          </footer>
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="assets/vendors/chart.js/Chart.min.js"></script>
    <script src="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="assets/vendors/flot/jquery.flot.js"></script>
    <script src="assets/vendors/flot/jquery.flot.resize.js"></script>
    <script src="assets/vendors/flot/jquery.flot.categories.js"></script>
    <script src="assets/vendors/flot/jquery.flot.fillbetween.js"></script>
    <script src="assets/vendors/flot/jquery.flot.stack.js"></script>
    <script src="assets/vendors/flot/jquery.flot.pie.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="assets/js/dashboard.js"></script>
    <!-- End custom js for this page -->
  </body>
</html>
