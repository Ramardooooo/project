<?php
if (isset($_POST['add_rt'])) {
    $nama_rt = mysqli_real_escape_string($conn, $_POST['nama_rt']);
    $ketua_rt = mysqli_real_escape_string($conn, $_POST['ketua_rt']);
    $id_rw = (int)$_POST['id_rw'];

    $query = "INSERT INTO rt (nama_rt, ketua_rt, id_rw) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssi", $nama_rt, $ketua_rt, $id_rw);
    mysqli_stmt_execute($stmt);
    header("Location: manage_wilayah");
    exit();
}

if (isset($_POST['edit_rt'])) {
    $id = (int)$_POST['id'];
    $nama_rt = mysqli_real_escape_string($conn, $_POST['nama_rt']);
    $ketua_rt = mysqli_real_escape_string($conn, $_POST['ketua_rt']);
    $id_rw = (int)$_POST['id_rw'];

    $query = "UPDATE rt SET nama_rt=?, ketua_rt=?, id_rw=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssii", $nama_rt, $ketua_rt, $id_rw, $id);
    mysqli_stmt_execute($stmt);
    header("Location: manage_wilayah");
    exit();
}

if (isset($_POST['delete_rt'])) {
    $id = (int)$_POST['id'];
    mysqli_query($conn, "DELETE FROM rt WHERE id = $id");
    header("Location: manage_wilayah");
    exit();
}

if (isset($_POST['add_rw'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);

    $query = "INSERT INTO rw (name) VALUES (?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $name);
    mysqli_stmt_execute($stmt);
    header("Location: manage_wilayah");
    exit();
}

if (isset($_POST['edit_rw'])) {
    $id = (int)$_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);

    $query = "UPDATE rw SET name=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $name, $id);
    mysqli_stmt_execute($stmt);
    header("Location: manage_wilayah");
    exit();
}

if (isset($_POST['delete_rw'])) {
    $id = (int)$_POST['id'];
    mysqli_query($conn, "DELETE FROM rw WHERE id = $id");
    header("Location: manage_wilayah");
    exit();
}
?>
