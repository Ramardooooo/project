<?php
include 'config/database.php';

if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: manage_users.php");
    exit();
}

include 'layouts/admin/header.php';
include 'layouts/admin/sidebar.php';

if ($_SESSION['role'] == 'admin') {
    $limit = 10;
    $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
    $offset = ($page - 1) * $limit;

    $total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
    $total_pages = max(1, ceil($total_users / $limit));
    echo "Total users: $total_users, Total pages: $total_pages<br>";

    $users = mysqli_query($conn, "SELECT * FROM users LIMIT $limit OFFSET $offset");
?>

<div class="ml-64 p-6">
<h1 class="text-2xl font-bold mb-6">Manage Users</h1>

    <a href="/PROJECT/tambah_user" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mb-4 inline-block">Tambah User</a>

    <table class="w-full bg-white rounded-lg shadow-lg overflow-hidden">
        <thead class="bg-gradient-to-r from-blue-500 to-blue-600 text-white">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Username</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Password</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php while ($user = mysqli_fetch_assoc($users)) { ?>
            <tr class="hover:bg-gray-50 transition duration-200">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $user['id']; ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $user['username']; ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $user['email']; ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $user['password']; ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $user['role']; ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="/PROJECT/edit_user?id=<?php echo $user['id']; ?>" class="bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 transition duration-200">Edit</a>
                    <form method="POST" class="inline ml-2">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        <button type="submit" name="delete_user" class="bg-purple-500 text-white px-3 py-1 rounded-md hover:bg-purple-600 transition duration-200" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="mt-4 flex justify-center">
        <?php if ($total_pages > 0): ?>
            <div class="flex space-x-2">
                <?php if ($page > 1): ?>
                    <a href="/PROJECT/manage_users?p=<?= $page - 1 ?>" class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="/PROJECT/manage_users?p=<?= $i ?>" class="px-3 py-2 <?= $i == $page ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' ?> rounded hover:bg-gray-300"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="/PROJECT/manage_users?p=<?= $page + 1 ?>" class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
}

include 'layouts/admin/footer.php';
?>
