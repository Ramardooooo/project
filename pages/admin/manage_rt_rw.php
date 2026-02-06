<?php
include '../../config/database.php';

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

include '../../layouts/admin/header.php';
include '../../layouts/admin/sidebar.php';

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

<div id="mainContent" class="ml-64 min-h-screen bg-blue-900 transition-all duration-300">
<div class="p-8">
    <a href="tambah_rt" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mb-4 inline-block drop-shadow-sm">Tambah RT</a>

    <form method="GET" class="mb-4">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari RT atau Ketua RT..." class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-800 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 drop-shadow-sm">
        <button type="submit" class="ml-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 drop-shadow-sm">Cari</button>
    </form>



    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php while ($r = mysqli_fetch_assoc($rt)) { ?>
        <div class="bg-white/20 backdrop-blur-md rounded-2xl shadow-lg p-6 border border-white/30 hover:shadow-2xl hover:bg-white/30 transition-all duration-300">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-lg">
                    <?php echo strtoupper(substr($r['nama_rt'], 0, 1)); ?>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-white drop-shadow-sm"><?php echo $r['nama_rt']; ?></h3>
                    <p class="text-sm text-white/70 drop-shadow-sm">Ketua RT: <?php echo $r['ketua_rt']; ?></p>
                </div>
            </div>
            <div class="mb-4">
                <p class="text-sm text-white/80 drop-shadow-sm"><strong>ID:</strong> <?php echo $r['id']; ?></p>
                <p class="text-sm text-white/80 drop-shadow-sm"><strong>Status:</strong>
                    <span class="px-2 py-1 rounded-full text-xs font-medium <?php echo ($r['status'] == 'aktif') ? 'bg-green-500 text-white' : 'bg-red-500 text-white'; ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $r['status'])); ?>
                    </span>
                </p>
                <p class="text-sm text-white/80 drop-shadow-sm"><strong>Dibuat:</strong> <?php echo isset($r['created_at']) ? date('d M Y', strtotime($r['created_at'])) : 'N/A'; ?></p>
            </div>
            <div class="flex gap-2">
                <a href="/PROJECT/edit_rt?id=<?php echo $r['id']; ?>" class="py-2 px-3 rounded-lg font-semibold text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:scale-105 transition-all duration-300 drop-shadow-sm text-center">Edit</a>
                <form method="POST" class="inline">
                    <input type="hidden" name="rt_id" value="<?php echo $r['id']; ?>">
                    <button type="submit" name="toggle_status" class="py-2 px-3 rounded-lg font-semibold text-white bg-gradient-to-r from-yellow-500 to-orange-500 hover:scale-105 transition-all duration-300 drop-shadow-sm" title="Toggle Status">
                        <?php echo ($r['status'] == 'aktif') ? 'Nonaktifkan' : 'Aktifkan'; ?>
                    </button>
                </form>
                <form method="POST" class="inline">
                    <input type="hidden" name="rt_id" value="<?php echo $r['id']; ?>">
                    <button type="submit" name="delete_rt" class="bg-red-500 text-white py-2 px-3 rounded-lg hover:bg-purple-600 transition duration-200 drop-shadow-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">Delete</button>
                </form>
            </div>
        </div>
        <?php } ?>
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