<?php
include '../../config/database.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id"));
    if (!$user) {
        header("Location: manage_users");
        exit();
    }
}

if (isset($_POST['update_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    // Check if username is already taken by another user
    $check_username = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? AND id != ?");
    mysqli_stmt_bind_param($check_username, "si", $username, $user_id);
    mysqli_stmt_execute($check_username);
    mysqli_stmt_store_result($check_username);
    if (mysqli_stmt_num_rows($check_username) > 0) {
        $error = "Username sudah digunakan oleh user lain.";
    } else {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "UPDATE users SET username=?, email=?, password=?, role=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "ssssi", $username, $email, $hashed_password, $role, $user_id);
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE users SET username=?, email=?, role=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $role, $user_id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $action = "User diedit oleh " . ($_SESSION['username'] ?? 'Unknown');
        $table_name = "users";
        $record_id = $user_id;
        $old_value = json_encode($user);
        $new_value = json_encode(['username' => $username, 'email' => $email, 'role' => $role]);
        $user_id_session = $_SESSION['user_id'] ?? null;
        $username_session = $_SESSION['username'] ?? 'Unknown';
        $audit_stmt = mysqli_prepare($conn, "INSERT INTO audit_log (action, table_name, record_id, old_value, new_value, user_id, username) VALUES (?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($audit_stmt, "ssissis", $action, $table_name, $record_id, $old_value, $new_value, $user_id_session, $username_session);
        mysqli_stmt_execute($audit_stmt);
        mysqli_stmt_close($audit_stmt);

        header("Location: manage_users");
        exit();
    }
    mysqli_stmt_close($check_username);
    
    $action = "User diedit oleh " . ($_SESSION['username'] ?? 'Unknown');
    $table_name = "users";
    $record_id = $user_id;
    $old_value = json_encode($user);
    $new_value = json_encode(['username' => $username, 'email' => $email, 'role' => $role]);
    $user_id_session = $_SESSION['user_id'] ?? null;
    $username_session = $_SESSION['username'] ?? 'Unknown';
    $audit_stmt = mysqli_prepare($conn, "INSERT INTO audit_log (action, table_name, record_id, old_value, new_value, user_id, username) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($audit_stmt, "ssissis", $action, $table_name, $record_id, $old_value, $new_value, $user_id_session, $username_session);
    mysqli_stmt_execute($audit_stmt);
    mysqli_stmt_close($audit_stmt);

    header("Location: manage_users");
    exit();
}

include '../../layouts/admin/header.php';
include '../../layouts/admin/sidebar.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit User - Lurahgo.id</title>
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="min-h-screen bg-blue-900">
<div class="ml-64 min-h-screen flex items-center justify-center p-8">
    <div class="bg-white/90 backdrop-blur-md rounded-2xl shadow-lg p-7 border border-white/20 hover:shadow-2xl hover:bg-white/95 transition-all duration-300 w-96">
        <h2 class="text-xl font-semibold mb-5 text-center text-gray-800">
            Edit User
        </h2>

        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-3"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="block text-sm text-gray-700 mb-1">Username</label>
                <input type="text" name="username" value="<?php echo $user['username']; ?>"
                    class="w-full px-3 py-2 border rounded
                           focus:outline-none focus:border-green-500"
                    required>
            </div>

            <div class="mb-3">
                <label class="block text-sm text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="<?php echo $user['email']; ?>"
                    class="w-full px-3 py-2 border rounded
                           focus:outline-none focus:border-green-500"
                    required>
            </div>

            <div class="mb-3">
                <label class="block text-sm text-gray-700 mb-1">Password</label>
                <input type="password" name="password"
                    class="w-full px-3 py-2 border rounded
                           focus:outline-none focus:border-green-500">
                <small class="text-gray-500 text-xs">
                    Kosongkan jika tidak ingin mengubah password
                </small>
            </div>

            <div class="mb-5">
                <label class="block text-sm text-gray-700 mb-1">Role</label>
                <select name="role"
                    class="w-full px-3 py-2 border rounded
                           focus:outline-none focus:border-green-500">
                    <option value="user" <?php if ($user['role'] == 'user') echo 'selected'; ?>>User</option>
                    <option value="ketua" <?php if ($user['role'] == 'ketua') echo 'selected'; ?>>Ketua</option>
                    <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                </select>
            </div>

            <button type="submit" name="update_user"
                class="w-full py-3 rounded-xl font-semibold text-white bg-gradient-to-r from-green-400 to-emerald-600 hover:scale-105 transition-all duration-300">
                Update User
            </button>
        </form>

        <a href="dashboard_admin"
            class="block text-center mt-4 text-sm text-green-600 hover:underline">
            Kembali ke Dashboard
        </a>
    </div>
</div>
</body>
</html>
