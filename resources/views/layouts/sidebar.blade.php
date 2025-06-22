<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="{{ url('/') }}" class="sidebar-logo"> {{-- Mengganti index.html dengan root Laravel --}}
            <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="light-logo">
            <img src="{{ asset('assets/images/logo-light.png') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('assets/images/logo-icon.png') }}" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                    <span>Dashboard</span>
                </a>
                <ul class="sidebar-submenu">
                    <li class="dropdown">
                        <a href="javascript:void(0)">
                            <iconify-icon icon="heroicons:document" class="menu-icon"></iconify-icon>
                            <span>Forms</span>
                        </a>
                    </li>
                    <li class="dropdown">
                        <a href="javascript:void(0)">
                            <iconify-icon icon="mingcute:storage-line" class="menu-icon"></iconify-icon>
                            <span>Table</span>
                        </a>
                    </li>
                    <li class="dropdown">
                        <a href="javascript:void(0)">
                            <iconify-icon icon="flowbite:users-group-outline" class="menu-icon"></iconify-icon>
                            <span>Users</span>
                        </a>
                    </li>

                    <li class="dropdown">
                        <a href="javascript:void(0)">
                            <i class="ri-user-settings-line text-xl me-14 d-flex w-auto"></i>
                            <span>Role & Access</span>
                        </a>
                    </li>
                    <li class="dropdown">
                        <a href="javascript:void(0)">
                            <iconify-icon icon="icon-park-outline:setting-two" class="menu-icon"></iconify-icon>
                            <span>Settings</span>
                        </a>
                </ul>
            </li>
        </ul>
    </div>
</aside>
