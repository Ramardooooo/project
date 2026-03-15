<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ketua') {
    header("Location: ../../../home.php");
    exit();
}

include '../../../config/database.php';

// Check if additional columns exist
$has_tempat_lahir = false;
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM warga LIKE 'tempat_lahir'");
if ($check_col && mysqli_num_rows($check_col) > 0) {
    $has_tempat_lahir = true;
}

$has_goldar = false;
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM warga LIKE 'goldar'");
if ($check_col && mysqli_num_rows($check_col) > 0) {
    $has_goldar = true;
}

$has_agama = false;
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM warga LIKE 'agama'");
if ($check_col && mysqli_num_rows($check_col) > 0) {
    $has_agama = true;
}

$has_status_kawin = false;
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM warga LIKE 'status_kawin'");
if ($check_col && mysqli_num_rows($check_col) > 0) {
    $has_status_kawin = true;
}

$has_status_approval = false;
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM warga LIKE 'status_approval'");
if ($check_col && mysqli_num_rows($check_col) > 0) {
    $has_status_approval = true;
}

// Handle Add - MUST be before any output
if (isset($_POST['add_warga'])) {
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $jk = mysqli_real_escape_string($conn, $_POST['jk']);
    $tanggal_lahir = !empty($_POST['tanggal_lahir']) ? mysqli_real_escape_string($conn, $_POST['tanggal_lahir']) : null;
    $pekerjaan = !empty($_POST['pekerjaan']) ? mysqli_real_escape_string($conn, $_POST['pekerjaan']) : null;
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $rt = (int)$_POST['rt'];
    $rw = (int)$_POST['rw'];
    $kk_id = !empty($_POST['kk_id']) ? (int)$_POST['kk_id'] : null;
    
    $tempat_lahir_val = !empty($_POST['tempat_lahir']) ? mysqli_real_escape_string($conn, $_POST['tempat_lahir']) : null;
    $goldar = !empty($_POST['goldar']) ? mysqli_real_escape_string($conn, $_POST['goldar']) : null;
    $agama = !empty($_POST['agama']) ? mysqli_real_escape_string($conn, $_POST['agama']) : null;
    $status_kawin = !empty($_POST['status_kawin']) ? mysqli_real_escape_string($conn, $_POST['status_kawin']) : null;
    
    $insert_fields = "nik, nama, jk, tanggal_lahir, pekerjaan, alamat, rt, rw, kk_id, status";
    $insert_values = "?, ?, ?, ?, ?, ?, ?, ?, ?, 'aktif'";
    $params = [$nik, $nama, $jk, $tanggal_lahir, $pekerjaan, $alamat, $rt, $rw, $kk_id];
    $types = "ssssssiii";
    
    if ($has_tempat_lahir && $tempat_lahir_val) {
        $insert_fields .= ", tempat_lahir";
        $insert_values .= ", ?";
        $params[] = $tempat_lahir_val;
        $types .= "s";
    }
    if ($has_goldar && $goldar) {
        $insert_fields .= ", goldar";
        $insert_values .= ", ?";
        $params[] = $goldar;
        $types .= "s";
    }
    if ($has_agama && $agama) {
        $insert_fields .= ", agama";
        $insert_values .= ", ?";
        $params[] = $agama;
        $types .= "s";
    }
    if ($has_status_kawin && $status_kawin) {
        $insert_fields .= ", status_kawin";
        $insert_values .= ", ?";
        $params[] = $status_kawin;
        $types .= "s";
    }
    if ($has_status_approval) {
        $insert_fields .= ", status_approval";
        $insert_values .= ", 'diterima'";
    }
    
    $sql = "INSERT INTO warga ($insert_fields) VALUES ($insert_values)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    
    // Log activity
    $user_id = $_SESSION['user_id'] ?? null;
    mysqli_query($conn, "INSERT INTO activities (action, entity, description, user_id) VALUES ('add', 'warga', 'Warga baru ditambahkan: $nama', $user_id)");
    
    header("Location: manage_warga");
    exit();
}

