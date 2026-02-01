<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
include 'config/database.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $check_sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "ss", $username, $email);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error = "Username atau email sudah ada.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $hashed_password, $role);

            if (mysqli_stmt_execute($stmt)) {
                $success = "User berhasil ditambahkan.";
            } else {
                $error = "Gagal menambahkan user: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($check_stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah User - Lurahgo.id</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #fef3c7; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Tambah User</h2>
        <?php if (isset($success)) echo "<p class='text-green-500 mb-4'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p class='text-red-500 mb-4'>$error</p>"; ?>
        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700">Username</label>
                <input type="text" name="username" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Confirm Password</label>
                <input type="password" name="confirm_password" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Role</label>
                <select name="role" class="w-full px-3 py-2 border rounded" required>
                    <option value="user">User</option>
                    <option value="ketua">Ketua</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-yellow-500 text-white py-2 rounded hover:bg-yellow-600">Tambah User</button>
        </form>
        <a href="manage_users.php" class="block text-center mt-4 text-yellow-500">Kembali ke Manage Users</a>
    </div>
</body>
</html>
