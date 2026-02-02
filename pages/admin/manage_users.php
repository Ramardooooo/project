<?php
include '../../config/database.php';

if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: manage_users");
    exit();
}

include '../../layouts/admin/header.php';
include '../../layouts/admin/sidebar.php';

if ($_SESSION['role'] == 'admin') {
    $limit = 10;
    $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
    $offset = ($page - 1) * $limit;
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    $where_clause = $search ? "WHERE username LIKE '%$search%'" : '';
    $total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users $where_clause"))['total'];
    $total_pages = max(1, ceil($total_users / $limit));

    $users = mysqli_query($conn, "SELECT * FROM users $where_clause LIMIT $limit OFFSET $offset");
?>

<div class="ml-64 min-h-screen" style="background-image: url('https://images.unsplash.com/photo-1565102127622-df163cfbdaa4?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'); background-size: cover; background-position: center; background-attachment: fixed;">
<div class="p-8 backdrop-blur-sm">
<h1 class="text-2xl font-bold mb-6 text-white drop-shadow-lg">Manage Users</h1>

    <a href="/PROJECT/tambah_user" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mb-4 inline-block drop-shadow-sm">Tambah User</a>

    <form method="GET" class="mb-4">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari Username..." class="px-4 py-2 bg-white/20 backdrop-blur-md border border-white/30 rounded-lg text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/50 drop-shadow-sm">
        <button type="submit" class="ml-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 drop-shadow-sm">Cari</button>
    </form>

    <div class="bg-white/20 backdrop-blur-md rounded-2xl shadow-lg overflow-hidden border border-white/30">
        <table class="w-full">
            <thead class="bg-white/30">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-white drop-shadow-sm">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-white drop-shadow-sm">Username</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-white drop-shadow-sm">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-white drop-shadow-sm">Password</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-white drop-shadow-sm">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-white drop-shadow-sm">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/20">
                <?php while ($user = mysqli_fetch_assoc($users)) { ?>
                <tr class="hover:bg-white/10 transition-all duration-300">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white drop-shadow-sm"><?php echo $user['id']; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white/80 drop-shadow-sm"><?php echo $user['username']; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white/80 drop-shadow-sm"><?php echo $user['email']; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white/80 drop-shadow-sm"><?php echo $user['password']; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white/80 drop-shadow-sm"><?php echo $user['role']; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="/PROJECT/edit_user?id=<?php echo $user['id']; ?>" class="bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-green-600 transition duration-200 drop-shadow-sm">Edit</a>
                        <form method="POST" class="inline ml-2">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="delete_user" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-purple-600 transition duration-200 drop-shadow-sm" onclick="return confirm('Apakah anda yakin untuk menghapus?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex justify-center">
        <?php if ($total_pages > 0): ?>
            <div class="flex space-x-2">
                <?php if ($page > 1): ?>
                    <a href="/PROJECT/manage_users?p=<?= $page - 1 ?>" class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="/PROJECT/manage_users?p=<?= $i ?>" class="px-3 py-2 <?= $i == $page ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700' ?> rounded hover:bg-gray-300"><?= $i ?></a>
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
?>