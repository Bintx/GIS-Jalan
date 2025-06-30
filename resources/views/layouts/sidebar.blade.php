<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="{{ url('/') }}" class="sidebar-logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="light-logo">
            <img src="{{ asset('assets/images/logo-light.png') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('assets/images/logo-icon.png') }}" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            {{-- Link Dashboard Utama --}}
            <li>
                <a href="{{ route('dashboard') }}">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                    <span>Dashboard</span>
                </a>
            </li>
            @can('admin', 'admin')
                <li class="sidebar-menu-group-title">Manajemen Data GIS</li>

                {{-- Dropdown Data Master --}}
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="solar:folder-2-light" class="menu-icon"></iconify-icon>
                        <span>Data Master</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="{{ route('regional.index') }}"><i
                                    class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Data Regional</a>
                        </li>
                        <li>
                            <a href="{{ route('jalan.index') }}"><i
                                    class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Data Jalan</a>
                        </li>
                    </ul>
                </li>

                {{-- Link Laporan Kerusakan --}}
                <li>
                    <a href="{{ route('kerusakan-jalan.index') }}">
                        <iconify-icon icon="solar:danger-circle-line-duotone" class="menu-icon"></iconify-icon>
                        <span>Laporan Kerusakan</span>
                    </a>
                </li>

                {{-- Link Peta Interaktif Utama --}}
                <li>
                    <a href="{{ route('map.overview') }}"> {{-- Menggunakan route name --}}
                        <iconify-icon icon="solar:map-point-wave-bold" class="menu-icon"></iconify-icon>
                        <span>Peta Interaktif</span>
                    </a>
                </li>

                {{-- Bagian Asli Template Wowdash (jika tidak digunakan, bisa dihapus) --}}
                <li class="sidebar-menu-group-title">Application</li>

                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="flowbite:users-group-outline" class="menu-icon"></iconify-icon>
                        <span>Users</span>
                    </a>
                    <ul class="sidebar-submenu">
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="icon-park-outline:setting-two" class="menu-icon"></iconify-icon>
                        <span>Settings</span>
                    </a>
                    <ul class="sidebar-submenu">
                    </ul>
                </li>
            </ul>
        @endcan

        @can('pejabat_desa')
            <li>
                <a href="{{ route('kerusakan-jalan.index') }}">
                    <iconify-icon icon="solar:danger-circle-line-duotone" class="menu-icon"></iconify-icon>
                    <span>Laporan Kerusakan</span>
                </a>
            </li>

            {{-- Link Peta Interaktif Utama --}}
            <li>
                <a href="{{ route('map.overview') }}"> {{-- Menggunakan route name --}}
                    <iconify-icon icon="solar:map-point-wave-bold" class="menu-icon"></iconify-icon>
                    <span>Peta Interaktif</span>
                </a>
            </li>
        @endcan
    </div>
</aside>
