<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}
include '../../../config/database.php';

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

include '../../../layouts/admin/header.php';
include '../../../layouts/admin/sidebar.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: home");
    exit();
}

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

<div id="mainContent" class="ml-64 min-h-screen bg-gray-50">
<div class="p-8">
    <a href="/PROJECT/tambah_user" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mb-4 inline-block drop-shadow-sm">Tambah User</a>

    <h1 class="text-2xl font-bold mb-6 text-gray-800 drop-shadow-lg">Manage Users</h1>

    <form method="GET" class="mb-4">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari Username..." class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-800 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 drop-shadow-sm">
        <button type="submit" class="ml-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 drop-shadow-sm">Cari</button>
    </form>

    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-200">
        <table class="w-full table-auto">
            <thead class="bg-slate-800 text-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">ID</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Username</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Email</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Role</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Created At</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php while ($user = mysqli_fetch_assoc($users)) { ?>
                <tr class="hover:bg-gray-100 transition-all duration-300 transform hover:shadow-md">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $user['id']; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div class="flex items-center">
                            <?php if ($user['profile_photo']): ?>
                                <img src="../../<?php echo $user['profile_photo']; ?>" alt="Profile" class="w-10 h-10 rounded-full object-cover mr-3 shadow-lg">
                            <?php else: ?>
                                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-sm mr-3 shadow-lg">
                                    <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                            <?php echo $user['username']; ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $user['email']; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold shadow-md <?php echo ($user['role'] == 'admin') ? 'bg-purple-500 text-white' : 'bg-blue-500 text-white'; ?>">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo isset($user['created_at']) ? date('d M Y', strtotime($user['created_at'])) : 'N/A'; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="/PROJECT/edit_user?id=<?php echo $user['id']; ?>" class="bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-600 transition-all duration-200 shadow-md hover:shadow-lg flex items-center">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                            <form method="POST" class="inline">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="delete_user" class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 transition-all duration-200 shadow-md hover:shadow-lg flex items-center" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">
                                    <i class="fas fa-trash mr-1"></i> Delete
                                </button>
                            </form>
                        </div>
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