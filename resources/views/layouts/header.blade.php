<nav class="navbar navbar-expand navbar-light bg-white fixed-top topbar mb-4 shadow">

@if(Auth::user()->hasAnyRole(['User', 'Admin','super_admin','Sch_Admin']))
    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn btn-link rounded-circle mr-3" data-toggle="offcanvas">
      <i class="fa fa-bars"></i>
    </button>
    <a href="{{ url()->previous() }}" class="pathclass">Go back</a>
@endif


    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

      <!-- Nav Item - Search Dropdown (Visible Only XS) -->
      <li class="nav-item dropdown no-arrow d-sm-none">
        <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-search fa-fw"></i>
        </a>
        <!-- Dropdown - Messages -->
        <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
          <form class="form-inline mr-auto w-100 navbar-search">
            <div class="input-group">
              <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
              <div class="input-group-append">
                <button class="btn btn-primary" type="button">
                  <i class="fas fa-search fa-sm"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li>

      {{-- Home page --}}
      <li class="nav-item dropdown no-arrow mx-1">
          <a class="nav-link dropdown-toggle" href="{{route('/')}}" data-toggle="tooltip" data-placement="bottom" title="home"  role="button">
          <i class="fas fa-home fa-fw"></i></a>
      </li>

     <!-- Nav Item - Pending Process Notification -->
      <li class="nav-item dropdown no-arrow mx-1">
        @if(Auth::user()->hasAnyRole(['User','super_admin','Admin']))
            @include('processnotification.show')
        @endif
      </li>

      <!-- Nav Item - Messages Notification  -->
      <li class="nav-item dropdown no-arrow mx-1" id="messageT" data-url="{{route('messages.five')}}">
        @include('message.message')
      </li>

      <!-- Nav Item - General Notification  -->
      <li class="nav-item dropdown no-arrow mx-1">
       @include('notification.notification')
      </li>

    
      <div class="topbar-divider d-none d-sm-block"></div>

      <!-- Nav Item - User Information -->
      <li class="nav-item dropdown no-arrow">
        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{Auth()->user()->name}}</span>
          @if(Auth()->user()->employee_id)
            <img class="img-profile rounded-circle" src="{{'/images/employees/'.Auth()->user()->employee_id.'.jpg'}}">
          @else
            <img class="img-profile rounded-circle" src="{{asset('backend/img/avatar.png')}}">
          @endif
        </a>
        <!-- Dropdown - User Information -->
        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
          <a class="dropdown-item" href="{{route('admin-profile')}}">
            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
            Profile
          </a>
          <a class="dropdown-item" href="{{route('change.password.form')}}">
            <i class="fas fa-key fa-sm fa-fw mr-2 text-gray-400"></i>
            Change Password
          </a>
          <!--<a class="dropdown-item" href="#">-->
          <!--  <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>-->
          <!--  Settings-->
          <!--</a>-->
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#"
                onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                 <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> {{ __('Logout') }}
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
      </li>

    </ul>

  </nav>