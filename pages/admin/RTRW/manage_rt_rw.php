<?php
include '../../../config/database.php';

if (isset($_POST['delete_rt'])) {
    $rt_id = $_POST['rt_id'];
    $rt_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rt WHERE id = $rt_id"));
    $stmt = mysqli_prepare($conn, "DELETE FROM rt WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $rt_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $action = "RT dihapus oleh " . $username;
    $table_name = "rt";
    $record_id = $rt_id;
    $old_value = json_encode($rt_data);
    $new_value = null;
    $user_id = $_SESSION['user_id'] ?? null;
    $username = $_SESSION['username'] ?? 'Unknown';
    $audit_stmt = mysqli_prepare($conn, "INSERT INTO audit_log (action, table_name, record_id, old_value, new_value, user_id, username) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($audit_stmt, "ssissis", $action, $table_name, $record_id, $old_value, $new_value, $user_id, $username);
    mysqli_stmt_execute($audit_stmt);
    mysqli_stmt_close($audit_stmt);

    header("Location: manage_rt_rw");
    exit();
}

if (isset($_POST['toggle_status'])) {
    $rt_id = $_POST['rt_id'];
    $current_status = mysqli_fetch_assoc(mysqli_query($conn, "SELECT status FROM rt WHERE id = $rt_id"))['status'];
    $new_status = ($current_status == 'aktif') ? 'tidak aktif' : 'aktif';
    $stmt = mysqli_prepare($conn, "UPDATE rt SET status=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "si", $new_status, $rt_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $action = "Status RT diubah oleh " . $username;
    $table_name = "rt";
    $record_id = $rt_id;
    $old_value = json_encode(['status' => $current_status]);
    $new_value = json_encode(['status' => $new_status]);
    $user_id = $_SESSION['user_id'] ?? null;
    $username = $_SESSION['username'] ?? 'Unknown';
    $audit_stmt = mysqli_prepare($conn, "INSERT INTO audit_log (action, table_name, record_id, old_value, new_value, user_id, username) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($audit_stmt, "ssissis", $action, $table_name, $record_id, $old_value, $new_value, $user_id, $username);
    mysqli_stmt_execute($audit_stmt);
    mysqli_stmt_close($audit_stmt);

    header("Location: manage_rt_rw");
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

    $where_clause = $search ? "WHERE nama_rt LIKE '%$search%' OR ketua_rt LIKE '%$search%'" : '';
    $total_rt = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM rt $where_clause"))['total'];
    $total_pages = max(1, ceil($total_rt / $limit));

    $rt = mysqli_query($conn, "SELECT * FROM rt $where_clause LIMIT $limit OFFSET $offset");
?>

<div id="mainContent" class="ml-64 min-h-screen bg-gradient-to-br from-white to-gray-50">
<div class="p-8">
    <a href="tambah_rt" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mb-4 inline-block drop-shadow-sm">Tambah RT</a>

    <h1 class="text-2xl font-bold mb-6 text-gray-800 drop-shadow-lg">Manage RT/RW</h1>

    <form method="GET" class="mb-4">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari RT atau Ketua RT..." class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-800 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 drop-shadow-sm">
        <button type="submit" class="ml-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 drop-shadow-sm">Cari</button>
    </form>



    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-200">
        <table class="w-full table-auto">
            <thead class="bg-gradient-to-r from-blue-500 to-purple-600 text-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">ID</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Nama RT</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Ketua RT</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Created At</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php while ($r = mysqli_fetch_assoc($rt)) { ?>
                <tr class="hover:bg-gradient-to-r hover:from-gray-50 hover:to-blue-50 transition-all duration-300 transform hover:shadow-md">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $r['id']; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-sm mr-3 shadow-lg">
                                <?php echo strtoupper(substr($r['nama_rt'], 0, 1)); ?>
                            </div>
                            <?php echo $r['nama_rt']; ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $r['ketua_rt']; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold shadow-md <?php echo ($r['status'] == 'aktif') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'bg-gradient-to-r from-red-400 to-red-600 text-white'; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $r['status'])); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo isset($r['created_at']) ? date('d M Y', strtotime($r['created_at'])) : 'N/A'; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="/PROJECT/edit_rt?id=<?php echo $r['id']; ?>" class="bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-600 transition-all duration-200 shadow-md hover:shadow-lg flex items-center">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                            <form method="POST" class="inline">
                                <input type="hidden" name="rt_id" value="<?php echo $r['id']; ?>">
                                <button type="submit" name="toggle_status" class="bg-yellow-500 text-white px-3 py-1 rounded-lg hover:bg-yellow-600 transition-all duration-200 shadow-md hover:shadow-lg flex items-center" title="Toggle Status">
                                    <i class="fas fa-toggle-on mr-1"></i> <?php echo ($r['status'] == 'aktif') ? 'Nonaktifkan' : 'Aktifkan'; ?>
                                </button>
                            </form>
                            <form method="POST" class="inline">
                                <input type="hidden" name="rt_id" value="<?php echo $r['id']; ?>">
                                <button type="submit" name="delete_rt" class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 transition-all duration-200 shadow-md hover:shadow-lg flex items-center" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">
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
                    <a href="/PROJECT/manage_rt_rw?p=<?= $page - 1 ?>" class="px-3 py-2 bg-white text-gray-800 rounded hover:bg-gray-100 drop-shadow-sm">Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="/PROJECT/manage_rt_rw?p=<?= $i ?>" class="px-3 py-2 <?= $i == $page ? 'bg-green-500 text-white' : 'bg-white text-gray-800' ?> rounded hover:bg-gray-100 drop-shadow-sm"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="/PROJECT/manage_rt_rw?p=<?= $page + 1 ?>" class="px-3 py-2 bg-white text-gray-800 rounded hover:bg-gray-100 drop-shadow-sm">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
}
?>
</div>
</div>
