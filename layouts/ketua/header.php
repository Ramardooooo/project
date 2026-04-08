
<?php if (!session_id()) session_start(); ?>
<?php include __DIR__ . '/../../config/database.php'; 
include __DIR__ . '/../../account/helpers.php'; ?>
<?php
$user_id = $_SESSION['user_id'] ?? null;
$user = null;
if ($user_id) {
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Lurahgo.id - Dashboard Ketua</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    #sidebar.mini { width: 64px !important; }
    #sidebar.mini + #mainContent, body:has(#sidebar.mini) header { margin-left: 64px !important; }
    body:has(#sidebar.mini) header.ml-64 { margin-left: 64px !important; }
    body:has(#sidebar.mini) #mainContent.ml-64 { margin-left: 64px !important; }
    #mainContent, header.ml-64 { transition: margin-left 0.3s ease; }
</style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-blue-50 min-h-screen flex flex-col">
<header class="bg-white/90 backdrop-blur-xl shadow-2xl border-b border-white/60 ring-1 ring-white/50 ml-64 transition-all duration-300 hover:shadow-3xl" id="mainHeader">
    <div class="flex justify-between items-center px-8 py-6">

        <div class="flex items-center">
            <button onclick="toggleSidebar()" class="group text-gray-700 hover:text-blue-600 mr-6 transition-all duration-300 p-3 rounded-2xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:shadow-xl hover:scale-110 hover:rotate-180 shadow-md ring-1 ring-transparent hover:ring-blue-200" id="hamburgerBtn">
                <i class="fas fa-bars text-2xl group-hover:scale-110 transition-transform duration-300"></i>
            </button>
            <h1 class="text-3xl font-black tracking-tight bg-gradient-to-r from-gray-800 to-blue-800 bg-clip-text text-transparent drop-shadow-lg"></h1>
        </div>

        <div class="flex items-center space-x-4">

            <!-- Notifications Ketua -->
            <div class="relative group">
                <button id="notifBtnKetua" class="relative p-3 text-gray-600 hover:text-blue-600 rounded-3xl hover:bg-gradient-to-br hover:from-blue-50 hover:to-indigo-50 hover:shadow-2xl hover:scale-110 transition-all duration-300 shadow-lg ring-1 ring-gray-200/50 hover:ring-blue-200 z-10" onclick="if(typeof toggleNotifications === 'function') toggleNotifications('Ketua')">
                    <i class="fas fa-bell text-2xl relative"></i>
                    <span id="notifBadgeKetua" class="absolute -top-2 -right-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-xs rounded-2xl w-7 h-7 flex items-center justify-center text-[11px] shadow-2xl ring-2 ring-white/50 animate-pulse min-w-[28px] font-bold">0</span>
                </button>
                <div id="notifDropdownKetua" class="hidden absolute right-0 mt-4 w-96 bg-white/95 backdrop-blur-2xl rounded-3xl shadow-2xl border border-white/50 ring-2 ring-white/40 z-[9999] overflow-hidden hover:shadow-3xl transition-all duration-300">
                    <div class="p-6 border-b border-white/50 bg-gradient-to-r from-blue-500/10 to-indigo-500/10">
                        <div class="flex items-center justify-between">
                            <h4 class="font-black text-gray-900 text-xl tracking-tight bg-gradient-to-r from-gray-900 to-blue-900 bg-clip-text text-transparent drop-shadow-sm">Pemberitahuan Ketua</h4>
                            <button onclick="markAllRead('ketua')" class="text-sm bg-white/70 px-4 py-2 rounded-2xl font-bold text-blue-600 hover:text-blue-700 hover:bg-white/90 hover:shadow-lg transition-all duration-200 backdrop-blur-sm ring-1 ring-blue-200/50">Tandai semua</button>
                        </div>
                    </div>
                    <div id="notifListKetua" class="max-h-96 overflow-y-auto p-2"></div>
                    <div class="p-4 border-t border-white/50 bg-gradient-to-r from-slate-50/50 to-blue-50/50 text-center">
                        <a href="#" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-black text-base gap-2 hover:bg-white/70 px-6 py-3 rounded-2xl hover:shadow-md transition-all duration-200 backdrop-blur-sm ring-1 ring-blue-200/30">Lihat semua <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Profile Simple -->
            <div class="flex items-center space-x-3 bg-gray-50 rounded-full px-4 py-2 border border-gray-200">
                <?php if ($user['profile_photo']): ?>
                    <img src="<?php echo get_profile_photo_url($user['profile_photo']); ?>" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-gray-300">
                <?php else: ?>
                    <div class="w-10 h-10 rounded-full border-2 border-gray-300 bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-lg">
                        <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
                    </div>
                <?php endif; ?>
                <span class="text-sm font-semibold text-gray-700">
                    <?= $_SESSION['username'] ?? 'User'; ?>
                </span>
            </div>

            <!-- Logout -->
            <a href="logout" class="flex items-center space-x-2 text-gray-500 hover:text-gray-900 transition-colors duration-200 p-2.5 rounded-full hover:bg-gray-100">
                <i class="fas fa-sign-out-alt text-xl"></i>
                <span class="text-xs font-medium hidden sm:inline">Logout</span>
            </a>
        </div>
    </div>
</header>

<script src="/static/notifications.js"></script>
<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebarCollapsed = !sidebarCollapsed;
    
    sidebar.style.width = sidebarCollapsed ? '64px' : '256px';
    
    // Toggle sidebar elements
    ['sidebarTitle','sidebarSubtitle','sidebarMenu','sidebarFooter'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = sidebarCollapsed ? 'none' : '';
    });
    
    // Toggle margins
    const header = document.getElementById('mainHeader');
    const mainContent = document.getElementById('mainContent');
    if (header) {
        header.classList.toggle('ml-64');
        header.classList.toggle('ml-16');
    }
    if (mainContent) {
        mainContent.classList.toggle('ml-64');
        mainContent.classList.toggle('ml-16');
    }
}
</script>

