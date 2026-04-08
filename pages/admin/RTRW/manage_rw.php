<?php

include '../../../config/database.php';

if (isset($_POST['delete_rw'])) {
    $rw_id = $_POST['rw_id'];
    $rw_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rw WHERE id = $rw_id"));
    $stmt = mysqli_prepare($conn, "DELETE FROM rw WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $rw_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $action = "RW dihapus oleh " . $username;
    $table_name = "rw";
    $record_id = $rw_id;
    $old_value = json_encode($rw_data);
    $new_value = null;
    $user_id = $_SESSION['user_id'] ?? null;
    $username = $_SESSION['username'] ?? 'Unknown';
    $audit_stmt = mysqli_prepare($conn, "INSERT INTO audit_log (action, table_name, record_id, old_value, new_value, user_id, username) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($audit_stmt, "ssissis", $action, $table_name, $record_id, $old_value, $new_value, $user_id, $username);
    mysqli_stmt_execute($audit_stmt);
    mysqli_stmt_close($audit_stmt);

    header("Location: manage_rw");
    exit();
}

if (isset($_POST['toggle_status'])) {
    $rw_id = $_POST['rw_id'];
    $current_status = mysqli_fetch_assoc(mysqli_query($conn, "SELECT status FROM rw WHERE id = $rw_id"))['status'];
    $new_status = ($current_status == 'aktif') ? 'tidak aktif' : 'aktif';
    $stmt = mysqli_prepare($conn, "UPDATE rw SET status=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "si", $new_status, $rw_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $action = "Status RW diubah oleh " . $username;
    $table_name = "rw";
    $record_id = $rw_id;
    $old_value = json_encode(['status' => $current_status]);
    $new_value = json_encode(['status' => $new_status]);
    $user_id = $_SESSION['user_id'] ?? null;
    $username = $_SESSION['username'] ?? 'Unknown';
    $audit_stmt = mysqli_prepare($conn, "INSERT INTO audit_log (action, table_name, record_id, old_value, new_value, user_id, username) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($audit_stmt, "ssissis", $action, $table_name, $record_id, $old_value, $new_value, $user_id, $username);
    mysqli_stmt_execute($audit_stmt);
    mysqli_stmt_close($audit_stmt);

    header("Location: manage_rw");
    exit();
}

include '../../../layouts/admin/header.php';
include '../../../layouts/admin/sidebar.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: home");
    exit();
}

$limit = 9;
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $_GET['search'] : '';

$where_clause = $search ? "WHERE rw.name LIKE '%$search%' OR w.nama LIKE '%$search%' OR rt.nama_rt LIKE '%$search%'" : "";
$total_rw = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM (SELECT rw.id FROM rw LEFT JOIN rt ON rw.id = rt.id_rw LEFT JOIN warga w ON rt.id = w.rt $where_clause) as subquery"))['total'];
$total_pages = max(1, ceil($total_rw / $limit));

$rw_query = "SELECT rw.*, COUNT(DISTINCT rt.id) as jumlah_rt, COUNT(DISTINCT w.id) as jumlah_warga, w2.nama as ketua_nama, w2.nik FROM rw LEFT JOIN rt ON rw.id = rt.id_rw LEFT JOIN warga w ON rt.id = w.rt LEFT JOIN warga w2 ON rw.ketua_rw_id = w2.id $where_clause GROUP BY rw.id";
$rw = mysqli_query($conn, $rw_query . " LIMIT $limit OFFSET $offset");
?>

<div id="mainContent" class="ml-64 min-h-screen bg-gray-50">
<div class="p-8">
    <a href="tambah_rw" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mb-4 inline-block drop-shadow-sm">+ Tambah RW</a>
    <a href="manage_rt_rw" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mb-4 inline-block drop-shadow-sm ml-2">RT</a>

    <h1 class="text-2xl font-bold mb-6 text-gray-800 drop-shadow-lg">Kelola RW</h1>

    <form method="GET" class="mb-6">
        <div class="flex">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari nama RW atau ketua..." class="flex-1 px-4 py-2 bg-white border border-gray-300 rounded-l-lg text-gray-800 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 drop-shadow-sm">
            <button type="submit" class="px-6 py-2 bg-green-500 text-white rounded-r-lg hover:bg-green-600 drop-shadow-sm">Cari</button>
        </div>
    </form>

    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-200">
        <table class="w-full table-auto">
            <thead class="bg-slate-800 text-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">ID</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Nama RW</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Ketua RW</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Jumlah RT</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Jumlah Warga</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php while ($r = mysqli_fetch_assoc($rw)) { ?>
                <tr class="hover:bg-gray-100 transition-all duration-300 transform hover:shadow-md">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $r['id']; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div class="flex items-center">
                            <?php
                            $initial = isset($r['ketua_nama']) 
                                ? strtoupper(substr($r['ketua_nama'], 0, 1)) 
                                : strtoupper(substr($r['name'], 0, 1));
                            $photo_path = get_profile_photo_path($r['profile_photo'] ?? '');
                            $photo_src = $photo_path ?: '';
                            if ($photo_src): ?>
                                <img src="<?php echo htmlspecialchars($photo_src); ?>" 
                                onerror="this.onerror=null; this.src='/PROJECT/uploads/profiles/default-avatar.png';"
                                class="w-10 h-10 rounded-full object-cover shadow-lg mr-3 border-2 border-gray-200">
                            <?php else: ?>
                                <div class="w-10 h-10 rounded-full shadow-lg mr-3 border-2 border-gray-200 flex items-center justify-center bg-blue-600 text-white font-bold">
                                    <?php echo $initial; ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <div class="font-semibold"><?php echo htmlspecialchars($r['name']); ?></div>
                                <?php if (isset($r['ketua_nama']) && $r['ketua_nama']): ?>
                                    <div class="text-xs text-gray-500">Ketua: <?php echo htmlspecialchars($r['ketua_nama']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php echo isset($r['ketua_nama']) && $r['ketua_nama'] ? htmlspecialchars($r['ketua_nama']) : 'N/A'; ?>
                        <?php if (isset($r['nik']) && $r['nik']): ?>
                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars($r['nik']); ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo ($r['jumlah_rt'] ?? 0); ?> RT</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo ($r['jumlah_warga'] ?? 0); ?> orang</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold shadow-md <?php echo isset($r['status']) && $r['status'] == 'aktif' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'; ?>">
                            <?php echo isset($r['status']) && $r['status'] == 'aktif' ? 'Aktif' : 'Tidak Aktif'; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-1">
                            <a href="edit_rw?id=<?php echo $r['id']; ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            <form method="POST" class="inline" style="display:inline;">
                                <input type="hidden" name="rw_id" value="<?php echo $r['id']; ?>">
                                <button type="submit" name="toggle_status" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 text-xs flex items-center" title="Toggle Status">
                                    <i class="fas fa-toggle-on mr-1"></i><?php echo isset($r['status']) && $r['status'] == 'aktif' ? 'Nonaktifkan' : 'Aktifkan'; ?>
                                </button>
                            </form>
                            <form method="POST" class="inline" style="display:inline;">
                                <input type="hidden" name="rw_id" value="<?php echo $r['id']; ?>">
                                <button type="submit" name="delete_rw" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs flex items-center" onclick="return confirm('Hapus RW ini?')">
                                    <i class="fas fa-trash mr-1"></i>Delete
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
        <?php if ($total_pages > 1): ?>
            <div class="flex space-x-2 bg-white p-2 rounded-xl shadow-lg border">
                <?php if ($page > 1): ?>
                    <a href="?p=<?= $page - 1 ?>" class="px-3 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">‹ Previous</a>
                <?php endif; ?>

                <?php 
                $start = max(1, $page - 2);
                $end = min($total_pages, $page + 2);
                for ($i = $start; $i <= $end; $i++): ?>
                    <a href="?p=<?= $i ?>" class="px-3 py-2 <?= $i == $page ? 'bg-green-500 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' ?> rounded-lg transition"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?p=<?= $page + 1 ?>" class="px-3 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">Next ›</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
