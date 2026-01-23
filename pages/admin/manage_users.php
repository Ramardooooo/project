<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
include '../../config/database.php';
include '../../layouts/admin/header.php';
include '../../layouts/admin/sidebar.php';

if ($_SESSION['role'] == 'admin') {
    $users = mysqli_query($conn, "SELECT * FROM users");

    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        mysqli_query($conn, "DELETE FROM users WHERE id=$user_id");
        header("Location: manage_users.php");
        exit();
    }
?>

<div class="ml-64 p-6">
<h1 class="text-2xl font-bold mb-6">Manage Users</h1>

    <a href="../auth/register.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mb-4 inline-block">Tambah User</a>

    <table class="w-full bg-white rounded shadow">
        <thead class="bg-yellow-100">
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Username</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Password</th>
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
                <td class="px-4 py-2"><?php echo $user['password']; ?></td>
                <td class="px-4 py-2"><?php echo $user['role']; ?></td>
                <td class="px-4 py-2">
                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Edit</a>
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
}
