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

$query = "UPDATE notifications SET is_read = 1 WHERE is_read = 0 AND (user_id = $user_id OR role IN ('all', '$role'))";

if (mysqli_query($conn, $query)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to mark all as read']);
}
?>
