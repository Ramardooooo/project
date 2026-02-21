<?php
// Session already started in common.php
if (ob_get_level() === 0) {
    ob_start();
}

$search = $_GET['search'] ?? '';
$page = (int)($_GET['page'] ?? 1);
$limit = 10;
$offset = ($page - 1) * $limit;

// Check if status_approval column exists
$has_status_approval = false;
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM warga LIKE 'status_approval'");
if ($check_col && mysqli_num_rows($check_col) > 0) {
    $has_status_approval = true;
}

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

// Handle Accept/Deny actions
if (isset($_POST['approve_warga']) && $has_status_approval) {
    $warga_id = (int)$_POST['warga_id'];
    $stmt = mysqli_prepare($conn, "UPDATE warga SET status_approval = 'diterima' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $warga_id);
    mysqli_stmt_execute($stmt);
    header("Location: manage_warga.php");
    exit();
}

if (isset($_POST['reject_warga']) && $has_status_approval) {
    $warga_id = (int)$_POST['warga_id'];
    $stmt = mysqli_prepare($conn, "UPDATE warga SET status_approval = 'ditolak' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $warga_id);
    mysqli_stmt_execute($stmt);
    header("Location: manage_warga.php");
    exit();
}

// Handle Delete
if (isset($_POST['delete_warga'])) {
    $warga_id = (int)$_POST['id'];
    $stmt = mysqli_prepare($conn, "DELETE FROM warga WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $warga_id);
    mysqli_stmt_execute($stmt);
    header("Location: manage_warga.php");
    exit();
}

// Handle Add
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
    
    // Additional fields
    $tempat_lahir = !empty($_POST['tempat_lahir']) ? mysqli_real_escape_string($conn, $_POST['tempat_lahir']) : null;
    $goldar = !empty($_POST['goldar']) ? mysqli_real_escape_string($conn, $_POST['goldar']) : null;
    $agama = !empty($_POST['agama']) ? mysqli_real_escape_string($conn, $_POST['agama']) : null;
    $status_kawin = !empty($_POST['status_kawin']) ? mysqli_real_escape_string($conn, $_POST['status_kawin']) : null;
    
    // Build dynamic insert query
    $insert_fields = "nik, nama, jk, tanggal_lahir, pekerjaan, alamat, rt, rw, kk_id, status";
    $insert_values = "?, ?, ?, ?, ?, ?, ?, ?, ?, 'aktif'";
    $params = [$nik, $nama, $jk, $tanggal_lahir, $pekerjaan, $alamat, $rt, $rw, $kk_id];
    $types = "ssssssiii";
    
    if ($has_tempat_lahir) {
        $insert_fields .= ", tempat_lahir";
        $insert_values .= ", ?";
        $params[] = $tempat_lahir;
        $types .= "s";
    }
    if ($has_goldar) {
        $insert_fields .= ", goldar";
        $insert_values .= ", ?";
        $params[] = $goldar;
        $types .= "s";
    }
    if ($has_agama) {
        $insert_fields .= ", agama";
        $insert_values .= ", ?";
        $params[] = $agama;
        $types .= "s";
    }
    if ($has_status_kawin) {
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
    header("Location: manage_warga.php");
    exit();
}

// Handle Edit
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
    
    // Additional fields
    $tempat_lahir = !empty($_POST['tempat_lahir']) ? mysqli_real_escape_string($conn, $_POST['tempat_lahir']) : null;
    $goldar = !empty($_POST['goldar']) ? mysqli_real_escape_string($conn, $_POST['goldar']) : null;
    $agama = !empty($_POST['agama']) ? mysqli_real_escape_string($conn, $_POST['agama']) : null;
    $status_kawin = !empty($_POST['status_kawin']) ? mysqli_real_escape_string($conn, $_POST['status_kawin']) : null;
    
    // Build dynamic update query
    $update_fields = "nik=?, nama=?, jk=?, tanggal_lahir=?, pekerjaan=?, alamat=?, rt=?, rw=?, kk_id=?";
    $params = [$nik, $nama, $jk, $tanggal_lahir, $pekerjaan, $alamat, $rt, $rw, $kk_id, $id];
    $types = "ssssssiiii";
    
    if ($has_tempat_lahir) {
        $update_fields .= ", tempat_lahir=?";
        $params[] = $tempat_lahir;
        $types .= "s";
    }
    if ($has_goldar) {
        $update_fields .= ", goldar=?";
        $params[] = $goldar;
        $types .= "s";
    }
    if ($has_agama) {
        $update_fields .= ", agama=?";
        $params[] = $agama;
        $types .= "s";
    }
    if ($has_status_kawin) {
        $update_fields .= ", status_kawin=?";
        $params[] = $status_kawin;
        $types .= "s";
    }
    
    $sql = "UPDATE warga SET $update_fields WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    header("Location: manage_warga.php");
    exit();
}

// Query for warga list - include additional fields
$select_fields = "w.id, w.nik, w.nama, w.jk, w.tanggal_lahir, w.pekerjaan, w.alamat, w.rt, w.rw, w.kk_id";
if ($has_tempat_lahir) $select_fields .= ", w.tempat_lahir";
if ($has_goldar) $select_fields .= ", w.goldar";
if ($has_agama) $select_fields .= ", w.agama";
if ($has_status_kawin) $select_fields .= ", w.status_kawin";
if ($has_status_approval) $select_fields .= ", w.status_approval";

if ($has_status_approval) {
    $query = "SELECT $select_fields, rt.nama_rt, rw.name as nama_rw, kk.no_kk, kk.kepala_keluaraga 
              FROM warga w 
              LEFT JOIN rt ON w.rt = rt.id 
              LEFT JOIN rw ON w.rw = rw.id 
              LEFT JOIN kk ON w.kk_id = kk.id 
              WHERE 1=1";
} else {
    $query = "SELECT $select_fields, rt.nama_rt, rw.name as nama_rw, kk.no_kk, kk.kepala_keluaraga 
              FROM warga w 
              LEFT JOIN rt ON w.rt = rt.id 
              LEFT JOIN rw ON w.rw = rw.id 
              LEFT JOIN kk ON w.kk_id = kk.id 
              WHERE 1=1";
}
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

if ($has_status_approval) {
    $query .= " ORDER BY 
        CASE WHEN w.status_approval = 'menunggu' THEN 0 
             WHEN w.status_approval = 'diterima' THEN 1 
             ELSE 2 END, 
        w.id DESC 
        LIMIT ? OFFSET ?";
} else {
    $query .= " ORDER BY w.id DESC LIMIT ? OFFSET ?";
}
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
$count_query = "SELECT COUNT(*) as total FROM warga WHERE 1=1";
$count_params = [];
$count_types = '';

if (!empty($search)) {
    $count_query .= " AND (nik LIKE ? OR nama LIKE ? OR alamat LIKE ?)";
    $count_params[] = $search_param;
    $count_params[] = $search_param;
    $count_params[] = $search_param;
    $count_types .= 'sss';
}

$count_stmt = mysqli_prepare($conn, $count_query);
if (!empty($count_params)) {
    mysqli_stmt_bind_param($count_stmt, $count_types, ...$count_params);
}
mysqli_stmt_execute($count_stmt);
$count_result = mysqli_stmt_get_result($count_stmt);
$total_row = mysqli_fetch_assoc($count_result);
$total = $total_row['total'];
$total_pages = ceil($total / $limit);

$rt_result = mysqli_query($conn, "SELECT id, nama_rt FROM rt");
$rw_result = mysqli_query($conn, "SELECT id, name FROM rw");
$kk_result = mysqli_query($conn, "SELECT id, no_kk, kepala_keluaraga FROM kk");

?>
