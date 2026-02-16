<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
  body {
    position: relative;
    overflow-x: hidden;
    background-color: #CFD8DC;
}
body,
html { height: 100%;}
.nav .open > a, 
.nav .open > a:hover, 
.nav .open > a:focus {background-color: transparent;}

/*-------------------------------*/
/*           Wrappers            */
/*-------------------------------*/

#wrapper {
    padding-left: 0;
    -webkit-transition: all 0.5s ease;
    -moz-transition: all 0.5s ease;
    -o-transition: all 0.5s ease;
    transition: all 0.5s ease;
}

#wrapper.toggled {
    padding-left: 220px;
}

#sidebar-wrapper {
    z-index: 1000;
    left: 220px;
    width: 0;
    height: 100%;
    margin-left: -220px;
    overflow-y: auto;
    overflow-x: hidden;
    background: #4169e1;
    -webkit-transition: all 0.5s ease;
    -moz-transition: all 0.5s ease;
    -o-transition: all 0.5s ease;
    transition: all 0.5s ease;
}

#sidebar-wrapper::-webkit-scrollbar {
  display: none;
}

#wrapper.toggled #sidebar-wrapper {
    width: 220px;
}

#page-content-wrapper {
    width: 100%;
    padding-top: 70px;
}

#wrapper.toggled #page-content-wrapper {
    position: absolute;
    margin-right: -220px;
}

/*-------------------------------*/
/*     Sidebar nav styles        */
/*-------------------------------*/
.navbar {
  padding: 0;
}

.sidebar-nav {
    position: absolute;
    top: 0;
    width: 220px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.sidebar-nav li {
    position: relative; 
    line-height: 20px;
    display: inline-block;
    width: 100%;
}

.sidebar-nav li:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    z-index: -1;
    height: 100%;
    width: 3px;
    background-color: #1c1c1c;
    -webkit-transition: width .2s ease-in;
      -moz-transition:  width .2s ease-in;
       -ms-transition:  width .2s ease-in;
            transition: width .2s ease-in;

}
.sidebar-nav li:first-child a {
    color: #fff;
    background-color: #1a1a1a;
}
.sidebar-nav li:nth-child(5n+1):before {
    background-color: #ec1b5a;   
}
.sidebar-nav li:nth-child(5n+2):before {
    background-color: #79aefe;   
}
.sidebar-nav li:nth-child(5n+3):before {
    background-color: #314190;   
}
.sidebar-nav li:nth-child(5n+4):before {
    background-color: #279636;   
}
.sidebar-nav li:nth-child(5n+5):before {
    background-color: #7d5d81;   
}

.sidebar-nav li:hover:before,
.sidebar-nav li.open:hover:before {
    width: 100%;
    -webkit-transition: width .2s ease-in;
      -moz-transition:  width .2s ease-in;
       -ms-transition:  width .2s ease-in;
            transition: width .2s ease-in;

}

.sidebar-nav li a {
    display: block;
    color: #ddd;
    text-decoration: none;
    padding: 10px 15px 10px 30px;    
}

