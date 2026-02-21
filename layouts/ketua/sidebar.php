<script>
let sidebarCollapsed = false;
const $ = id => document.getElementById(id);

function toggleSidebar() {
    sidebarCollapsed = !sidebarCollapsed;
    
    const sidebar = $('sidebar');
    const header = $('mainHeader');
    const mainContent = $('mainContent');
    
    // Toggle sidebar width
    sidebar.style.width = sidebarCollapsed ? '64px' : '256px';
    
    // Toggle sidebar elements
    ['sidebarTitle','sidebarSubtitle','sidebarMenu','sidebarFooter'].forEach(id => {
        const el = $(id);
        if (el) el.style.display = sidebarCollapsed ? 'none' : '';
    });
    
    // Toggle header margin
    if (header) {
        header.classList.remove('ml-64', 'ml-16');
        header.classList.add(sidebarCollapsed ? 'ml-16' : 'ml-64');
    }
    
    // Toggle main content margin
    if (mainContent) {
        mainContent.classList.remove('ml-64', 'ml-16');
        mainContent.classList.add(sidebarCollapsed ? 'ml-16' : 'ml-64');
    }
}

function toggleKelola() {
    const kelolaMenu = document.getElementById('kelolaMenu');
    const arrowKelola = document.getElementById('arrowKelola');
    kelolaMenu?.classList.toggle('hidden');
    arrowKelola?.classList.toggle('rotate-180');
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
                <span id="sidebarTitle">Ketua Panel</span>
            </div>
        </div>

        <div class="text-xs text-gray-500 mt-1" id="sidebarSubtitle">
            Dashboard Ketua
        </div>
    </div>

    <ul class="mt-4 space-y-1 px-3" id="sidebarMenu">

        <li>
            <a href="dashboard_ketua"
            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-tachometer-alt text-sm text-gray-500 w-5"></i>
                <span class="text-gray-700 sidebar-text">Dashboard</span>
            </a>
        </li>

        <li>
            <button onclick="toggleKelola()"
            class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                <div class="flex items-center gap-3">
                    <i class="fas fa-cogs text-sm text-gray-500 w-5"></i>
                    <span class="text-gray-700 sidebar-text">Kelola</span>
                </div>
                <i id="arrowKelola" class="fas fa-chevron-down text-xs text-gray-500 transition-transform"></i>
            </button>

            <ul id="kelolaMenu"
            class="ml-6 mt-1 space-y-1 hidden">
                <li>
                    <a href="manage_warga"
                    class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition">
                        Kelola Warga
                    </a>
                </li>
                <li>
                    <a href="manage_kk"
                    class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition">
                        Kelola KK
                    </a>
                </li>
                <li>
                    <a href="manage_wilayah"
                    class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition">
                        Kelola Wilayah
                    </a>
                </li>
                <li>
                    <a href="manage_master_data"
                    class="block px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition">
                        Kelola Master Data
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <a href="mutasi_warga"
            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-exchange-alt text-sm text-gray-500 w-5"></i>
                <span class="text-gray-700 sidebar-text">Mutasi Warga</span>
            </a>
        </li>

        <li>
            <a href="laporan"
            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-chart-bar text-sm text-gray-500 w-5"></i>
                <span class="text-gray-700 sidebar-text">Laporan</span>
            </a>
        </li>

        <li>
            <a href="home"
            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-bullhorn text-sm text-gray-500 w-5"></i>
                <span class="text-gray-700 sidebar-text">Beranda</span>
            </a>
        </li>
    </ul>

    <div id="sidebarFooter"
    class="absolute bottom-4 left-4 right-4 text-center text-xs text-gray-400">
        <div>Version 1.0</div>
        <div>&copy; 2025 Ketua Panel</div>
    </div>
</div>
