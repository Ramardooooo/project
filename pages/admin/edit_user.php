<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
include 'config/database.php';

$user_id = $_GET['id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id"));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    $update_sql = "UPDATE users SET username='$username', email='$email', role='$role'";
    if (!empty($password)) {
        $update_sql .= ", password='$password'";
    }
    $update_sql .= " WHERE id=$user_id";

    mysqli_query($conn, $update_sql);
    header("Location: dashboard_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User - Lurahgo.id</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #fef3c7; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Edit User</h2>
        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700">Username</label>
                <input type="text" name="username" value="<?php echo $user['username']; ?>" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" value="<?php echo $user['email']; ?>" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Role</label>
                <select name="role" class="w-full px-3 py-2 border rounded">
                    <option value="user" <?php if ($user['role'] == 'user') echo 'selected'; ?>>User</option>
                    <option value="ketua" <?php if ($user['role'] == 'ketua') echo 'selected'; ?>>Ketua</option>
                    <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-yellow-500 text-white py-2 rounded hover:bg-yellow-600">Update User</button>
        </form>
        <a href="dashboard_admin.php" class="block text-center mt-4 text-yellow-500">Back to Dashboard</a>
    </div>
</body>
</html>
