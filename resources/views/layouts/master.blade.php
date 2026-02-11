<!DOCTYPE html>
<html lang="en">

@include('layouts.head')

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        {{-- Sidebar --}}
        @unless(request()->is('full-calender'))
            @include('layouts.sidebar')
        @endunless

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                {{-- Topbar --}}
                @include('layouts.header')

                <!-- Begin Page Content -->
                <div class="container-fluid pb-5">
                    @yield('main-content')
                </div>
                <!-- End Page Content -->

            </div>
            <!-- End Main Content -->

            {{-- Footer --}}
            @include('layouts.footer')

        </div>
        <!-- End Content Wrapper -->

    </div>
    <!-- End Page Wrapper -->


    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    {{-- Logout Modal --}}
    @include('layouts.logout-modal')

    {{-- Global Scripts --}}
    @include('layouts.scripts')

    {{-- Page-level Scripts --}}
    @stack('scripts')

    <script>
        // Auto hide alerts
        setTimeout(() => $('.alert').slideUp(), 4000);
    </script>

</body>
</html>
