<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'POST only']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');
$ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email']);
    exit;
}

$stmt = mysqli_prepare($conn, "INSERT IGNORE INTO subscribers (email, ip, user_agent) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'sss', $email, $ip, $user_agent);

if (mysqli_stmt_execute($stmt)) {
    $affected = mysqli_stmt_affected_rows($stmt);
    echo json_encode([
        'success' => true, 
        'message' => $affected > 0 ? 'Subscribed! Terima kasih' : 'Already subscribed',
        'email' => $email
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Subscription failed']);
}
?>

