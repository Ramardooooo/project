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
    $('kelolaMenu')?.classList.toggle('hidden');
    $('arrowKelola')?.classList.toggle('rotate-180');
}

// Close dropdowns on page load
document.addEventListener('DOMContentLoaded', function() {
    const dropdowns = ['kelolaMenu'];
    dropdowns.forEach(id => {
        const el = $(id);
        if (el && !el.classList.contains('hidden')) {
            el.classList.add('hidden');
        }
    });
    const arrows = ['arrowKelola'];
    arrows.forEach(id => {
        const el = $(id);
        if (el) {
            el.classList.remove('rotate-180');
        }
    });
});
</script>
<div id="sidebar"
class="min-h-screen fixed top-0 left-0 z-50 backdrop-blur-xl bg-gradient-to-b from-white/95 to-white/80 text-gray-900 shadow-2xl border-r border-white/60 ring-1 ring-white/40 transition-all duration-300 ease-out hover:shadow-3xl"
style="width:256px;">

    <div class="p-6 border-b border-white/50 bg-gradient-to-r from-slate-50/70 to-blue-50/50 rounded-br-2xl">
        <div class="flex items-center justify-between gap-2">
            <div class="text-xl font-black tracking-tight bg-gradient-to-r from-gray-900 via-blue-600 to-indigo-700 bg-clip-text text-transparent drop-shadow-sm">
                <span id="sidebarTitle">Lurahgo.id</span>
            </div>
        </div>

        <div class="text-xs bg-white/70 backdrop-blur-sm px-3 py-1.5 rounded-full font-semibold text-gray-700 mt-3 shadow-sm ring-1 ring-white/50" id="sidebarSubtitle">
            Dashboard Ketua
        </div>
    </div>

    <ul class="mt-8 space-y-2 px-4" id="sidebarMenu">

        <li>
            <a href="home"
            class="group flex items-center gap-4 px-5 py-3 rounded-2xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:shadow-lg hover:scale-[1.02] hover:border-r-4 hover:border-blue-500 transition-all duration-300 ease-out relative overflow-hidden sidebar-link bg-white/50 ring-1 ring-transparent hover:ring-blue-200">
                <i class="fas fa-house text-lg group-hover:text-blue-600 group-hover:scale-110 transition-all duration-250 w-6 flex-shrink-0"></i>
                <span class="font-semibold text-gray-800 group-hover:text-blue-700 sidebar-text transition-all duration-250">Beranda</span>
            </a>
        </li>

        <li>
            <a href="dashboard_ketua"
            class="group flex items-center gap-4 px-5 py-3 rounded-2xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:shadow-lg hover:scale-[1.02] hover:border-r-4 hover:border-blue-500 transition-all duration-300 ease-out relative overflow-hidden sidebar-link bg-white/50 ring-1 ring-transparent hover:ring-blue-200">
                <i class="fas fa-tachometer-alt text-lg group-hover:text-blue-600 group-hover:scale-110 transition-all duration-250 w-6 flex-shrink-0"></i>
                <span class="font-semibold text-gray-800 group-hover:text-blue-700 sidebar-text transition-all duration-250">Dashboard</span>
            </a>
        </li>

        <li>
            <button onclick="toggleKelola()"
            class="group w-full flex items-center justify-between px-5 py-3 rounded-2xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:shadow-lg transition-all duration-300 ease-out bg-white/50 ring-1 ring-transparent hover:ring-blue-200 cursor-pointer">
                <div class="flex items-center gap-4">
                    <i class="fas fa-cogs text-lg group-hover:text-blue-600 transition-all duration-250 w-6 flex-shrink-0"></i>
                    <span class="font-semibold text-gray-800 group-hover:text-blue-700 sidebar-text transition-all duration-250">Kelola</span>
                </div>
                <i id="arrowKelola" class="fas fa-chevron-down text-lg group-hover:text-blue-600 transition-all duration-250"></i>
            </button>

            <ul id="kelolaMenu"
            class="ml-8 mt-2 space-y-1 bg-white/40 backdrop-blur-sm p-3 rounded-2xl border border-white/50 hidden">
                <li>
                    <a href="manage_warga"
                    class="block group px-5 py-2.5 text-sm font-semibold text-gray-700 rounded-xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:shadow-md transition-all duration-250 hover:text-blue-700">
                        Kelola Warga
                    </a>
                </li>
                <li>
                    <a href="manage_kk"
                    class="block group px-5 py-2.5 text-sm font-semibold text-gray-700 rounded-xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:shadow-md transition-all duration-250 hover:text-blue-700">
                        Kelola KK
                    </a>
                </li>
                <li>
                    <a href="manage_wilayah"
                    class="block group px-5 py-2.5 text-sm font-semibold text-gray-700 rounded-xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:shadow-md transition-all duration-250 hover:text-blue-700">
                        Kelola Wilayah
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <a href="mutasi_warga"
            class="group flex items-center gap-4 px-5 py-3 rounded-2xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:shadow-lg hover:scale-[1.02] hover:border-r-4 hover:border-blue-500 transition-all duration-300 ease-out relative overflow-hidden sidebar-link bg-white/50 ring-1 ring-transparent hover:ring-blue-200">
                <i class="fas fa-exchange-alt text-lg group-hover:text-blue-600 group-hover:scale-110 transition-all duration-250 w-6 flex-shrink-0"></i>
                <span class="font-semibold text-gray-800 group-hover:text-blue-700 sidebar-text transition-all duration-250">Mutasi Warga</span>
            </a>
        </li>

        <li>
            <a href="laporan"
            class="group flex items-center gap-4 px-5 py-3 rounded-2xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:shadow-lg hover:scale-[1.02] hover:border-r-4 hover:border-blue-500 transition-all duration-300 ease-out relative overflow-hidden sidebar-link bg-white/50 ring-1 ring-transparent hover:ring-blue-200">
                <i class="fas fa-chart-bar text-lg group-hover:text-blue-600 group-hover:scale-110 transition-all duration-250 w-6 flex-shrink-0"></i>
                <span class="font-semibold text-gray-800 group-hover:text-blue-700 sidebar-text transition-all duration-250">Laporan</span>
            </a>
        </li>

        <li>
            <a href="settings"
            class="group flex items-center gap-4 px-5 py-3 rounded-2xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:shadow-lg hover:scale-[1.02] hover:border-r-4 hover:border-blue-500 transition-all duration-300 ease-out relative overflow-hidden sidebar-link bg-white/50 ring-1 ring-transparent hover:ring-blue-200">
                <i class="fas fa-gear text-lg group-hover:text-blue-600 group-hover:scale-110 transition-all duration-250 w-6 flex-shrink-0"></i>
                <span class="font-semibold text-gray-800 group-hover:text-blue-700 sidebar-text transition-all duration-250">Pengaturan</span>
            </a>
        </li>

    </ul>

    <div id="sidebarFooter"
    class="absolute bottom-6 left-6 right-6 text-center text-xs bg-white/70 backdrop-blur-md p-4 rounded-3xl border border-white/60 shadow-xl ring-1 ring-white/50">
        <div class="font-medium text-gray-600 mb-1">Version 1.2</div>
        <div class="text-gray-500">&copy; 2026 Lurahgo.id</div>
    </div>
</div>
