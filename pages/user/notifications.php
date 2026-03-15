<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../home");
    exit();
}

include '../../config/database.php';
include '../../layouts/user/header.php';
include '../../layouts/user/sidebar.php';

$user_id = $_SESSION['user_id'];
$nama = $_SESSION['username'] ?? '';

$query = "SELECT * FROM notifications WHERE (role = 'user' OR role = 'all') AND user_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$notifications = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div id="mainContent" class="ml-64 min-h-screen bg-gray-50 p-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200">
            <div class="p-8 border-b border-gray-200">
                <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-bell text-amber-500 mr-3"></i>
                    Pemberitahuan
                </h1>
                <p class="text-gray-600">Semua notifikasi untuk akun Anda</p>
            </div>
            
            <div class="divide-y divide-gray-200">
                <?php if (empty($notifications)): ?>
                    <div class="p-16 text-center">
                        <i class="fas fa-bell-slash text-6xl text-gray-300 mb-6"></i>
                        <h3 class="text-2xl font-semibold text-gray-500 mb-2">Belum ada pemberitahuan</h3>
                        <p class="text-gray-400">Pemberitahuan akan muncul di sini saat ada aktivitas baru.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($notifications as $notif): ?>
                        <div class="p-6 hover:bg-gray-50 transition-colors <?php echo !$notif['is_read'] ? 'bg-amber-50 border-l-4 border-amber-400' : ''; ?>">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br rounded-xl flex items-center justify-center text-white shadow-lg">
                                    <?php if ($notif['type'] === 'approval'): ?>
                                        <i class="fas fa-check-circle text-xl"></i>
                                    <?php elseif ($notif['type'] === 'announcement'): ?>
                                        <i class="fas fa-bullhorn text-xl"></i>
                                    <?php else: ?>
                                        <i class="fas fa-info-circle text-xl"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <h4 class="font-semibold text-gray-900 text-lg truncate"><?php echo htmlspecialchars($notif['title']); ?></h4>
                                        <span class="text-xs text-gray-400 ml-2 whitespace-nowrap">
                                            <?php echo date('d M Y H:i', strtotime($notif['created_at'])); ?>
                                        </span>
                                    </div>
                                    <p class="text-gray-600 mb-2 line-clamp-2"><?php echo htmlspecialchars($notif['message']); ?></p>
                                    <?php if (!$notif['is_read']): ?>
                                        <button onclick="markAsRead(<?php echo $notif['id']; ?>)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Tandai terbaca</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function markAsRead(id) {
    fetch('/api/mark_notification_read.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({id: id})
    }).then(() => {
        location.reload();
    });
}
</script>

