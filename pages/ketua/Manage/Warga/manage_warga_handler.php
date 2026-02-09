<?php
$search = $_GET['search'] ?? '';
$page = (int)($_GET['page'] ?? 1);
$limit = 10;
$offset = ($page - 1) * $limit;

$query = "SELECT w.id, w.nik, w.nama, w.jk, w.alamat, w.rt, w.rw, rt.nama_rt, rw.name as nama_rw FROM warga w LEFT JOIN rt ON w.rt = rt.id LEFT JOIN rw ON w.rw = rw.id WHERE 1=1";
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

if (isset($_POST['add_warga'])) {
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $jk = mysqli_real_escape_string($conn, $_POST['jk']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $rt = (int)$_POST['rt'];
    $rw = (int)$_POST['rw'];
    $kk_id = isset($_POST['kk_id']) && !empty($_POST['kk_id']) ? (int)$_POST['kk_id'] : null;

    $query = "INSERT INTO warga (nik, nama, jk, alamat, rt, rw, kk_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssssiii", $nik, $nama, $jk, $alamat, $rt, $rw, $kk_id);
    mysqli_stmt_execute($stmt);
    header("Location: manage_warga.php");
    exit();
}

if (isset($_POST['edit_warga'])) {
    $id = (int)$_POST['id'];
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $jk = mysqli_real_escape_string($conn, $_POST['jk']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $rt = (int)$_POST['rt'];
    $rw = (int)$_POST['rw'];

    $query = "UPDATE warga SET nik=?, nama=?, jk=?, alamat=?, rt=?, rw=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssssiii", $nik, $nama, $jk, $alamat, $rt, $rw, $id);
    mysqli_stmt_execute($stmt);
    header("Location: manage_warga.php");
    exit();
}

if (isset($_POST['delete_warga'])) {
    $id = (int)$_POST['id'];
    mysqli_query($conn, "DELETE FROM warga WHERE id = $id");
    header("Location: manage_warga.php");
    exit();
}
?>
