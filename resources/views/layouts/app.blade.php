<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
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

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-draw@1.0.4/dist/leaflet.draw.css" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.0/dist/sweetalert2.min.css">

    {{-- START: Fixed Header CSS (from your working map.blade.php) --}}
    <style>
        .navbar-header {
            position: fixed;
            top: 0;
            left: 250px;
            /* Sesuaikan jika sidebar bukan 250px */
            width: calc(100% - 250px);
            z-index: 1050;
            /* background-color: #fff; */
            /* BARIS INI DIHAPUS */
            /* Ensure this matches your theme's background */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            /* Keep original padding unless it causes issues */
            /* padding: 1rem 1.5rem; */
        }

        body {
            padding-top: 80px;
            /* Agar konten tidak ketiban header. Pastikan 80px ini adalah tinggi aktual header Anda */
            overflow-y: auto;
            /* Allow body to scroll */
        }

        /* Jika Anda memiliki toggle sidebar yang mengubah lebar sidebar, Anda perlu menambahkan CSS tambahan
            misalnya, jika ada class 'sidebar-collapsed' pada body atau elemen lain:
        body.sidebar-collapsed .navbar-header {
            left: 80px; // Ganti 80px dengan lebar sidebar saat collapsed
            width: calc(100% - 80px);
        }
        */
    </style>
    {{-- END: Fixed Header CSS --}}

    @stack('styles') {{-- Untuk CSS spesifik halaman --}}
</head>

<body class="font-sans antialiased">
    <div class="app">
        <div class="layout">

            @include('layouts.sidebar')

            <main class="dashboard-main">
                @include('layouts.header')

                <div class="dashboard-main-body">
                    @yield('content')
                </div>

                @include('layouts.footer')
            </main>
        </div>
    </div>

    {{-- ... (scripts remain the same) ... --}}
    <script src="{{ asset('assets/js/lib/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/bootstrap.bundle.min.js') }}"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.0/dist/sweetalert2.min.js"></script>
    <script>
        $(document).ready(function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: '{{ session('error') }}',
                    showConfirmButton: true,
                });
            @endif

            @if (session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: '{{ session('warning') }}',
                    showConfirmButton: false,
                    timer: 4000
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Input Tidak Valid!',
                    html: '{!! implode('<br>', $errors->all()) !!}',
                    showConfirmButton: true,
                });
            @endif
        });
    </script>
    @stack('scripts')
</body>

</html>
