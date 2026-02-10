<?php

include '../../../config/database.php';

// Handle POST requests before any output
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
    header("Location: manage_warga");
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
    header("Location: manage_warga");
    exit();
}

if (isset($_POST['delete_warga'])) {
    $id = (int)$_POST['id'];
    // First delete related mutasi_warga records to avoid foreign key constraint
    mysqli_query($conn, "DELETE FROM mutasi_warga WHERE warga_id = $id");
    // Then delete the warga
    mysqli_query($conn, "DELETE FROM warga WHERE id = $id");
    header("Location: manage_warga");
    exit();
}

include 'common.php';
include 'warga/manage_warga_handler.php';
include 'warga/manage_warga_view.php';

?>
