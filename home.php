<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}
include 'config/database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home - Lurago.id</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #fef3c7; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-96 text-center">
        <h1 class="text-2xl font-bold mb-6">Welcome to Lurago.id</h1>
        <p class="mb-6">You are logged in as <?php echo $_SESSION['username']; ?> (<?php echo $_SESSION['role']; ?>)</p>
        <a href="pages/user/dashboard_user.php" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Dashboard</a>
        <a href="auth/logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 ml-4">Logout</a>
    </div>
</body>
</html>
