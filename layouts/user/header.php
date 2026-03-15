<?php if (!session_id()) session_start(); ?>
<?php include __DIR__ . '/../../config/database.php'; ?>
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
<title>Lurahgo.id - Dashboard User</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    /* Sidebar toggle styles */
    #sidebar.collapsed {
        width: 0px;
        overflow: hidden;
    }
    #sidebar.collapsed + #mainContent,
    body:has(#sidebar.collapsed) #mainContent,
    body:has(#sidebar.collapsed) header {
        margin-left: 0 !important;
    }
    body:has(#sidebar.collapsed) header.ml-64 {
        margin-left: 0 !important;
    }
    body:has(#sidebar.collapsed) #mainContent.ml-64 {
        margin-left: 0 !important;
    }
    /* Smooth transition for content */
    #mainContent, header.ml-64 {
        transition: margin-left 0.3s ease;
    }
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
                <!-- Notifications -->
                <div class="relative">
                    <button id="notifBtn" class="relative p-2.5 text-gray-500 hover:text-gray-900 rounded-full hover:bg-gray-100 transition-all duration-200" onclick="toggleNotifications()">
                        <i class="fas fa-bell text-xl relative z-10"></i>
                        <span id="notifBadgeUser" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center text-[10px] shadow-lg min-w-[20px]">0</span>
                    </button>
                    <div id="notifDropdownUser" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border ring-1 ring-black/5 z-[9999] overflow-hidden">
                        <div class="p-4 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-gray-800 text-lg">Pemberitahuan</h4>
                                <button onclick="markAllRead('user')" class="text-xs text-blue-600 hover:text-blue-800 font-medium hover:underline">Tandai semua</button>
                            </div>
                        </div>
                        <div id="notifListUser" class="max-h-96 overflow-y-auto"></div>
                        <div class="p-3 border-t text-center bg-gray-50">
                            <a href="/pages/user/notifications.php" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Lihat semua →</a>
                        </div>
                    </div>
                </div>

                <!-- Profile -->
                <div class="flex items-center space-x-3 bg-gray-50 rounded-full px-4 py-2 border border-gray-200">
                    <?php if ($user && $user['profile_photo']): ?>
                        <img src="../../<?php echo $user['profile_photo']; ?>" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-gray-300">
                    <?php else: ?>
                        <div class="w-10 h-10 rounded-full border-2 border-gray-300 bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-lg">
                            <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <span class="text-sm font-semibold text-gray-700 max-w-[120px] truncate">
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

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const header = document.getElementById('mainHeader');
    const mainContent = document.getElementById('mainContent');
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const sidebarItems = document.querySelectorAll('.sidebar-text');
    const sidebarTitle = document.getElementById('sidebarTitle');
    const sidebarSubtitle = document.getElementById('sidebarSubtitle');
    const sidebarFooter = document.getElementById('sidebarFooter');
    
    if (!sidebar) return;
    
    // Toggle mini class
    const isMini = sidebar.classList.toggle('mini');
    
    // Update sidebar width via style
    sidebar.style.width = isMini ? '64px' : '256px';
    
    // Toggle header margin
    if (header) {
        header.classList.toggle('ml-16', isMini);
        header.classList.toggle('ml-64', !isMini);
    }
    
    // Toggle main content margin
    if (mainContent) {
        mainContent.classList.toggle('ml-16', isMini);
        mainContent.classList.toggle('ml-64', !isMini);
    }
    
    // Show/hide text elements
    sidebarItems.forEach(item => {
        item.style.display = isMini ? 'none' : 'inline';
    });
    
    // Hide title and subtitle when mini
    if (sidebarTitle) sidebarTitle.style.display = isMini ? 'none' : 'inline';
    if (sidebarSubtitle) sidebarSubtitle.style.display = isMini ? 'none' : 'block';
    if (sidebarFooter) sidebarFooter.style.display = isMini ? 'none' : 'block';
    
    // Update hamburger icon
    if (hamburgerBtn) {
        const icon = hamburgerBtn.querySelector('i');
        if (icon) {
            if (isMini) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-bars');
            } else {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-bars');
            }
        }
    }
}

// Notification functions - User
function toggleNotificationsUser() {
    toggleNotifications('');
}
window.toggleNotificationsUser = toggleNotificationsUser;

function toggleNotifications() {
    const dropdown = document.getElementById('notifDropdownUser');
    dropdown.classList.toggle('hidden');
    if (!dropdown.classList.contains('hidden')) window.loadNotifications('');
}
</script>
<script src="/static/notifications.js"></script>
<script>
  // Auto load on page ready
  document.addEventListener('DOMContentLoaded', () => {
    window.loadNotifications('');
    console.log('Notifications initialized');
  });
</script>
