<?php
$search = $_GET['search'] ?? '';
$page = (int)($_GET['page'] ?? 1);
$limit = 10; 
$offset = ($page - 1) * $limit;

// Check if status_approval column exists in kk table
$has_status_approval = false;
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM kk LIKE 'status_approval'");
if ($check_col && mysqli_num_rows($check_col) > 0) {
    $has_status_approval = true;
}

// Handle Approve KK
if (isset($_POST['approve_kk']) && $has_status_approval) {
    $kk_id = (int)$_POST['kk_id'];
    $stmt = mysqli_prepare($conn, "UPDATE kk SET status_approval = 'diterima' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $kk_id);
    mysqli_stmt_execute($stmt);
    header("Location: manage_kk");
    exit();
}

// Handle Reject KK
if (isset($_POST['reject_kk']) && $has_status_approval) {
    $kk_id = (int)$_POST['kk_id'];
    $stmt = mysqli_prepare($conn, "UPDATE kk SET status_approval = 'ditolak' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $kk_id);
    mysqli_stmt_execute($stmt);
    header("Location: manage_kk");
    exit();
}

$query = "SELECT k.id, k.no_kk, k.kepala_keluaraga" . ($has_status_approval ? ", k.status_approval" : "") . ", 
          (SELECT COUNT(*) FROM warga w WHERE w.kk_id = k.id) as anggota_count,
COALESCE(w.nik, '') as kepala_nik
          FROM kk k 
          LEFT JOIN warga w ON LOWER(TRIM(w.nama)) = LOWER(TRIM(k.kepala_keluaraga)) AND w.status = 'aktif'
          WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $query .= " AND (no_kk LIKE ? OR kepala_keluaraga LIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ss';
}

if ($has_status_approval) {
    $query .= " ORDER BY 
        CASE WHEN k.status_approval = 'menunggu' THEN 0 
             WHEN k.status_approval = 'diterima' THEN 1 
             ELSE 2 END, 
        k.id DESC 
        LIMIT ? OFFSET ?"; 
} else {
    $query .= " ORDER BY id DESC LIMIT ? OFFSET ?";
}
$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt = mysqli_prepare($conn, $query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$kk_result = mysqli_stmt_get_result($stmt);

$count_query = "SELECT COUNT(*) as total FROM kk WHERE 1=1";
$count_params = [];
$count_types = '';

if (!empty($search)) {
    $count_query .= " AND (no_kk LIKE ? OR kepala_keluaraga LIKE ?)";
    $count_params[] = $search_param;
    $count_params[] = $search_param;
    $count_types .= 'ss';
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

if (isset($_POST['add_kk'])) {
    $no_kk = mysqli_real_escape_string($conn, $_POST['no_kk']);
    $kepala_keluaraga = mysqli_real_escape_string($conn, $_POST['kepala_keluarga'] ?? $_POST['kepala_keluaraga']);

    if ($has_status_approval) {
        $query = "INSERT INTO kk (no_kk, kepala_keluaraga, status_approval) VALUES (?, ?, 'menunggu')";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $no_kk, $kepala_keluaraga);
    } else {
        $query = "INSERT INTO kk (no_kk, kepala_keluaraga) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $no_kk, $kepala_keluaraga);
    }
    mysqli_stmt_execute($stmt);
    
    // Log activity
    $user_id = $_SESSION['user_id'] ?? null;
    mysqli_query($conn, "INSERT INTO activities (action, entity, description, user_id) VALUES ('add', 'kk', 'KK baru ditambahkan: $no_kk', $user_id)");
    
    header("Location: manage_kk");
    exit();
}

if (isset($_POST['edit_kk'])) {
    $id = (int)$_POST['id'];
    $no_kk = mysqli_real_escape_string($conn, $_POST['no_kk']);
    $kepala_keluaraga = mysqli_real_escape_string($conn, $_POST['kepala_keluarga'] ?? $_POST['kepala_keluaraga']);

    $query = "UPDATE kk SET no_kk=?, kepala_keluaraga=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssi", $no_kk, $kepala_keluaraga, $id);
    mysqli_stmt_execute($stmt);
    
    // Log activity
    $user_id = $_SESSION['user_id'] ?? null;
    mysqli_query($conn, "INSERT INTO activities (action, entity, description, user_id) VALUES ('edit', 'kk', 'Data KK diperbarui: $no_kk', $user_id)");
    
    header("Location: manage_kk");
    exit();
}

if (isset($_POST['delete_kk'])) {
    $id = (int)$_POST['id'];
    
    // Get KK info for logging
    $kk_result = mysqli_query($conn, "SELECT no_kk FROM kk WHERE id = $id");
    $kk_data = mysqli_fetch_assoc($kk_result);
    $kk_no = $kk_data['no_kk'] ?? 'Unknown';
    
    mysqli_query($conn, "DELETE FROM kk WHERE id = $id");
    
    // Log activity
    $user_id = $_SESSION['user_id'] ?? null;
    mysqli_query($conn, "INSERT INTO activities (action, entity, description, user_id) VALUES ('delete', 'kk', 'KK dihapus: $kk_no', $user_id)");
    
    header("Location: manage_kk");
    exit();
}

// Get pending count for alert
$pending_count = 0;
if ($has_status_approval) {
    $pending_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM kk WHERE status_approval = 'menunggu'");
    if ($pending_result) {
        $pending_row = mysqli_fetch_assoc($pending_result);
        $pending_count = $pending_row['total'];
    }
}
?>