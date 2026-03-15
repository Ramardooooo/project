<?php
session_start();
include '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'user';

// Role-specific queries
switch ($role) {
    case 'admin':
        $count_query = "SELECT COUNT(*) as unread FROM notifications WHERE is_read = 0";
        $list_query = "SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5";
        break;
    case 'ketua':
        $count_query = "SELECT COUNT(*) as unread FROM notifications WHERE role = 'ketua' OR role = 'all' AND is_read = 0";
        $list_query = "SELECT * FROM notifications WHERE role = 'ketua' OR role = 'all' ORDER BY created_at DESC LIMIT 5";
        break;
    default: // user
        $count_query = "SELECT COUNT(*) as unread FROM notifications WHERE role = 'user' OR role = 'all' AND is_read = 0 AND user_id = ?";
        $list_query = "SELECT * FROM notifications WHERE (role = 'user' OR role = 'all') AND user_id = ? ORDER BY created_at DESC LIMIT 5";
        break;
}

if ($role === 'user') {
    $stmt_count = mysqli_prepare($conn, $count_query);
    mysqli_stmt_bind_param($stmt_count, "i", $user_id);
    mysqli_stmt_execute($stmt_count);
    $count_result = mysqli_stmt_get_result($stmt_count);
    $count_row = mysqli_fetch_assoc($count_result);
    $unread_count = $count_row['unread'];
    
    $stmt_list = mysqli_prepare($conn, $list_query);
    mysqli_stmt_bind_param($stmt_list, "i", $user_id);
    mysqli_stmt_execute($stmt_list);
    $list_result = mysqli_stmt_get_result($stmt_list);
} else {
    $result_count = mysqli_query($conn, $count_query);
    $count_row = mysqli_fetch_assoc($result_count);
    $unread_count = $count_row['unread'];
    
    $result_list = mysqli_query($conn, $list_query);
}

$notifications = [];
while ($row = mysqli_fetch_assoc($list_result ?? $result_list)) {
    $notifications[] = $row;
}

echo json_encode([
    'success' => true,
    'unread_count' => $unread_count,
    'notifications' => $notifications
]);
?>

