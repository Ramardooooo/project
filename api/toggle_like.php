<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

include __DIR__ . '/../config/database.php';

$input = null;
$gallery_id = null;

// Handle both JSON and form-data
$raw_input = file_get_contents('php://input');
if (!empty($raw_input)) {
    $decoded = json_decode($raw_input, true);
    if ($decoded && isset($decoded['gallery_id'])) {
        $gallery_id = (int)$decoded['gallery_id'];
    }
}
if (!$gallery_id && isset($_POST['gallery_id'])) {
    $gallery_id = (int)$_POST['gallery_id'];
}

$user_id = (int)$_SESSION['user_id'];

if (!$gallery_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid gallery ID']);
    exit;
}

// Prepared statements - SECURITY FIX
$check_stmt = mysqli_prepare($conn, "SELECT id FROM gallery_likes WHERE gallery_id = ? AND user_id = ?");
mysqli_stmt_bind_param($check_stmt, "ii", $gallery_id, $user_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($check_result) > 0) {
    // Unlike
    $delete_stmt = mysqli_prepare($conn, "DELETE FROM gallery_likes WHERE gallery_id = ? AND user_id = ?");
    mysqli_stmt_bind_param($delete_stmt, "ii", $gallery_id, $user_id);
    mysqli_stmt_execute($delete_stmt);
    $liked = false;
} else {
    // Like
    $insert_stmt = mysqli_prepare($conn, "INSERT INTO gallery_likes (gallery_id, user_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($insert_stmt, "ii", $gallery_id, $user_id);
    mysqli_stmt_execute($insert_stmt);
    $liked = true;
}

$count_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM gallery_likes WHERE gallery_id = ?");
mysqli_stmt_bind_param($count_stmt, "i", $gallery_id);
mysqli_stmt_execute($count_stmt);
$count_result = mysqli_stmt_get_result($count_stmt);
$count_row = mysqli_fetch_assoc($count_result);
$like_count = (int)$count_row['count'];

echo json_encode([
    'success' => true,
    'liked' => $liked,
    'like_count' => $like_count
]);

mysqli_close($conn);
?>

