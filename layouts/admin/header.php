<?php if (!session_id()) session_start(); ?>
<?php include __DIR__ . '/../../account/helpers.php'; ?>
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
<title>Lurahgo.id - Dashboard RT/RW</title>
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
<body class="bg-gray-100 min-h-screen flex flex-col">
<header class="bg-white shadow-sm border-b border-gray-200 ml-64 transition-all duration-300" id="mainHeader">
    <div class="flex justify-between items-center px-8 py-5">

        <div class="flex items-center">
            <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 mr-4 transition-colors duration-200 p-2 rounded-lg hover:bg-gray-100" id="hamburgerBtn">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <h1 class="text-2xl font-bold text-gray-800 tracking-wide"></h1>
        </div>


        <div class="flex items-center space-x-3">
            <!-- Notifications Admin -->
            <div class="relative">
                <button id="notifBtnAdmin" class="relative p-2.5 text-gray-500 hover:text-gray-900 rounded-full hover:bg-gray-100 transition-all duration-200" onclick="toggleNotificationsAdmin()">
                    <i class="fas fa-bell text-xl relative z-10"></i>
                    <span id="notifBadgeAdmin" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center text-[10px] shadow-lg min-w-[20px]">0</span>
                </button>
                <div id="notifDropdownAdmin" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border ring-1 ring-black/5 z-[9999] overflow-hidden">
                    <div class="p-4 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-gray-800 text-lg">Pemberitahuan Admin</h4>
                            <button onclick="markAllRead('admin')" class="text-xs text-blue-600 hover:text-blue-800 font-medium hover:underline">Tandai semua</button>
                        </div>
                    </div>
                    <div id="notifListAdmin" class="max-h-96 overflow-y-auto"></div>
                    <div class="p-3 border-t text-center bg-gray-50">
                        <a href="/pages/admin/notifications.php" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Lihat semua →</a>
                    </div>
                </div>
            </div>

            <!-- Profile -->
            <div class="flex items-center space-x-3 bg-gray-50 rounded-full px-4 py-2 border border-gray-200">
                <?php if ($user && $user['profile_photo']): ?>
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