// Handle Edit - MUST be before any output
if (isset($_POST['edit_warga'])) {
    $id = (int)$_POST['id'];
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $jk = mysqli_real_escape_string($conn, $_POST['jk']);
    $tanggal_lahir = !empty($_POST['tanggal_lahir']) ? mysqli_real_escape_string($conn, $_POST['tanggal_lahir']) : null;
    $pekerjaan = !empty($_POST['pekerjaan']) ? mysqli_real_escape_string($conn, $_POST['pekerjaan']) : null;
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $rt = (int)$_POST['rt'];
    $rw = (int)$_POST['rw'];
    $kk_id = !empty($_POST['kk_id']) ? (int)$_POST['kk_id'] : null;
    
    $tempat_lahir_val = !empty($_POST['tempat_lahir']) ? mysqli_real_escape_string($conn, $_POST['tempat_lahir']) : null;
    $goldar = !empty($_POST['goldar']) ? mysqli_real_escape_string($conn, $_POST['goldar']) : null;
    $agama = !empty($_POST['agama']) ? mysqli_real_escape_string($conn, $_POST['agama']) : null;
    $status_kawin = !empty($_POST['status_kawin']) ? mysqli_real_escape_string($conn, $_POST['status_kawin']) : null;
    
    $update_fields = "nik=?, nama=?, jk=?, tanggal_lahir=?, pekerjaan=?, alamat=?, rt=?, rw=?, kk_id=?";
    $params = [$nik, $nama, $jk, $tanggal_lahir, $pekerjaan, $alamat, $rt, $rw, $kk_id, $id];
    $types = "ssssssiiii";
    
    if ($has_tempat_lahir && $tempat_lahir_val) {
        $update_fields .= ", tempat_lahir=?";
        $params[] = $tempat_lahir_val;
        $types .= "s";
    }
    if ($has_goldar && $goldar) {
        $update_fields .= ", goldar=?";
        $params[] = $goldar;
        $types .= "s";
    }
    if ($has_agama && $agama) {
        $update_fields .= ", agama=?";
        $params[] = $agama;
        $types .= "s";
    }
    if ($has_status_kawin && $status_kawin) {
        $update_fields .= ", status_kawin=?";
        $params[] = $status_kawin;
        $types .= "s";
    }
    
    $sql = "UPDATE warga SET $update_fields WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    
    // Log activity
    $user_id = $_SESSION['user_id'] ?? null;
    mysqli_query($conn, "INSERT INTO activities (action, entity, description, user_id) VALUES ('edit', 'warga', 'Data warga diperbarui: $nama', $user_id)");
    
    header("Location: manage_warga");
    exit();
}


