
<header>
    <div class="container">
         <div class="row">

              <div class="col-md-4 col-sm-5">
                   <p>Welcome to a Professional Dentist Clinic</p>
              </div>
                   
              <div class="col-md-8 col-sm-7 text-align-right">
                   <span class="phone-icon"><i class="fa fa-phone"></i>  010-070-0170</span>
                   <span class="date-icon"><i class="fa fa-calendar-plus-o"></i> 6 Days a Week (Fri OFF)</span>
                   <span class="email-icon"><i class="fa fa-envelope-o"></i> <a href="/">Dentist_clinic@company.com</a></span>
              </div>

         </div>
    </div>
</header>


<!-- MENU -->
<section class="navbar navbar-default navbar-static-top" role="navigation">
    <div class="container">

         <div class="navbar-header">
              <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                   <span class="icon icon-bar"></span>
                   <span class="icon icon-bar"></span>
                   <span class="icon icon-bar"></span>
              </button>

              <!-- lOGO TEXT HERE -->
              <a href="/" class="navbar-brand" style="display:flex; align-items:center; gap:8px;">
                <img src="images/D.jpg" alt="Logo" style="width:40px; height:40px;"><span>entist clinic</span>
              </a>
         </div>

         <!-- MENU LINKS -->
         <div class="collapse navbar-collapse">
              <ul class="nav navbar-nav navbar-right">
               <li class="nav-item nav-search border-0 mt-4 ml-1 ml-md-3 ml-lg-5 d-none d-md-flex">
                    <form class="nav-link form-inline mt-2 mt-md-0" style="margin-top:0px;"action="{{ route('search') }}" method="GET">
                      <div class="input-group">
                        <input type="text" class="form-control" name="q" placeholder="Search" aria-label="Search"/>
                        <div class="input-group-append">
                          <span class="input-group-text">
                            <i class="mdi mdi-magnify"></i>
                          </span>
                        </div>
                      </div>
                    </form>
                  </li>
                  <li>
                    @if (Request::is('/')) {{-- يعني لو أنا الآن في الصفحة الرئيسية --}}
                        <a href="#about" class="smoothScroll">About Us</a>
                    @else
                        <a href="{{ route('about') }}" class="smoothScroll">About Us</a>
                    @endif
                </li>
                   <li><a href="{{ route('listDoctors') }}" class="smoothScroll">Doctors</a></li>
                   <li><a href="{{route('reservation')}}" class="smoothScroll">Reservations</a></li>
                   
                   <li>
                    @auth
                    <a href="{{ route('dashboard') }}">
                      Welcome  {{ Auth::user()->name }}
                    </a>
                     @else
                    <a href="{{ route('login') }}">Login</a>
                    @endauth
                    </li>
                   <li>
                    @auth
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                         @csrf
                         <button type="submit" class="smoothScroll" style="background:none; border:none; color:inherit; cursor:pointer; font:inherit; padding:20%; margin:10%; pr-5; line-height:normal; vertical-align:middle;">
                             Logout
                         </button>
                     </form>
                     @else
                    <a href="{{ route('register') }}">Register</a>
                    @endauth
                    </li>
                    @if( Auth::check() && Auth::user()->is_admin)
                   <li class="appointment-btn"><a href="{{route('reservation')}}">Make an appointment</a></li>
                   @else
                   <li class="appointment-btn"><a href="{{route('makeAppointment')}}">Make an appointment</a></li>
                    @endif
              </ul>
         </div>

    </div>
</section>
</html>