<?php
include '../../config/database.php';
include '../../layouts/admin/header.php';
include '../../layouts/admin/sidebar.php';

if (isset($_POST['tambah_user'])) {
    echo "Post Berhasil!";
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    $check_username = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
    mysqli_stmt_bind_param($check_username, "s", $username);
    mysqli_stmt_execute($check_username);
    mysqli_stmt_store_result($check_username);
    if (mysqli_stmt_num_rows($check_username) > 0) {
        $error = "Username sudah ada.";
    } else {
        $check_email = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check_email, "s", $email);
        mysqli_stmt_execute($check_email);
        mysqli_stmt_store_result($check_email);
        if (mysqli_stmt_num_rows($check_email) > 0) {
            $error = "Email sudah ada.";
        } else {
            if ($password !== $confirm_password) {
                $error = "Password dan konfirmasi password tidak cocok.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $hashed_password, $role);
                if (mysqli_stmt_execute($stmt)) {
                    $user_id = mysqli_insert_id($conn);
                    $success = "User berhasil ditambahkan.";
                    $user_id_session = $_SESSION['user_id'] ?? null;
                    $username_session = $_SESSION['username'] ?? 'Unknown';
                    $action = "User baru ditambahkan oleh " . $username_session;
                    $table_name = "users";
                    $record_id = $user_id;
                    $old_value = null;
                    $new_value = json_encode(['username' => $username, 'email' => $email, 'role' => $role]);
                    $audit_stmt = mysqli_prepare($conn, "INSERT INTO audit_log (action, table_name, record_id, old_value, new_value, user_id, username) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    mysqli_stmt_bind_param($audit_stmt, "ssissis", $action, $table_name, $record_id, $old_value, $new_value, $user_id_session, $username_session);
                    mysqli_stmt_execute($audit_stmt);
                    mysqli_stmt_close($audit_stmt);
                } else {
                    $error = "Gagal menambahkan user: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            }
        }
        mysqli_stmt_close($check_email);
    }
    mysqli_stmt_close($check_username);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah User - Lurahgo.id</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-blue-900">
<div class="ml-64 min-h-screen flex items-center justify-center p-8">
    <div class="max-w-xl w-full bg-white/90 backdrop-blur-md rounded-2xl shadow-lg p-7 border border-white/20 hover:shadow-2xl hover:bg-white/95 transition-all duration-300">
        <h2 class="text-xl font-semibold mb-5 text-center text-green-700">
            Tambah User
        </h2>

        <?php if (isset($success)): ?>
            <p class="text-green-600 mb-3"><?php echo $success; ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-3"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="block text-sm text-gray-700 mb-1">Username</label>
                <input type="text" name="username"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:border-green-500"
                    required>
            </div>

            <div class="mb-3">
                <label class="block text-sm text-gray-700 mb-1">Email</label>
                <input type="email" name="email"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:border-green-500"
                    required>
            </div>

            <div class="mb-3">
                <label class="block text-sm text-gray-700 mb-1">Password</label>
                <input type="password" name="password"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:border-green-500"
                    required>
            </div>

            <div class="mb-3">
                <label class="block text-sm text-gray-700 mb-1">Confirm Password</label>
                <input type="password" name="confirm_password"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:border-green-500"
                    required>
            </div>

            <div class="mb-5">
                <label class="block text-sm text-gray-700 mb-1">Role</label>
                <select name="role"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:border-green-500"
                    required>
                    <option value="user">User</option>
                    <option value="ketua">Ketua</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <button type="submit" name="tambah_user"
                class="w-full py-3 rounded-xl font-semibold text-white bg-gradient-to-r from-green-400 to-emerald-600 hover:scale-105 transition-all duration-300">
                Tambah User
            </button>
        </form>

        <a href="manage_users"
            class="block text-center mt-4 text-sm text-green-600 hover:underline">
            Kembali ke Manage Users
        </a>
    </div>
</body>
</html>
