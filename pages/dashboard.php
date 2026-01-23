<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
include '../config/database.php';
include '../layouts/header.php';
include '../layouts/sidebar.php';

if ($_SESSION['role'] == 'admin') {
    $totalWarga = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM warga"))['total'];
    $totalKK = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kk"))['total'];
    $totalRT = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM rt"))['total'];
    $totalRW = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM rw"))['total'];
    $users = mysqli_query($conn, "SELECT * FROM users");

    if (isset($_POST['update_role'])) {
        $user_id = $_POST['user_id'];
        $new_role = $_POST['role'];
        mysqli_query($conn, "UPDATE users SET role='$new_role' WHERE id=$user_id");
        header("Location: dashboard.php");
        exit();
    }

    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        mysqli_query($conn, "DELETE FROM users WHERE id=$user_id");
        header("Location: dashboard.php");
        exit();
    }
?>

<div class="ml-64 p-6">
<h1 class="text-2xl font-bold mb-6">Dashboard Admin Lurahgo.id</h1>

  <div class="grid grid-cols-4 gap-6">
        <div class="bg-white p-4 rounded shadow border-l-4 border-yellow-500">
            <p class="text-gray-500">Total Warga</p>
            <h2 class="text-3xl font-bold text-yellow-600"><?= $totalWarga ?></h2>
        </div>
        <div class="bg-white p-4 rounded shadow border-l-4 border-yellow-500">
            <p class="text-gray-500">Total Kepala Keluarga</p>
            <h2 class="text-3xl font-bold text-yellow-600"><?= $totalKK ?></h2>
        </div>
        <div class="bg-white p-4 rounded shadow border-l-4 border-yellow-500">
            <p class="text-gray-500">Total RT</p>
            <h2 class="text-3xl font-bold text-yellow-600"><?= $totalRT ?></h2>
        </div>
        <div class="bg-white p-4 rounded shadow border-l-4 border-yellow-500">
            <p class="text-gray-500">Total RW</p>
            <h2 class="text-3xl font-bold text-yellow-600"><?= $totalRW ?></h2>
        </div>
    </div>

    <h2 class="text-xl font-bold mt-8 mb-4">Manage Users</h2>
    <a href="../auth/register.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mb-4 inline-block">Add New User</a>

    <table class="w-full bg-white rounded shadow">
        <thead class="bg-yellow-100">
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Username</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Role</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = mysqli_fetch_assoc($users)) { ?>
            <tr class="border-t">
                <td class="px-4 py-2"><?php echo $user['id']; ?></td>
                <td class="px-4 py-2"><?php echo $user['username']; ?></td>
                <td class="px-4 py-2"><?php echo $user['email']; ?></td>
                <td class="px-4 py-2"><?php echo $user['role']; ?></td>
                <td class="px-4 py-2">
                    <form method="POST" class="inline">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        <select name="role" class="border rounded px-2 py-1">
                            <option value="user" <?php if ($user['role'] == 'user') echo 'selected'; ?>>User</option>
                            <option value="ketua" <?php if ($user['role'] == 'ketua') echo 'selected'; ?>>Ketua</option>
                            <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                        </select>
                        <button type="submit" name="update_role" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Update</button>
                    </form>
                    <form method="POST" class="inline ml-2">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        <button type="submit" name="delete_user" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php
} else {
    // User dashboard
?>
<div class="ml-64 p-6 flex-grow">
<h1 class="text-2xl font-bold mb-6">Dashboard User</h1>
<p>Welcome to your dashboard!</p>
</div>
<?php
}
include '../layouts/footer.php';
?>
