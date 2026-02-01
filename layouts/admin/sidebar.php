<div class="w-64 bg-gradient-to-b from-blue-500 to-blue-600 text-blue-100 min-h-screen fixed shadow-2xl">
    <div class="p-6 border-b border-blue-800">
        <div class="text-lg font-semibold flex items-center gap-2">
            <i class="fas fa-home"></i>
            Lurahgo.id
        </div>
        <div class="text-xs text-slate-300 mt-1">Dashboard Admin</div>
    </div>

    <ul class="mt-4 space-y-1 px-3">
        <li>
        <a href="/PROJECT/home" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-800">
                <i class="fas fa-home text-sm"></i>
                <span>Beranda</span>
            </a>
        </li>

        <li>
            <a href="/PROJECT/dashboard_admin" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-tachometer-alt text-sm"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <?php if ($_SESSION['role'] == 'admin') { ?>

        <li>
            <button onclick="toggleMaster()"
                class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-blue-800 transition">
                <div class="flex items-center gap-3">
                    <i class="fas fa-database text-sm"></i>
                    <span>Data Master</span>
                </div>
                <i id="arrowMaster" class="fas fa-chevron-down text-xs transition-transform"></i>
            </button>

            <ul id="masterMenu" class="hidden ml-6 mt-1 space-y-1">
                <li>
                    <a href="/PROJECT/manage_users"
                       class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-slate-600">
                        Manage Users
                    </a>
                </li>
                <li>
                    <a href="/PROJECT/manage_rt_rw"
                       class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-slate-600">
                        Manage RT/RW
                    </a>
                </li>
            </ul>
        </li>

        <?php } ?>

        <li class="mt-6">
            <a href="rama.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-800">
                <i class="fas fa-user text-sm"></i>
                <span>Ramadhani Fadillah</span>
            </a>
        </li>
    </ul>

    <div class="absolute bottom-4 left-4 right-4 text-center text-xs text-slate-300">
        <div>Version 1.0</div>
        <div>© 2025 Lurago.id</div>
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
