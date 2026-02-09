<script src="../../PROJECT/layouts/admin/sidebar.js"></script>
<div id="sidebar"
class="min-h-screen fixed top-0 left-0 z-50
bg-gradient-to-b from-slate-900/80 to-slate-800/80 backdrop-blur-xl backdrop-saturate-150
text-white shadow-2xl border-r border-slate-600/30
transition-all duration-300 ease-in-out"
style="width:256px;">

    <div class="p-6 border-b border-white/20">
        <div class="flex items-center justify-between gap-2">
            <div class="text-lg font-semibold flex items-center gap-2">
                <i class="fas fa-home"></i>
                <span id="sidebarTitle">Lurahgo.id</span>
            </div>

            <div class="flex gap-1">
                <button onclick="togglePosition()"
                class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10">
                    <i class="fas fa-exchange-alt"></i>
                </button>

                <button onclick="toggleSidebar()"
                class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10">
                    <i id="sidebarToggleIcon" class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        <div class="text-xs text-white/70 mt-1" id="sidebarSubtitle">
            Dashboard Admin
        </div>
    </div>

    <ul class="mt-4 space-y-1 px-3" id="sidebarMenu">

        <li>
            <a href="/PROJECT/home"
            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/20 transition">
                <i class="fas fa-home text-sm"></i>
                <span>Beranda</span>
            </a>
        </li>

        <li>
            <a href="dashboard_admin"
            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white/20 transition">
                <i class="fas fa-tachometer-alt text-sm"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <?php if ($_SESSION['role'] == 'admin') { ?>
        <li>
            <button onclick="toggleMaster()"
            class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-white/20 transition">
                <div class="flex items-center gap-3">
                    <i class="fas fa-database text-sm"></i>
                    <span>Data Master</span>
                </div>
                <i id="arrowMaster" class="fas fa-chevron-down text-xs transition-transform"></i>
            </button>

            <ul id="masterMenu"
            class="ml-6 mt-1 space-y-1 hidden">
                <li>
                    <a href="manage_users"
                    class="block px-4 py-2 text-sm rounded-lg hover:bg-white/20 transition">
                        Manage Users
                    </a>
                </li>
                <li>
                    <a href="manage_rt_rw"
                    class="block px-4 py-2 text-sm rounded-lg hover:bg-white/20 transition">
                        Manage RT/RW
                    </a>
                </li>
            </ul>
        </li>
        <?php } ?>
    </ul>

    <div id="sidebarFooter"
    class="absolute bottom-4 left-4 right-4 text-center text-xs text-white/70">
        <div>Version 1.0</div>
        <div>© 2025 Lurahgo.id</div>
    </div>
</div>

