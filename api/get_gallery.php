<?php
header('Content-Type: application/json');
session_start();

include __DIR__ . '/../config/database.php';

$filter = $_GET['filter'] ?? 'all';
$user_id = $_SESSION['user_id'] ?? null;

$where = '';
$params = [];
$types = '';

if ($filter === 'newest') {
    $where = 'ORDER BY g.created_at DESC';
} elseif ($filter === 'popular') {
    $where = 'ORDER BY like_count DESC';
} elseif ($filter === 'category' && isset($_GET['cat'])) {
    $cat = $_GET['cat'];
    $where = 'WHERE g.category = ?';
    $params[] = $cat;
    $types .= 's';
}
// Add LIMIT for perf
$page = $_GET['page'] ?? 1;
$limit = 12;
$offset = ($page - 1) * $limit;
$where .= " LIMIT {$limit} OFFSET {$offset}";

$query = "SELECT g.*, 
          (SELECT image_path FROM gallery g2 WHERE g2.id = g.id LIMIT 1) as aspect_image_path,
          COUNT(l.id) as like_count,
          CASE WHEN EXISTS(SELECT 1 FROM gallery_likes gl WHERE gl.gallery_id = g.id AND gl.user_id = ?) THEN 1 ELSE 0 END as user_liked
          FROM gallery g 
          LEFT JOIN gallery_likes l ON g.id = l.gallery_id" . ($where ? ' ' . $where : '') . "
          GROUP BY g.id 
          ORDER BY g.created_at DESC";

$query = "SELECT g.*, 
          COUNT(l.id) as like_count,
          CASE WHEN EXISTS(SELECT 1 FROM gallery_likes gl WHERE gl.gallery_id = g.id AND gl.user_id = ?) THEN 1 ELSE 0 END as user_liked
          FROM gallery g 
          LEFT JOIN gallery_likes l ON g.id = l.gallery_id" . ($where ? ' ' . $where : '') . "
          GROUP BY g.id 
          ORDER BY g.created_at DESC";

$stmt = mysqli_prepare($conn, $query);
$bind_params = array_merge([$user_id], $params);
$types = 'i' . $types;
mysqli_stmt_bind_param($stmt, $types, ...$bind_params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$gallery_items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $gallery_items[] = $row;
}

echo json_encode(['success' => true, 'items' => $gallery_items]);
?>

