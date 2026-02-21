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
        </div>

        <div class="text-xs text-gray-500 mt-1" id="sidebarSubtitle">
            Dashboard User
        </div>
    </div>

    <ul class="mt-4 space-y-1 px-3" id="sidebarMenu">

        <li>
            <a href="/PROJECT/home"
            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-home text-sm text-gray-500 w-5"></i>
                <span class="text-gray-700 sidebar-text">Beranda</span>
            </a>
        </li>

        <li>
            <a href="/PROJECT/dashboard_user"
            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-tachometer-alt text-sm text-gray-500 w-5"></i>
                <span class="text-gray-700 sidebar-text">Dashboard</span>
            </a>
        </li>

        <li>
            <a href="data_diri"
            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-user text-sm text-gray-500 w-5"></i>
                <span class="text-gray-700 sidebar-text">Data Diri</span>
            </a>
        </li>

        <li>
            <a href="anggota_kk"
            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-users text-sm text-gray-500 w-5"></i>
                <span class="text-gray-700 sidebar-text">Daftar Anggota</span>
            </a>
        </li>

        <li>
            <a href="settings"
            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-cog text-sm text-gray-500 w-5"></i>
                <span class="text-gray-700 sidebar-text">Pengaturan</span>
            </a>
        </li>

    </ul>

    <div id="sidebarFooter"
    class="absolute bottom-4 left-4 right-4 text-center text-xs text-gray-400">
        <div>&copy; 2025 Lurahgo.id</div>
    </div>
</div>
