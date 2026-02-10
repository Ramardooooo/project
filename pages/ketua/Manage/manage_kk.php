
<?php
include '../../../config/database.php';

// Handle POST requests before any output
if (isset($_POST['add_kk'])) {
    $no_kk = mysqli_real_escape_string($conn, $_POST['no_kk']);
    $kepala_keluaraga = mysqli_real_escape_string($conn, $_POST['kepala_keluaraga']);

    $query = "INSERT INTO kk (no_kk, kepala_keluaraga) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $no_kk, $kepala_keluaraga);
    mysqli_stmt_execute($stmt);
    header("Location: manage_kk");
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
    header("Location: manage_kk");
    exit();
}

if (isset($_POST['delete_kk'])) {
    $id = (int)$_POST['id'];
    mysqli_query($conn, "DELETE FROM kk WHERE id = $id");
    header("Location: manage_kk");
    exit();
}

include 'common.php';
include 'Kk/manage_kk_handler.php';
include 'Kk/manage_kk_view.php';
?>
