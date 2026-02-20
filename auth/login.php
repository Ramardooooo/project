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
        $error = "Masukkan username atau email.";
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

                if ($user['role'] == 'admin') {
                    header("Location: /PROJECT/dashboard_admin");
                } elseif ($user['role'] == 'ketua') {
                    header("Location: /PROJECT/dashboard_ketua");
                } else {
                    header("Location: ../../PROJECT/home");
                }
                exit();
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "User tidak ditemukan.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login - Lurahgo.id</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-100">

<div class="w-full max-w-md bg-white rounded-lg shadow-md p-8 border border-gray-200">

    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Lurahgo.id</h2>
        <p class="text-gray-500 mt-1">Silakan login untuk melanjutkan</p>
    </div>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-5">

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Username / Email</label>
            <input type="text" name="email" id="email" required
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password" id="password" required
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <div class="flex items-center">
            <input type="checkbox" id="show_password" onclick="togglePassword()" 
            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
            <label for="show_password" class="ml-2 text-sm text-gray-600">Tampilkan password</label>
        </div>

        <button type="submit"
        class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors duration-200">
            Login
        </button>

    </form>

    <div class="mt-6 text-center text-sm text-gray-500">
        <p>Belum punya akun? <a href="register.php" class="text-blue-600 hover:text-blue-800">Daftar di sini</a></p>
    </div>

</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
}
</script>

</body>
</html>
