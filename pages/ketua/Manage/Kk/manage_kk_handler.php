<?php
$search = $_GET['search'] ?? '';
$page = (int)($_GET['page'] ?? 1);
$limit = 10; 
$offset = ($page - 1) * $limit;

$query = "SELECT id, no_kk, kepala_keluaraga FROM kk WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $query .= " AND (no_kk LIKE ? OR kepala_keluaraga LIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ss';
}

$query .= " ORDER BY id DESC LIMIT ? OFFSET ?";
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
    $kepala_keluaraga = mysqli_real_escape_string($conn, $_POST['kepala_keluaraga']);

    $query = "INSERT INTO kk (no_kk, kepala_keluaraga) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $no_kk, $kepala_keluaraga);
    mysqli_stmt_execute($stmt);
    header("Location: manage_kk.php");
    exit();
}

if (isset($_POST['edit_kk'])) {
    $id = (int)$_POST['id'];
    $no_kk = mysqli_real_escape_string($conn, $_POST['no_kk']);
    $kepala_keluaraga = mysqli_real_escape_string($conn, $_POST['kepala_keluaraga']);

    $query = "UPDATE kk SET no_kk=?, kepala_keluaraga=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssi", $no_kk, $kepala_keluaraga, $id);
    mysqli_stmt_execute($stmt);
    header("Location: manage_kk.php");
    exit();
}

if (isset($_POST['delete_kk'])) {
    $id = (int)$_POST['id'];
    mysqli_query($conn, "DELETE FROM kk WHERE id = $id");
    header("Location: manage_kk.php");
    exit();
}
?>
