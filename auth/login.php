<?php
session_start();
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_input = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($login_input)) {
        if (filter_var($login_input, FILTER_VALIDATE_EMAIL)) {
            $sql = "SELECT * FROM users WHERE email=?";
        } else {
            $sql = "SELECT * FROM users WHERE username=?";
        }
        $param = $login_input;
    } else {
        $error = "Please provide email or username.";
    }

    if (!isset($error)) {
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $param);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                if ($_SESSION['role'] == 'admin') {
                    header("Location: ../pages/admin/dashboard_admin.php");
                } elseif ($_SESSION['role'] == 'ketua') {
                    header("Location: ../pages/admin/dashboard_ketua.php");
                } else {
                    header("Location: ../home.php");
                }
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "User not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Amatgo.id</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #fef3c7; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Login to Lurago.id</h2>
        <?php if (isset($error)) echo "<p class='text-red-500 mb-4'>$error</p>"; ?>
        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700">Email or Username</label>
                <input type="text" name="email" class="w-full px-3 py-2 border rounded" placeholder="Enter email or username" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" class="w-full px-3 py-2 border rounded" required>
            </div>
            <button type="submit" class="w-full bg-yellow-500 text-white py-2 rounded hover:bg-yellow-600">Login</button>
        </form>
        <p class="mt-4 text-center">Gak punya akun? <a href="register.php" class="text-yellow-500">Register</a></p>
    </div>
</body>
</html>

