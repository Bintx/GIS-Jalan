<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Wowdash Dashboard'))</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}" sizes="16x16">

    <link rel="stylesheet" href="{{ asset('assets/css/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lib/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lib/apexcharts.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lib/dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lib/editor-katex.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lib/editor.atom-one-dark.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lib/editor.quill.snow.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lib/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lib/full-calendar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lib/jquery-jvectormap-2.0.5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lib/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lib/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lib/prism.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lib/file-upload.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lib/audioplayer.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    {{-- PENTING: Hapus baris @vite ini untuk menghindari konflik Tailwind dengan Bootstrap --}}
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    @stack('styles') {{-- Untuk CSS spesifik halaman --}}
</head>

<body class="font-sans antialiased">
    <div class="app"> {{-- Container utama dari Wowdash --}}
        <div class="layout"> {{-- Layout container dari Wowdash --}}

            @include('layouts.sidebar') {{-- Sidebar Wowdash (pastikan file ini ada dan berisi kode sidebar) --}}

            <main class="dashboard-main"> {{-- Main content area dari Wowdash --}}
                @include('layouts.header') {{-- Header/Navbar dari Wowdash (pastikan file ini ada dan berisi kode header) --}}

                <div class="dashboard-main-body"> {{-- Wrapper untuk body konten dashboard --}}
                    {{-- Ini adalah tempat untuk konten utama yang akan diisi oleh view anak (misal dashboard.blade.php) --}}
                    @yield('content')
                </div>

                @include('layouts.footer') {{-- Footer Wowdash (pastikan file ini ada dan berisi kode footer) --}}
            </main>
        </div>
    </div>

    <script src="{{ asset('assets/js/lib/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/iconify-icon.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/jquery-jvectormap-2.0.5.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('assets/js/lib/magnifc-popup.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/slick.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/prism.js') }}"></script>
    <script src="{{ asset('assets/js/lib/file-upload.js') }}"></script>
    <script src="{{ asset('assets/js/lib/audioplayer.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/homeOneChart.js') }}"></script>

    @stack('scripts') {{-- Untuk JS spesifik halaman --}}
</body>

</html>
