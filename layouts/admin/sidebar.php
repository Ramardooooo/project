<div class="w-64 min-h-screen fixed
    bg-gradient-to-b from-slate-900/70 to-slate-800/70 backdrop-blur-xl backdrop-saturate-150
    text-white shadow-2xl border-r border-slate-600/30">

    <!-- Header -->
    <div class="p-6 border-b border-white/20">
        <div class="text-lg font-semibold flex items-center gap-2 drop-shadow">
            <i class="fas fa-home"></i>
            Lurahgo.id
        </div>
        <div class="text-xs text-white/70 mt-1">Dashboard Admin</div>
    </div>

    <!-- Menu -->
    <ul class="mt-4 space-y-1 px-3">

        <li>
            <a href="/PROJECT/home"
               class="flex items-center gap-3 px-4 py-2 rounded-lg
                      hover:bg-white/20 transition">
                <i class="fas fa-home text-sm"></i>
                <span>Beranda</span>
            </a>
        </li>

        <li>
            <a href="/PROJECT/dashboard_admin"
               class="flex items-center gap-3 px-4 py-2 rounded-lg
                      hover:bg-white/20 transition">
                <i class="fas fa-tachometer-alt text-sm"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <?php if ($_SESSION['role'] == 'admin') { ?>
        <li>
            <button onclick="toggleMaster()"
                class="w-full flex items-center justify-between px-4 py-2 rounded-lg
                       hover:bg-white/20 transition">
                <div class="flex items-center gap-3">
                    <i class="fas fa-database text-sm"></i>
                    <span>Data Master</span>
                </div>
                <i id="arrowMaster"
                   class="fas fa-chevron-down text-xs transition-transform"></i>
            </button>

            <ul id="masterMenu" class="hidden ml-6 mt-1 space-y-1">
                <li>
                    <a href="/PROJECT/manage_users"
                       class="block px-4 py-2 text-sm rounded-lg
                              hover:bg-white/20 transition">
                        Manage Users
                    </a>
                </li>
                <li>
                    <a href="/PROJECT/manage_rt_rw"
                       class="block px-4 py-2 text-sm rounded-lg
                              hover:bg-white/20 transition">
                        Manage RT/RW
                    </a>
                </li>
            </ul>
        </li>
        <?php } ?>

        <li class="mt-6">
            <a href="rama.php"
               class="flex items-center gap-3 px-4 py-2 rounded-lg
                      hover:bg-white/20 transition">
                <i class="fas fa-user text-sm"></i>
                <span>Ramadhani Fadillah</span>
            </a>
        </li>
    </ul>

    <!-- Footer -->
    <div class="absolute bottom-4 left-4 right-4 text-center text-xs text-white/70">
        <div>Version 1.0</div>
        <div>© 2025 Lurahgo.id</div>
    </div>
</div>

<script>
function toggleMaster() {
    const menu = document.getElementById('masterMenu');
    const arrow = document.getElementById('arrowMaster');
    menu.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}
</script>
