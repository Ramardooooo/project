<script src="../../PROJECT/layouts/admin/sidebar.js"></script>
<div id="sidebar"
class="min-h-screen fixed top-0 left-0 z-50
bg-white text-gray-800 shadow-md border-r border-gray-200
transition-all duration-300 ease-in-out"
style="width:256px;">

    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between gap-2">
<div class="text-lg font-semibold">
                <span id="sidebarTitle">Lurahgo.id</span>
            </div>

            <div class="flex gap-1">
                <button onclick="togglePosition()"
                class="text-gray-500 hover:text-gray-800 p-1 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-exchange-alt"></i>
                </button>

                <button onclick="toggleSidebar()"
                class="text-gray-500 hover:text-gray-800 p-1 rounded-lg hover:bg-gray-100">
                    <i id="sidebarToggleIcon" class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        <div class="text-xs text-gray-500 mt-1" id="sidebarSubtitle">
            Dashboard User
        </div>
    </div>

    <ul class="mt-4 space-y-1 px-3" id="sidebarMenu">

        <li>
            <a href="/PROJECT/home"
            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-home text-sm text-gray-500"></i>
                <span class="text-gray-700">Beranda</span>
            </a>
        </li>

        <li>
            <a href="/PROJECT/dashboard_user"
            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-tachometer-alt text-sm text-gray-500"></i>
                <span class="text-gray-700">Dashboard</span>
            </a>
        </li>

        <li>
            <a href="/PROJECT/profile"
            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-user text-sm text-gray-500"></i>
                <span class="text-gray-700">Profile</span>
            </a>
        </li>

        <li>
            <a href="/PROJECT/settings"
            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-cog text-sm text-gray-500"></i>
                <span class="text-gray-700">Pengaturan</span>
            </a>
        </li>

        <li>
            <a href="/PROJECT/gallery"
            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-images text-sm text-gray-500"></i>
                <span class="text-gray-700">Galeri</span>
            </a>
        </li>

    </ul>

    <div id="sidebarFooter"
    class="absolute bottom-4 left-4 right-4 text-center text-xs text-gray-400">
        <div>Version 1.0</div>
        <div>&copy; 2025 Lurahgo.id</div>
    </div>
</div>
