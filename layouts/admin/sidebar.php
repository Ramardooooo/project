<script>
let sidebarCollapsed = false;
const $ = id => document.getElementById(id);

function toggleSidebar() {
    sidebarCollapsed = !sidebarCollapsed;
    $('sidebar').style.width = sidebarCollapsed ? '64px' : '256px';
    $('sidebarToggleIcon').className = sidebarCollapsed ? 'fas fa-arrow-right' : 'fas fa-bars';
    ['sidebarTitle','sidebarSubtitle','sidebarMenu','sidebarFooter']
        .forEach(id => $(id).style.display = sidebarCollapsed ? 'none' : '');
    const main = $('mainContent');
    main.classList.remove('ml-64','ml-16');
    main.classList.add(sidebarCollapsed ? 'ml-16' : 'ml-64');
}

function toggleMaster() {
    $('masterMenu')?.classList.toggle('hidden');
    $('arrowMaster')?.classList.toggle('rotate-180');
}

function toggleKelola() {
    $('kelolaMenu')?.classList.toggle('hidden');
    $('arrowKelola')?.classList.toggle('rotate-180');
}
</script>
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
            Dashboard Admin
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
            <a href="dashboard_admin"
            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-tachometer-alt text-sm text-gray-500"></i>
                <span class="text-gray-700">Dashboard</span>
            </a>
        </li>

        <?php if ($_SESSION['role'] == 'admin') { ?>
        <li>
            <button onclick="toggleMaster()"
            class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                <div class="flex items-center gap-3">
                    <i class="fas fa-database text-sm text-gray-500"></i>
                    <span class="text-gray-700">Data Master</span>
                </div>
                <i id="arrowMaster" class="fas fa-chevron-down text-xs text-gray-500 transition-transform"></i>
            </button>

            <ul id="masterMenu"
            class="ml-6 mt-1 space-y-1 hidden">
                <li>
                    <a href="manage_users"
                    class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition">
                        Manage Users
                    </a>
                </li>
                <li>
                    <a href="manage_rt_rw"
                    class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition">
                        Manage RT/RW
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <button onclick="toggleKelola()"
            class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                <div class="flex items-center gap-3">
                    <i class="fas fa-cogs text-sm text-gray-500"></i>
                    <span class="text-gray-700">Kelola</span>
                </div>
                <i id="arrowKelola" class="fas fa-chevron-down text-xs text-gray-500 transition-transform"></i>
            </button>

<ul id="kelolaMenu"
            class="ml-6 mt-1 space-y-1 hidden">
                <li>
                    <a href="gallery"
                    class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition">
                        Kelola Gallery
                    </a>
                </li>
                <li>
                    <a href="pengumuman"
                    class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition">
                        Kelola Pengumuman
                    </a>
                </li>
            </ul>
        </li>
        <?php } ?>
    </ul>

    <div id="sidebarFooter"
    class="absolute bottom-4 left-4 right-4 text-center text-xs text-gray-400">
        <div>Version 1.0</div>
        <div>&copy; 2025 Lurahgo.id</div>
    </div>
</div>
