<?php
include '../../config/database.php';

if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $user_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT username, email, role FROM users WHERE id = $user_id"));
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $action = "Delete User";
    $table_name = "users";
    $record_id = $user_id;
    $old_value = json_encode($user_data);
    $new_value = null;
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

if ($_SESSION['role'] == 'admin') {
    $limit = 9;
    $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
    $offset = ($page - 1) * $limit;
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    $where_clause = $search ? "WHERE username LIKE '%$search%'" : '';
    $total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users $where_clause"))['total'];
    $total_pages = max(1, ceil($total_users / $limit));

    $users = mysqli_query($conn, "SELECT * FROM users $where_clause LIMIT $limit OFFSET $offset");
?>

<div class="ml-64 min-h-screen bg-blue-900">
<div class="p-8">
<h1 class="text-2xl font-bold mb-6 text-white drop-shadow-lg">Manage Users</h1>

    <a href="/PROJECT/tambah_user" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mb-4 inline-block drop-shadow-sm">Tambah User</a>

    <form method="GET" class="mb-4">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari Username..." class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-800 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 drop-shadow-sm">
        <button type="submit" class="ml-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 drop-shadow-sm">Cari</button>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php while ($user = mysqli_fetch_assoc($users)) { ?>
        <div class="bg-white/20 backdrop-blur-md rounded-2xl shadow-lg p-6 border border-white/30 hover:shadow-2xl hover:bg-white/30 transition-all duration-300">
            <div class="flex items-center mb-4">
                <?php if ($user['profile_photo']): ?>
                    <img src="../../<?php echo $user['profile_photo']; ?>" alt="Profile" class="w-12 h-12 rounded-full object-cover border-2 border-white/30">
                <?php else: ?>
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-lg">
                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-white drop-shadow-sm"><?php echo $user['username']; ?></h3>
                    <p class="text-sm text-white/70 drop-shadow-sm"><strong>Jabatannya: </strong><?php echo $user['role']; ?></p>
                </div>
            </div>
            <div class="mb-4">
                <p class="text-sm text-white/80 drop-shadow-sm"><strong>Email:</strong> <?php echo $user['email']; ?></p>
                <p class="text-sm text-white/80 drop-shadow-sm"><strong>ID Database:</strong> <?php echo $user['id']; ?></p>
                <p class="text-sm text-white/80 drop-shadow-sm"><strong>Dibuat:</strong> <?php echo isset($user['created_at']) ? date('d M Y', strtotime($user['created_at'])) : 'N/A'; ?></p>
            </div>
            <div class="flex gap-2">
                <a href="/PROJECT/edit_user?id=<?php echo $user['id']; ?>" class="flex-1 py-2 px-3 rounded-lg font-semibold text-white bg-gradient-to-r from-green-400 to-emerald-600 hover:scale-105 transition-all duration-300 drop-shadow-sm text-center">Edit</a>
                <form method="POST" class="flex-1">
                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                    <button type="submit" name="delete_user" class="w-full bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition duration-200 drop-shadow-sm" onclick="return confirm('Apakah anda yakin untuk menghapus?')">Delete</button>
                </form>
            </div>
        </div>
        <?php } ?>
    </div>

    <div class="mt-4 flex justify-center">
        <?php if ($total_pages > 0): ?>
            <div class="flex space-x-2">
                <?php if ($page > 1): ?>
                    <a href="/PROJECT/manage_users?p=<?= $page - 1 ?>" class="px-3 py-2 bg-white text-gray-800 rounded hover:bg-gray-100 drop-shadow-sm">Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="/PROJECT/manage_users?p=<?= $i ?>" class="px-3 py-2 <?= $i == $page ? 'bg-green-500 text-white' : 'bg-white text-gray-800' ?> rounded hover:bg-gray-100 drop-shadow-sm"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="/PROJECT/manage_users?p=<?= $page + 1 ?>" class="px-3 py-2 bg-white text-gray-800 rounded hover:bg-gray-100 drop-shadow-sm">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
}
?>