.sidebar-nav li a:hover,
.sidebar-nav li a:active,
.sidebar-nav li a:focus,
.sidebar-nav li.open a:hover,
.sidebar-nav li.open a:active,
.sidebar-nav li.open a:focus{
    color: #fff;
    text-decoration: none;
    background-color: transparent;
}
.sidebar-header {
    text-align: center;
    font-size: 20px;
    position: relative;
    width: 100%;
    display: inline-block;
}
.sidebar-brand {
    height: 65px;
    position: relative;
    background:#212531;
    background: linear-gradient(to right bottom, #2f3441 50%, #212531 50%);
   padding-top: 1em;
}
.sidebar-brand a {
    color: #ddd;
}
.sidebar-brand a:hover {
    color: #fff;
    text-decoration: none;
}
.dropdown-header {
    text-align: center;
    font-size: 1em;
    color: #ddd;
    background:#212531;
    background: linear-gradient(to right bottom, #2f3441 50%, #212531 50%);
}
.sidebar-nav .dropdown-menu {
    position: relative;
    width: 100%;
    padding: 0;
    margin: 0;
    border-radius: 0;
    border: none;
    background-color: #222;
    box-shadow: none;
}
.dropdown-menu.show {
    top: 0;
}

/*-------------------------------*/
/*       Hamburger-Cross         */
/*-------------------------------*/

.hamburger {
  position: fixed;
  top: 20px;  
  z-index: 999;
  display: block;
  width: 32px;
  height: 32px;
  margin-left: 15px;
  background: transparent;
  border: none;
}
.hamburger:hover,
.hamburger:focus,
.hamburger:active {
  outline: none;
}
.hamburger.is-closed:before {
  content: '';
  display: block;
  width: 100px;
  font-size: 14px;
  color: #fff;
  line-height: 32px;
  text-align: center;
  opacity: 0;
  -webkit-transform: translate3d(0,0,0);
  -webkit-transition: all .35s ease-in-out;
}
.hamburger.is-closed:hover:before {
  opacity: 1;
  display: block;
  -webkit-transform: translate3d(-100px,0,0);
  -webkit-transition: all .35s ease-in-out;
}

.hamburger.is-closed .hamb-top,
.hamburger.is-closed .hamb-middle,
.hamburger.is-closed .hamb-bottom,
.hamburger.is-open .hamb-top,
.hamburger.is-open .hamb-middle,
.hamburger.is-open .hamb-bottom {
  position: absolute;
  left: 0;
  height: 4px;
  width: 100%;
}
.hamburger.is-closed .hamb-top,
.hamburger.is-closed .hamb-middle,
.hamburger.is-closed .hamb-bottom {
  background-color: #1a1a1a;
}
.hamburger.is-closed .hamb-top { 
  top: 5px; 
  -webkit-transition: all .35s ease-in-out;
}
.hamburger.is-closed .hamb-middle {
  top: 50%;
  margin-top: -2px;
}
.hamburger.is-closed .hamb-bottom {
  bottom: 5px;  
  -webkit-transition: all .35s ease-in-out;
}

.hamburger.is-closed:hover .hamb-top {
  top: 0;
  -webkit-transition: all .35s ease-in-out;
}
.hamburger.is-closed:hover .hamb-bottom {
  bottom: 0;
  -webkit-transition: all .35s ease-in-out;
}
.hamburger.is-open .hamb-top,
.hamburger.is-open .hamb-middle,
.hamburger.is-open .hamb-bottom {
  background-color: #1a1a1a;
}
.hamburger.is-open .hamb-top,
.hamburger.is-open .hamb-bottom {
  top: 50%;
  margin-top: -2px;  
}
.hamburger.is-open .hamb-top { 
  -webkit-transform: rotate(45deg);
  -webkit-transition: -webkit-transform .2s cubic-bezier(.73,1,.28,.08);
}
.hamburger.is-open .hamb-middle { display: none; }
.hamburger.is-open .hamb-bottom {
  -webkit-transform: rotate(-45deg);
  -webkit-transition: -webkit-transform .2s cubic-bezier(.73,1,.28,.08);
}
.hamburger.is-open:before {
  content: '';
  display: block;
  width: 100px;
  font-size: 14px;
  color: #fff;
  line-height: 32px;
  text-align: center;
  opacity: 0;
  -webkit-transform: translate3d(0,0,0);
  -webkit-transition: all .35s ease-in-out;
}
.hamburger.is-open:hover:before {
  opacity: 1;
  display: block;
  -webkit-transform: translate3d(-100px,0,0);
  -webkit-transition: all .35s ease-in-out;
}

</style>

<div id="wrapper">
   <div class="overlay"></div>
    
    <!-- Sidebar -->
    <nav class="navbar navbar-inverse fixed-top" id="sidebar-wrapper" role="navigation">
     <ul class="nav sidebar-nav">
       <div class="sidebar-header">
       <div class="sidebar-brand">
         <a href="#">Brand</a></div></div>
       <li><a href="{{ route('/') }}"><i class="fas fa-fw fa-home"></i><span> Home</span></a></li>
       @can('user-list')
       <li class="dropdown">
        <a href="#works" class="dropdown-toggle"  data-toggle="dropdown"><i class="fas fa-users"></i> Manage Users <span class="caret"></span></a>
          <ul class="dropdown-menu animated fadeInLeft" role="menu">
            <div class="dropdown-header">Dropdown heading</div>
            <li><a href="{{ route('user.index') }}">Users</a></li>
            @can('role-list')
            <li><a href="{{ route('roles.index') }}">Manage Roles</a></li>
            @endcan
            @can('permission-list')
            <li><a href="{{ route('permissions.index') }}">Manage Permissions</a></li>
            @endcan
          </ul>
      </li>
       @endcan
       @can('process-list')
       <li class="dropdown">
        <a href="#works" class="dropdown-toggle"  data-toggle="dropdown"><i class="fas fa-sitemap"></i> Services <span class="caret"></span></a>
          <ul class="dropdown-menu animated fadeInLeft" role="menu">
            <div class="dropdown-header">Dropdown heading</div>
            <li><a href="{{ route('service.index') }}">Services</a></li>
            @can('service-edit')    
            <li><a href="{{ route('inst.viewclerk') }}">Manage Officers</a></li>
            @endcan
          </ul>
      </li>
      @endcan
      @can('process-list')
       <li class="dropdown">
        <a href="#works" class="dropdown-toggle"  data-toggle="dropdown"><i class="fas fa-cogs"></i> Process<span class="caret"></span></a>
          <ul class="dropdown-menu animated fadeInLeft" role="menu">
            <div class="dropdown-header">Dropdown heading</div>
            @can('process-create')
            <li><a href="{{ route('process.create') }}">Add Process</a></li>
            @endcan
            @can('process-list')
            <li><a href="{{ route('process.index',1) }}">Pending Process</a></li>
            <li><a href="{{ route('process.index',2) }}">Zonal Pending Process</a></li>
            <li><a href="{{ route('process.index',3) }}">Completed Process</a></li>
            <li><a href="{{ route('process.index',4) }}">Holding Process</a></li>
            <li><a href="{{ route('process.bulkedit') }}">Bulk Update</a></li>
            @endcan
          </ul>
      </li>
      @endcan
      @can('process-list')
      <li class="dropdown">
        <a href="#works" class="dropdown-toggle"  data-toggle="dropdown"><i class="fa fa-institution"></i> Institutes<span class="caret"></span></a>
          <ul class="dropdown-menu animated fadeInLeft" role="menu">
            <div class="dropdown-header">Dropdown heading</div>
            <li><a href="{{ route('institute.index') }}">Institutes</a></li>
            <li><a href="{{ route('institute.prlclass') }}">Parallel Classes</a></li>
            <li><a href="{{ route('institute.students') }}">Students</a></li>
          </ul>
      </li>
      @endcan
      @can('employee-create')
      <li class="dropdown">
        <a href="#works" class="dropdown-toggle"  data-toggle="dropdown"><i class="far fa-address-card"></i> Employees<span class="caret"></span></a>
          <ul class="dropdown-menu animated fadeInLeft" role="menu">
            <div class="dropdown-header">Dropdown heading</div>
            @can('employee-list')
            <li><a href="{{ route('employee.index') }}">Employees</a></li>
            <li><a href="{{ route('employee.analysis') }}">Analyse Employees</a></li>
            @endcan
            @can('employee-create')
            <li><a href="{{ route('employee.dummy_index') }}">Approval for Updation</a></li>
            @endcan
            @can('employee-list')
            <li><a href="{{ route('submitform.index',6) }}">Profile</a></li>
            @endcan
            @can('employee-delete')
            <li><a href="{{ route('employee.appendview') }}">Append Institute for clerks</a></li>
            @endcan
            @can('employee-create')
            <li><a href="{{ route('salaryimport.view') }}">Import Salary File</a></li>
            <li><a href="{{ route('crosscheckcheck.salary') }}">Cross-Check with Salary</a></li>
            <!--<li><a href="{{ route('nemisimport.view') }}">Import NEMIS File</a></li>-->
            <li><a href="{{ route('crosscheckcheck.nemis') }}">Cross-Check with NEMIS</a></li>
            @endcan
          </ul>
      </li>
      @endcan
      @can('process-create')
      <li class="dropdown">
        <a href="#works" class="dropdown-toggle"  data-toggle="dropdown"><i class="fa fa-exchange"></i> Transfer<span class="caret"></span></a>
          <ul class="dropdown-menu animated fadeInLeft" role="menu">
            <div class="dropdown-header">Dropdown heading</div>
            <li><a href="{{ route('employee.transfer') }}">Transfer Validation</a></li>
            <li><a href="{{ route('transfer.index') }}">Transfer List</a></li>
          </ul>
      </li>
      @endcan
      @can('cadre-export')
      <li class="dropdown">
        <a href="#works" class="dropdown-toggle"  data-toggle="dropdown"><i class="fas fa-chalkboard-teacher"></i> Cadre<span class="caret"></span></a>
          <ul class="dropdown-menu animated fadeInLeft" role="menu">
            <div class="dropdown-header">Dropdown heading</div>
            <li><a href="{{ route('submitform.index',1) }}">Cadre Summary</a></li>
            <li><a href="{{ route('submitform.index',3) }}">Detailed Cadre</a></li>
            <li><a href="{{ route('submitform.index',2) }}">Cadre Export</a></li>
          </ul>
      </li>
      @endcan
      @can('cadre-view')
      <li class="dropdown">
        <a href="#exam" class="dropdown-toggle"  data-toggle="dropdown"><i class="fas fa-chalkboard-teacher"></i> Exam<span class="caret"></span></a>
          <ul class="dropdown-menu animated fadeInLeft" role="menu">
            <div class="dropdown-header">Dropdown heading</div>
            <li><a href="{{ route('marks.create') }}">Enter Marks</a></li>
            <li><a href="{{ route('reports.ol.exam.final.result') }}">O/L Final Exam Analysis</a></li>
            <li><a href="{{ route('reports.ol.exam.final.subject.result') }}">O/L Final Exam Subject Analysis</a></li>
            <li><a href="{{ route('reports.pass') }}">Subject wise Pass Percentage Analysis</a></li>
            <li><a href="{{ route('reports.average') }}">Subject wise PI Analysis</a></li>
            <li><a href="{{ route('reports.students.subject.marks') }}">Subject wise Students Analysis</a></li>
            <li><a href="{{ route('reports.student.average.allsubject.marks') }}">Student's All Subject Average</a></li>
            <li><a href="{{ route('reports.exam.attendance') }}">Student's Attendance of Exam</a></li>
            <li><a href="{{ route('reports.student.marks.table') }}">Student Marks Table</a></li>
            <li><a href="{{ route('reports.student.ranks.table') }}">Student Rank Table</a></li>
            <li><a href="{{ route('reports.student.attendance') }}">Student's Attendance</a></li>
            <li><a href="{{ route('reports.student.marks.print') }}">Student Mrks Print</a></li>
            <li><a href="{{ route('reports.school.subject.analysis') }}">School wise Subject Analysis</a></li>
            <li><a href="{{ route('reports.school.overall.analysis') }}">School wise Overall Analysis</a></li>
            <li><a href="{{ route('reports.item.analysis') }}">Item Analysis</a></li>
            <li><a href="{{ route('student_responses.create') }}">Add Students Responses</a></li>
          </ul>
      </li>
      
      @endcan
      @can('cadre-export')
        <li class="dropdown">
            <a href="#works" class="dropdown-toggle"  data-toggle="dropdown"><i class="fas fa-clipboard-check"></i> Attendance<span class="caret"></span></a>
              <ul class="dropdown-menu animated fadeInLeft" role="menu">
                <div class="dropdown-header">Dropdown heading</div>
                @if(Auth::user()->desig == 'principal')
                <li><a href="{{ route('attendance.create') }}">Attendance Submit</a></li>
                @endif
                <li><a href="{{ route('attendance.index') }}">@if(Auth::user()->desig != 'principal') Attendane (Zonal) @else Attendane-List @endif</a></li>
                @if(Auth::user()->desig != 'principal')
                <li><a href="{{ route('submitform.index',5) }}">Attendane (School-Indiv.)</a></li>
                <li><a href="{{ route('attendance.list') }}">Attendane (School-All)</a></li>
                @endif
              </ul>
        </li>
        {{-- Messaging--}}
        <li class="dropdown">
            <a href="#works" class="dropdown-toggle"  data-toggle="dropdown"><i class="fas fa-mail-bulk"></i> Messaging<span class="caret"></span></a>
              <ul class="dropdown-menu animated fadeInLeft" role="menu">
                <div class="dropdown-header">Dropdown heading</div>
                <li><a href="{{ route('message.create') }}">Compose Message</a></li>
                <li><a href="{{ route('message.index') }}">Inbox</a></li>
              </ul>
        </li>
        @endcan
        @can('settings-manage')
        {{-- News--}}
        <li class="dropdown">
            <a href="#works" class="dropdown-toggle"  data-toggle="dropdown"><i class="fas fa-mail-bulk"></i> Website<span class="caret"></span></a>
              <ul class="dropdown-menu animated fadeInLeft" role="menu">
                <div class="dropdown-header">Dropdown General</div>
                <li><a href="{{ route('news.index') }}">Manage News</a></li>
                <li><a href="{{ route('programme.index') }}">Manage Coordinators</a></li>
                <li><a href="{{ route('staff.index') }}">Manage Staff Officers</a></li>
                <li><a href="{{ route('upload.index') }}">Manage PDFs</a></li>

                <hr style="border-color: white; margin: 0.5rem 0;">

                <div class="dropdown-header">School Buildings</div>
                <li><a href="{{ route('building-categories.index') }}">Manage Building Categories</a></li>
                <li><a href="{{ route('building-types.index') }}">Manage Building Types</a></li>
                <li><a href="{{ route('building-repair-categories.index') }}">Manage Building Repair Category</a></li>
                <li><a href="{{ route('buildings.index') }}">Manage Buildings</a></li>
                <li><a href="{{ route('room-types.index') }}">Manage Room Types</a></li>
              </ul>
        </li>
        @endcan
        @can('settings-manage')
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cog"></i> Settings<span class="caret"></span></a>
            <ul class="dropdown-menu animated fadeInLeft" role="menu">
                <li><a href="{{ route('cadre-subject.index') }}">Manage Cadre Subjects</a></li>
                <li><a href="{{ route('designation.index') }}">Manage Designation</a></li>
                <li><a href="{{ route('dsdivision.index') }}">Manage DS Divisions</a></li>
                <li><a href="{{ route('gndivision.index') }}">Manage GN Divisions</a></li>
                <li><a href="{{ route('degrees.index') }}">Manage Degrees</a></li>
                <li><a href="{{ route('deg-institutes.index') }}">Manage Degree Institutes</a></li>
                <li><a href="{{ route('prof-qualifications.index') }}">Manage Professional Qualifications</a></li>
                <li><a href="{{ route('deg-subjects.index') }}">Manage Degree Subject</a></li>
            </ul>
        </li>
        @endcan
        @can('results-manage')
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cog"></i> Manage Results<span class="caret"></span></a>
            <ul class="dropdown-menu animated fadeInLeft" role="menu">
                <li><a href="{{ route('manage.result') }}">Manage Grade/Units/Questions</a></li>
            </ul>
        </li>
        @endcan
        <!-- Sidebar Toggler (Sidebar) -->
        <div class="text-center d-none d-md-inline">
             <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>
</ul>
</nav>
    
<script>
$(document).ready(function () {
  var trigger = $('#sidebarToggleTop'),
      overlay = $('.overlay'),
     isClosed = false;

    trigger.click(function () {
      hamburger_cross();      
    });

    function hamburger_cross() {

      if (isClosed == true) {          
        overlay.hide();
        trigger.removeClass('is-open');
        trigger.addClass('is-closed');
        isClosed = false;
      } else {   
        overlay.show();
        trigger.removeClass('is-closed');
        trigger.addClass('is-open');
        isClosed = true;
      }
  }
  
  $('[data-toggle="offcanvas"]').click(function () {
        $('#wrapper').toggleClass('toggled');
  });  
});

</script>