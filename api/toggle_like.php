<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

include '../config/database.php';

$input = json_decode(file_get_contents('php://input'), true);
$gallery_id = (int)$input['gallery_id'];
$user_id = $_SESSION['user_id'];

if (!$gallery_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid gallery ID']);
    exit;
}

// Check if user already liked this gallery item
$check_query = "SELECT id FROM gallery_likes WHERE gallery_id = $gallery_id AND user_id = $user_id";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) > 0) {
    // User already liked, so unlike
    $delete_query = "DELETE FROM gallery_likes WHERE gallery_id = $gallery_id AND user_id = $user_id";
    mysqli_query($conn, $delete_query);
    $liked = false;
} else {
    // User hasn't liked, so like
    $insert_query = "INSERT INTO gallery_likes (gallery_id, user_id) VALUES ($gallery_id, $user_id)";
    mysqli_query($conn, $insert_query);
    $liked = true;
}

// Get updated like count
$count_query = "SELECT COUNT(*) as count FROM gallery_likes WHERE gallery_id = $gallery_id";
$count_result = mysqli_query($conn, $count_query);
$count = mysqli_fetch_assoc($count_result)['count'];

echo json_encode([
    'success' => true,
    'liked' => $liked,
    'like_count' => $count
]);

mysqli_close($conn);
?>