// Handle Approve - MUST be before any output
if (isset($_POST['approve_warga'])) {
    $warga_id = (int)$_POST['id'];
    
    $warga_result = mysqli_query($conn, "SELECT nama FROM warga WHERE id = $warga_id");
    $warga_data = mysqli_fetch_assoc($warga_result);
    $warga_nama = $warga_data['nama'] ?? 'Unknown';
    
    $stmt = mysqli_prepare($conn, "UPDATE warga SET status_approval = 'diterima' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $warga_id);
    mysqli_stmt_execute($stmt);
    
    // Send notification
    $user_result = mysqli_query($conn, "SELECT id FROM users WHERE username = '" . mysqli_real_escape_string($conn, $warga_nama) . "'");
    if ($user_row = mysqli_fetch_assoc($user_result)) {
        $title = 'Data Diri Anda Disetujui';
        $message_notif = 'Data diri Anda telah disetujui oleh Ketua RT';
        mysqli_query($conn, "INSERT INTO notifications (title, message, type, role, user_id) VALUES ('$title', '$message_notif', 'approval', 'user', " . $user_row['id'] . ")");
    }
    
    // Log activity
    $user_id = $_SESSION['user_id'] ?? null;
    mysqli_query($conn, "INSERT INTO activities (action, entity, description, user_id) VALUES ('approve', 'warga', 'Approval edit warga diterima: $warga_nama', $user_id)");
    
    header("Location: manage_warga");
    exit();
}

// Handle Reject - MUST be before any output
if (isset($_POST['reject_warga'])) {
    $warga_id = (int)$_POST['id'];
    
    $warga_result = mysqli_query($conn, "SELECT nama FROM warga WHERE id = $warga_id");
    $warga_data = mysqli_fetch_assoc($warga_result);
    $warga_nama = $warga_data['nama'] ?? 'Unknown';
    
    $stmt = mysqli_prepare($conn, "UPDATE warga SET status_approval = 'ditolak' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $warga_id);
    mysqli_stmt_execute($stmt);
    
    // Send notification
    $user_result = mysqli_query($conn, "SELECT id FROM users WHERE username = '" . mysqli_real_escape_string($conn, $warga_nama) . "'");
    if ($user_row = mysqli_fetch_assoc($user_result)) {
        $title = 'Data Diri Anda Ditolak';
        $message_notif = 'Data diri Anda ditolak oleh Ketua RT. Silakan perbaiki dan ajukan kembali.';
        mysqli_query($conn, "INSERT INTO notifications (title, message, type, role, user_id) VALUES ('$title', '$message_notif', 'approval', 'user', " . $user_row['id'] . ")");
    }
    
    // Log activity
    $user_id = $_SESSION['user_id'] ?? null;
    mysqli_query($conn, "INSERT INTO activities (action, entity, description, user_id) VALUES ('reject', 'warga', 'Approval edit warga ditolak: $warga_nama', $user_id)");
    
    header("Location: manage_warga");
    exit();
}

// Handle Delete - MUST be before any output
if (isset($_POST['delete_warga'])) {
    $warga_id = (int)$_POST['id'];
    
    $warga_result = mysqli_query($conn, "SELECT nama FROM warga WHERE id = $warga_id");
    $warga_data = mysqli_fetch_assoc($warga_result);
    $warga_nama = $warga_data['nama'] ?? 'Unknown';
    
    mysqli_query($conn, "DELETE FROM mutasi_warga WHERE warga_id = $warga_id");
    $stmt = mysqli_prepare($conn, "DELETE FROM warga WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $warga_id);
    mysqli_stmt_execute($stmt);
    
    // Log activity
    $user_id = $_SESSION['user_id'] ?? null;
    mysqli_query($conn, "INSERT INTO activities (action, entity, description, user_id) VALUES ('delete', 'warga', 'Warga dihapus: $warga_nama', $user_id)");
    
    header("Location: manage_warga");
    exit();
}


/* Legacy PDF export removed - use EXPORT_WARGA.php buttons */
 // Now include the layouts (after handling redirects)
include '../../../layouts/ketua/header.php';
include '../../../layouts/ketua/sidebar.php';


$search = $_GET['search'] ?? '';
$page = (int)($_GET['page'] ?? 1);
$limit = 10;
$offset = ($page - 1) * $limit;

// Query for warga list
$select_fields = "w.id, w.nik, w.nama, w.jk, w.tanggal_lahir, w.pekerjaan, w.alamat, w.rt, w.rw, w.kk_id, w.status";
if ($has_tempat_lahir) $select_fields .= ", w.tempat_lahir";
if ($has_goldar) $select_fields .= ", w.goldar";
if ($has_agama) $select_fields .= ", w.agama";
if ($has_status_kawin) $select_fields .= ", w.status_kawin";
if ($has_status_approval) $select_fields .= ", w.status_approval";

$query = "SELECT $select_fields, rt.nama_rt, rw.name as nama_rw, kk.no_kk, kk.kepala_keluaraga 
          FROM warga w 
          LEFT JOIN rt ON w.rt = rt.id 
          LEFT JOIN rw ON w.rw = rw.id 
          LEFT JOIN kk ON w.kk_id = kk.id 
          WHERE 1=1";

$params = [];
$types = '';

if (!empty($search)) {
    $query .= " AND (w.nik LIKE ? OR w.nama LIKE ? OR w.alamat LIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}

$query .= " ORDER BY w.id DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt = mysqli_prepare($conn, $query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$warga_result = mysqli_stmt_get_result($stmt);

// Count query
$count_query = "SELECT COUNT(*) as total FROM warga";
$count_stmt = mysqli_prepare($conn, $count_query);
mysqli_stmt_execute($count_stmt);
$count_result = mysqli_stmt_get_result($count_stmt);
$total_row = mysqli_fetch_assoc($count_result);
$total = $total_row['total'];
$total_pages = ceil($total / $limit);

$rt_result = mysqli_query($conn, "SELECT id, nama_rt FROM rt");
$rw_result = mysqli_query($conn, "SELECT id, name FROM rw");
$kk_result = mysqli_query($conn, "SELECT id, no_kk, kepala_keluaraga FROM kk");
?>

<div id="mainContent" class="ml-64 p-8 bg-white min-h-screen transition-all duration-300">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Manajemen Data Warga</h1>
        <div class="flex gap-3">
            <div class="flex gap-2">
                <a href="\PROJECT\pages\ketua/export_warga.php?format.pdf<?php echo $_SERVER['QUERY_STRING']; ?>" class="bg-green-500 hover:bg-green-600 text-white px-5 py-2 rounded-lg flex items-center text-sm">
                    <i class="fas fa-file-pdf mr-1"></i>PDF Export
                </a>
            </div>
            <button onclick="openAddModal()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                <i class="fas fa-plus mr-2"></i>Tambah Warga
            </button>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-6">
        <form method="GET" class="flex gap-4">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari nama, NIK, atau alamat..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button type="submit" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
        </form>
    </div>

    <!-- Warga Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">NIK</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">JK</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tgl Lahir</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tempat Lahir</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Gol. Darah</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Agama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status Kawin</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pekerjaan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Alamat</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">RT/RW</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">KK</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Approval</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>

                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php while ($warga = mysqli_fetch_assoc($warga_result)): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($warga['nik'] ?? ''); ?></td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($warga['nama'] ?? ''); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo ($warga['jk'] ?? '') == 'L' ? 'L' : 'P'; ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo $warga['tanggal_lahir'] ? date('d-m-Y', strtotime($warga['tanggal_lahir'])) : '-'; ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars($warga['tempat_lahir'] ?? '-'); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars($warga['goldar'] ?? '-'); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars($warga['agama'] ?? '-'); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars($warga['status_kawin'] ?? '-'); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars($warga['pekerjaan'] ?? '-'); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars(substr($warga['alamat'] ?? '-', 0, 30) . (strlen($warga['alamat'] ?? '') > 30 ? '...' : '')); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars(($warga['nama_rt'] ?? '-') . '/' . ($warga['nama_rw'] ?? '-')); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <?php if (isset($warga['kk_id']) && $warga['kk_id'] && isset($warga['no_kk'])): ?>
                                <span class="text-blue-600"><?php echo htmlspecialchars($warga['no_kk']); ?></span>
                            <?php else: ?>
                                <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <?php 
                            $status = $warga['status'] ?? 'aktif';
                            $status_class = '';
                            if($status == 'aktif') $status_class = 'bg-green-100 text-green-800';
                            elseif($status == 'tidak_aktif') $status_class = 'bg-yellow-100 text-yellow-800';
                            elseif($status == 'meninggal') $status_class = 'bg-gray-100 text-gray-800';
                            elseif($status == 'pindah') $status_class = 'bg-red-100 text-red-800';
                            ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $status_class; ?>">
                                <?php echo ucfirst($status); ?>
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <?php if ($has_status_approval && isset($warga['status_approval'])): ?>
                                <?php 
                                $app_status = $warga['status_approval'];
                                $app_class = $app_status === 'menunggu' ? 'bg-yellow-100 text-yellow-800' : ($app_status === 'diterima' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $app_class; ?>">
                                    <?php echo ucfirst($app_status); ?>
                                </span>
                                <?php if ($app_status === 'menunggu'): ?>
                                    <form method="POST" class="inline ml-2" onsubmit="return confirm('Yakin terima edit ini?')">
                                        <input type="hidden" name="id" value="<?php echo $warga['id']; ?>">
                                        <button type="submit" name="approve_warga" class="text-green-600 hover:text-green-800 text-xs">
                                            <i class="fas fa-check mr-1"></i>Terima
                                        </button>
                                    </form>
                                    <form method="POST" class="inline ml-1" onsubmit="return confirm('Yakin tolak edit ini?')">
                                        <input type="hidden" name="id" value="<?php echo $warga['id']; ?>">
                                        <button type="submit" name="reject_warga" class="text-red-600 hover:text-red-800 text-xs">
                                            <i class="fas fa-times mr-1"></i>Tolak
                                        </button>
                                    </form>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <button onclick="openEditModal(<?php echo $warga['id']; ?>, '<?php echo addslashes($warga['nik'] ?? ''); ?>', '<?php echo addslashes($warga['nama'] ?? ''); ?>', '<?php echo $warga['jk'] ?? 'L'; ?>', '<?php echo addslashes($warga['alamat'] ?? ''); ?>', '<?php echo addslashes($warga['tempat_lahir'] ?? ''); ?>', '<?php echo addslashes($warga['goldar'] ?? ''); ?>', '<?php echo addslashes($warga['agama'] ?? ''); ?>', '<?php echo addslashes($warga['status_kawin'] ?? ''); ?>', '<?php echo addslashes($warga['pekerjaan'] ?? ''); ?>', '<?php echo $warga['tanggal_lahir'] ?? ''; ?>', <?php echo $warga['rt'] ?? 'null'; ?>, <?php echo $warga['rw'] ?? 'null'; ?>)" class="text-blue-600 hover:text-blue-800 mr-3">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>

                            <form method="POST" class="inline" onsubmit="return confirm('Yakin hapus warga ini?')">
                                <input type="hidden" name="id" value="<?php echo $warga['id']; ?>">
                                <button type="submit" name="delete_warga" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash mr-1"></i>Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="mt-6 flex justify-center">
        <nav class="flex gap-2">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" class="px-4 py-2 border <?php echo $i == $page ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'; ?> rounded">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </nav>
    </div>
    <?php endif; ?>
</div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Tambah Warga Baru</h3>
            <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">NIK</label>
                    <input type="text" name="nik" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" name="nama" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                    <select name="jk" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Golongan Darah</label>
                    <select name="goldar" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Pilih</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                        <option value="O">O</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Agama</label>
                    <select name="agama" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Pilih</option>
                        <option value="Islam">Islam</option>
                        <option value="Kristen">Kristen</option>
                        <option value="Katolik">Katolik</option>
                        <option value="Hindu">Hindu</option>
                        <option value="Budha">Budha</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status Perkawinan</label>
                    <select name="status_kawin" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Pilih</option>
                        <option value="Belum Kawin">Belum Kawin</option>
                        <option value="Kawin">Kawin</option>
                        <option value="Cerai Hidup">Cerai Hidup</option>
                        <option value="Cerai Mati">Cerai Mati</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Pekerjaan</label>
                    <input type="text" name="pekerjaan" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea name="alamat" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">RT</label>
                    <select name="rt" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        <?php mysqli_data_seek($rt_result, 0); while ($rt = mysqli_fetch_assoc($rt_result)): ?>
                            <option value="<?php echo $rt['id']; ?>"><?php echo $rt['nama_rt']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">RW</label>
                    <select name="rw" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        <?php mysqli_data_seek($rw_result, 0); while ($rw = mysqli_fetch_assoc($rw_result)): ?>
                            <option value="<?php echo $rw['id']; ?>"><?php echo $rw['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Kartu Keluarga (Opsional)</label>
                    <select name="kk_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Pilih KK</option>
                        <?php mysqli_data_seek($kk_result, 0); while ($kk = mysqli_fetch_assoc($kk_result)): ?>
                            <option value="<?php echo $kk['id']; ?>"><?php echo $kk['kepala_keluaraga']; ?> (<?php echo $kk['no_kk']; ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Batal</button>
                <button type="submit" name="add_warga" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Edit Warga</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="id" id="edit_id">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">NIK</label>
                    <input type="text" name="nik" id="edit_nik" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" name="nama" id="edit_nama" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                    <select name="jk" id="edit_jk" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" id="edit_tempat_lahir" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Golongan Darah</label>
                    <select name="goldar" id="edit_goldar" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Pilih</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                        <option value="O">O</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Agama</label>
                    <select name="agama" id="edit_agama" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Pilih</option>
                        <option value="Islam">Islam</option>
                        <option value="Kristen">Kristen</option>
                        <option value="Katolik">Katolik</option>
                        <option value="Hindu">Hindu</option>
                        <option value="Budha">Budha</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status Perkawinan</label>
                    <select name="status_kawin" id="edit_status_kawin" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Pilih</option>
                        <option value="Belum Kawin">Belum Kawin</option>
                        <option value="Kawin">Kawin</option>
                        <option value="Cerai Hidup">Cerai Hidup</option>
                        <option value="Cerai Mati">Cerai Mati</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Pekerjaan</label>
                    <input type="text" name="pekerjaan" id="edit_pekerjaan" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea name="alamat" id="edit_alamat" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">RT</label>
                    <select name="rt" id="edit_rt" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        <?php mysqli_data_seek($rt_result, 0); while ($rt = mysqli_fetch_assoc($rt_result)): ?>
                            <option value="<?php echo $rt['id']; ?>"><?php echo $rt['nama_rt']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">RW</label>
                    <select name="rw" id="edit_rw" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        <?php mysqli_data_seek($rw_result, 0); while ($rw = mysqli_fetch_assoc($rw_result)): ?>
                            <option value="<?php echo $rw['id']; ?>"><?php echo $rw['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Batal</button>
                <button type="submit" name="edit_warga" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() { document.getElementById('addModal').classList.remove('hidden'); }
function closeAddModal() { document.getElementById('addModal').classList.add('hidden'); }
function openEditModal(id, nik, nama, jk, alamat, tempat_lahir, goldar, agama, status_kawin, pekerjaan, tanggal_lahir, rt, rw) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nik').value = nik || '';
    document.getElementById('edit_nama').value = nama || '';
    document.getElementById('edit_jk').value = jk || 'L';
    document.getElementById('edit_alamat').value = alamat || '';
    document.getElementById('edit_tempat_lahir').value = tempat_lahir || '';
    document.getElementById('edit_goldar').value = goldar || '';
    document.getElementById('edit_agama').value = agama || '';
    document.getElementById('edit_status_kawin').value = status_kawin || '';
    document.getElementById('edit_pekerjaan').value = pekerjaan || '';
    document.getElementById('edit_tanggal_lahir').value = tanggal_lahir || '';
    if (rt) document.getElementById('edit_rt').value = rt;
    if (rw) document.getElementById('edit_rw').value = rw;
    document.getElementById('editModal').classList.remove('hidden');
}
function closeEditModal() { document.getElementById('editModal').classList.add('hidden'); }
</script>

