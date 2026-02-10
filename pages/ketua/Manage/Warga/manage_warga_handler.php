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

?>
