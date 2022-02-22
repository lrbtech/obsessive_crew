<!DOCTYPE html>
<html lang="en">
    <!-- BEGIN: Head -->
    <head>
        <meta charset="utf-8">
        <link href="/images/logo_icon.png" rel="shortcut icon">
        <!-- <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Midone admin is super flexible, powerful, clean & modern responsive tailwind admin template with unlimited possibilities.">
        <meta name="keywords" content="admin template, Midone admin template, dashboard template, flat admin template, responsive admin template, web app">
        <meta name="author" content="LEFT4CODE"> -->
        <title>Obsessive Crew - Admin</title>
        <!-- BEGIN: CSS Assets-->
        <link rel="stylesheet" href="/admin-assets/dist/css/app.css" />
        <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/extensions/sweetalert2.min.css">
        @yield('extra-css')
        <!-- END: CSS Assets-->
    </head>
    <!-- END: Head -->
    <body class="app">
        <!-- BEGIN: Mobile Menu -->
        @include('admin.mobile_menu')
        <!-- END: Mobile Menu -->
        <div class="flex">
            <!-- BEGIN: Side Menu -->
            @include('admin.sidebar')
            <!-- END: Side Menu -->
            <!-- BEGIN: Content -->  
            @yield('body-section')
            <!-- END: Content -->      
        </div>
        <!-- BEGIN: JS Assets-->
        <!-- <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script> -->
        <!-- <script src="https://maps.googleapis.com/maps/api/js?key=["your-google-map-api"]&libraries=places"></script> -->        
        <script src="/admin-assets/dist/js/app.js"></script>
        <script src="/app-assets/vendors/js/extensions/sweetalert2.all.min.js"></script>     
        @yield('extra-js')
        <!-- END: JS Assets-->

    </body>
</html>