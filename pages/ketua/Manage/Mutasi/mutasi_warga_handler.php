<?php
if (isset($_POST['mutasi_datang'])) {
    $warga_id = (int)$_POST['warga_id'];
    if ($warga_id <= 0) {
        die("Warga tidak valid dipilih.");
    }
    $tanggal_mutasi = mysqli_real_escape_string($conn, $_POST['tanggal_mutasi']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    // Fetch nama_warga
    $nama_query = "SELECT nama FROM warga WHERE id = ?";
    $nama_stmt = mysqli_prepare($conn, $nama_query);
    mysqli_stmt_bind_param($nama_stmt, "i", $warga_id);
    mysqli_stmt_execute($nama_stmt);
    $nama_result = mysqli_stmt_get_result($nama_stmt);
    $nama_row = mysqli_fetch_assoc($nama_result);
    $nama_warga = $nama_row['nama'];

    $query = "UPDATE warga SET status='aktif', tanggal_mutasi=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $tanggal_mutasi, $warga_id);
    mysqli_stmt_execute($stmt);

    $mutasi_query = "INSERT INTO mutasi_warga (warga_id, jenis_mutasi, tanggal_mutasi, keterangan, nama_warga) VALUES (?, 'datang', ?, ?, ?)";
    $mutasi_stmt = mysqli_prepare($conn, $mutasi_query);
    mysqli_stmt_bind_param($mutasi_stmt, "isss", $warga_id, $tanggal_mutasi, $keterangan, $nama_warga);
    mysqli_stmt_execute($mutasi_stmt);
    
    // Log activity - mutasi datang (exclude from activity list as per user request)
    // Activity logging skipped for mutasi as per user request

    header("Location: mutasi_warga");
    exit();
}

if (isset($_POST['mutasi_pindah'])) {
    $warga_id = (int)$_POST['warga_id'];
    $tanggal_mutasi = mysqli_real_escape_string($conn, $_POST['tanggal_mutasi']);
    $alamat_tujuan = mysqli_real_escape_string($conn, $_POST['alamat_tujuan']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    // Fetch nama_warga
    $nama_query = "SELECT nama FROM warga WHERE id = ?";
    $nama_stmt = mysqli_prepare($conn, $nama_query);
    mysqli_stmt_bind_param($nama_stmt, "i", $warga_id);
    mysqli_stmt_execute($nama_stmt);
    $nama_result = mysqli_stmt_get_result($nama_stmt);
    $nama_row = mysqli_fetch_assoc($nama_result);
    $nama_warga = $nama_row['nama'];

    // Start transaction
    mysqli_begin_transaction($conn);

    $mutasi_query = "INSERT INTO mutasi_warga (warga_id, jenis_mutasi, tanggal_mutasi, alamat_tujuan, keterangan, nama_warga) VALUES (?, 'pindah', ?, ?, ?, ?)";
    $mutasi_stmt = mysqli_prepare($conn, $mutasi_query);
    mysqli_stmt_bind_param($mutasi_stmt, "issss", $warga_id, $tanggal_mutasi, $alamat_tujuan, $keterangan, $nama_warga);
    mysqli_stmt_execute($mutasi_stmt);

    // Disable foreign key checks to allow deletion
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");

    $query = "DELETE FROM warga WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $warga_id);
    mysqli_stmt_execute($stmt);

    // Re-enable foreign key checks
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");

    // Commit transaction
    mysqli_commit($conn);

    header("Location: mutasi_warga");
    exit();
}

if (isset($_POST['mutasi_meninggal'])) {
    $warga_id = (int)$_POST['warga_id'];
    $tanggal_mutasi = mysqli_real_escape_string($conn, $_POST['tanggal_mutasi']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    // Fetch nama_warga
    $nama_query = "SELECT nama FROM warga WHERE id = ?";
    $nama_stmt = mysqli_prepare($conn, $nama_query);
    mysqli_stmt_bind_param($nama_stmt, "i", $warga_id);
    mysqli_stmt_execute($nama_stmt);
    $nama_result = mysqli_stmt_get_result($nama_stmt);
    $nama_row = mysqli_fetch_assoc($nama_result);
    $nama_warga = $nama_row['nama'];

    $query = "UPDATE warga SET status='meninggal', tanggal_mutasi=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $tanggal_mutasi, $warga_id);
    mysqli_stmt_execute($stmt);

    $mutasi_query = "INSERT INTO mutasi_warga (warga_id, jenis_mutasi, tanggal_mutasi, keterangan, nama_warga) VALUES (?, 'meninggal', ?, ?, ?)";
    $mutasi_stmt = mysqli_prepare($conn, $mutasi_query);
    mysqli_stmt_bind_param($mutasi_stmt, "isss", $warga_id, $tanggal_mutasi, $keterangan, $nama_warga);
    mysqli_stmt_execute($mutasi_stmt);

    header("Location: mutasi_warga");
    exit();
}

$warga_query = "SELECT w.*, rt.nama_rt, rw.name as nama_rw FROM warga w
                LEFT JOIN rt ON w.rt = rt.id
                LEFT JOIN rw ON w.rw = rw.id
                WHERE w.status IS NULL OR w.status = 'aktif'
                ORDER BY w.nama";
$warga_result = mysqli_query($conn, $warga_query);

$search = $_GET['search'] ?? '';
$page = (int)($_GET['page'] ?? 1);
$limit = 10;
$offset = ($page - 1) * $limit;

// Fixed SQL syntax and use prepared statements
$count_query = "SELECT COUNT(*) as total FROM mutasi_warga m 
                LEFT JOIN warga w ON m.warga_id = w.id 
                LEFT JOIN rt ON w.rt = rt.id 
                LEFT JOIN rw ON w.rw = rw.id 
                WHERE 1=1";
$mutasi_base_query = "SELECT m.*, COALESCE(m.nama_warga, w.nama) as nama, w.nik, w.alamat, w.tanggal_lahir, w.jenis_kelamin, rt.nama_rt, rw.name as nama_rw
                 FROM mutasi_warga m
                 LEFT JOIN warga w ON m.warga_id = w.id
                 LEFT JOIN rt ON w.rt = rt.id
                 LEFT JOIN rw ON w.rw = rw.id 
                 WHERE 1=1";

$params = [];
$types = '';

if (!empty($search)) {
    $search_param = "%$search%";
    $count_query .= " AND (m.nama_warga LIKE ? OR w.nik LIKE ? OR m.keterangan LIKE ? OR m.jenis_mutasi LIKE ?)";
    $mutasi_base_query .= " AND (m.nama_warga LIKE ? OR w.nik LIKE ? OR m.keterangan LIKE ? OR m.jenis_mutasi LIKE ?)";
    $params = [$search_param, $search_param, $search_param, $search_param];
    $types = "ssss";
}

$count_stmt = mysqli_prepare($conn, $count_query);
if (!empty($params)) {
    mysqli_stmt_bind_param($count_stmt, $types, ...$params);
}
mysqli_stmt_execute($count_stmt);
$count_result = mysqli_stmt_get_result($count_stmt);
$total_row = mysqli_fetch_assoc($count_result);
$total_mutasi = $total_row['total'];
$total_pages = ceil($total_mutasi / $limit);

// Main query
$mutasi_query = $mutasi_base_query . " ORDER BY m.tanggal_mutasi DESC LIMIT ? OFFSET ?";
$params = array_merge($params, [$limit, $offset]);
$types .= "ii";

$mutasi_stmt = mysqli_prepare($conn, $mutasi_query);
mysqli_stmt_bind_param($mutasi_stmt, $types, ...$params);
mysqli_stmt_execute($mutasi_stmt);
$mutasi_result = mysqli_stmt_get_result($mutasi_stmt);
?>
