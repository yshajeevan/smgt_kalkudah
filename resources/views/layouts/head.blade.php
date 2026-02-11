<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Service Management System of Zonal Education Office, Batticaloa West">
    <meta name="author" content="Shajeevan (SLEAS-III)">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SMGT</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('backend/img/favicon.ico') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" 
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" 
          crossorigin="anonymous">

    <!-- SB Admin 2 CSS -->
    <link href="{{ asset('css/sb-admin-2.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Datatables -->
    <link href="{{ asset('css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/buttons.bootstrap.min.css') }}" rel="stylesheet">

    <!-- Select2 -->
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">

    <!-- Toast -->
    <link href="{{ asset('css/jquery.toast.css') }}" rel="stylesheet">

    <!-- Slider -->
    <link href="{{ asset('css/jquery.bxslider.css') }}" rel="stylesheet">

    <!-- jQuery UI -->
    <link href="{{ asset('css/jquery-ui.min.css') }}" rel="stylesheet">

    <!-- Summernote -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css">

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-203062795-2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){ dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'UA-203062795-2');
    </script>

    {{-- Page-level Styles --}}
    @stack('styles')

</head>
