<?php
session_start();
include '../../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /PROJECT/home");
    exit();
}

if (isset($_POST['tambah_user'])) {
    // Trim and sanitize inputs
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Skip honeypot check - let empty honeypots pass (browsers don't always fill)
    /* Honeypot detection commented out to avoid false positives
    if (!empty($_POST['fake_username']) || !empty($_POST['fake_email']) || !empty($_POST['fake_password'])) {
        $error = "Invalid form submission.";
        goto show_form;
    }*/


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
                    header("Location: /PROJECT/manage_users");
                    exit();
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

show_form:
include '../../../layouts/admin/header.php';
include '../../../layouts/admin/sidebar.php';
?>



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah User - Lurahgo.id</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        // Clear form fields on load to prevent autofill
        window.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[method="POST"]');
            if (form) {
                const inputs = form.querySelectorAll('input[name="username"], input[name="email"], input[name="password"], input[name="confirm_password"]');
                inputs.forEach(input => {
                    if (input.value === '') return;
                    input.value = '';
                    input.focus();
                    input.blur();
                });
            }
        });
        
        // Prevent autofill on focus
        document.addEventListener('input', function(e) {
            if (e.target.matches('input[name="username"], input[name="email"], input[name="password"], input[name="confirm_password"]')) {
                e.target.setAttribute('autocomplete', 'off');
            }
        });
    </script>
</head>
<style>
    /* Additional anti-autofill styling */
    input:-webkit-autofill,
    input:-webkit-autofill:hover,
    input:-webkit-autofill:focus,
    input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 30px white inset !important;
        box-shadow: 0 0 0 30px white inset !important;
        -webkit-text-fill-color: #374151 !important;
        background-color: white !important;
        transition: background-color 5000s ease-in-out 0s;
    }
</style>
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

        <form method="POST" autocomplete="off">
            <!-- Autofill honeypot - invisible to users, attracts browser autofill -->
            <div style="position: absolute; opacity: 0; height: 0; overflow: hidden;">
                <input type="text" name="fake_username" tabindex="-1" autocomplete="username">
                <input type="email" name="fake_email" tabindex="-1" autocomplete="email">
                <input type="password" name="fake_password" tabindex="-1" autocomplete="current-password">
            </div>
            <div class="mb-3">
                <label class="block text-sm text-gray-700 mb-1">Username</label>
                <input type="text" name="username" autocomplete="new-username"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:border-green-500"
                    required>
            </div>

            <div class="mb-3">
                <label class="block text-sm text-gray-700 mb-1">Email</label>
                <input type="email" name="email" autocomplete="new-email"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:border-green-500"
                    required>
            </div>

            <div class="mb-3">
                <label class="block text-sm text-gray-700 mb-1">Password</label>
                <input type="password" name="password" autocomplete="new-password"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:border-green-500"
                    required>
            </div>

            <div class="mb-3">
                <label class="block text-sm text-gray-700 mb-1">Confirm Password</label>
                <input type="password" name="confirm_password" autocomplete="new-password"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:border-green-500"
                    required>
            </div>

            <div class="mb-5">
                <label class="block text-sm text-gray-700 mb-1">Role</label>
                <select name="role" autocomplete="off"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:border-green-500"
                    required>
                    <option value="user">User</option>
                    <option value="ketua">Ketua</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <button type="submit" name="tambah_user"
                class="w-full py-3 rounded-xl font-semibold text-white bg-gradient-to-r from-green-400 to-emerald-600 hover:scale-105 transition-all duration-300">
                <i class="fas fa-save mr-2"></i>Tambah User
            </button>
        </form>

        <a href="manage_users"
            class="block text-center mt-4 py-3 rounded-xl font-semibold text-gray-700 bg-gray-200 hover:bg-gray-300 hover:scale-105 transition-all duration-300">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Manage Users
        </a>
    </div>
</body>
</html>
