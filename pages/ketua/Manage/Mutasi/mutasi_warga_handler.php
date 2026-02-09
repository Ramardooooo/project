<?php
if (isset($_POST['mutasi_datang'])) {
    $warga_id = (int)$_POST['warga_id'];
    $tanggal_mutasi = mysqli_real_escape_string($conn, $_POST['tanggal_mutasi']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    $query = "UPDATE warga SET status='aktif', tanggal_mutasi=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $tanggal_mutasi, $warga_id);
    mysqli_stmt_execute($stmt);

    $mutasi_query = "INSERT INTO mutasi_warga (warga_id, jenis_mutasi, tanggal_mutasi, keterangan) VALUES (?, 'datang', ?, ?)";
    $mutasi_stmt = mysqli_prepare($conn, $mutasi_query);
    mysqli_stmt_bind_param($mutasi_stmt, "iss", $warga_id, $tanggal_mutasi, $keterangan);
    mysqli_stmt_execute($mutasi_stmt);

    header("Location: mutasi_warga.php");
    exit();
}

if (isset($_POST['mutasi_pindah'])) {
    $warga_id = (int)$_POST['warga_id'];
    $tanggal_mutasi = mysqli_real_escape_string($conn, $_POST['tanggal_mutasi']);
    $alamat_tujuan = mysqli_real_escape_string($conn, $_POST['alamat_tujuan']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    $query = "UPDATE warga SET status='pindah', tanggal_mutasi=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $tanggal_mutasi, $warga_id);
    mysqli_stmt_execute($stmt);

    $mutasi_query = "INSERT INTO mutasi_warga (warga_id, jenis_mutasi, tanggal_mutasi, alamat_tujuan, keterangan) VALUES (?, 'pindah', ?, ?, ?)";
    $mutasi_stmt = mysqli_prepare($conn, $mutasi_query);
    mysqli_stmt_bind_param($mutasi_stmt, "isss", $warga_id, $tanggal_mutasi, $alamat_tujuan, $keterangan);
    mysqli_stmt_execute($mutasi_stmt);

    header("Location: mutasi_warga.php");
    exit();
}

if (isset($_POST['mutasi_meninggal'])) {
    $warga_id = (int)$_POST['warga_id'];
    $tanggal_mutasi = mysqli_real_escape_string($conn, $_POST['tanggal_mutasi']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    $query = "UPDATE warga SET status='meninggal', tanggal_mutasi=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $tanggal_mutasi, $warga_id);
    mysqli_stmt_execute($stmt);

    $mutasi_query = "INSERT INTO mutasi_warga (warga_id, jenis_mutasi, tanggal_mutasi, keterangan) VALUES (?, 'meninggal', ?, ?)";
    $mutasi_stmt = mysqli_prepare($conn, $mutasi_query);
    mysqli_stmt_bind_param($mutasi_stmt, "iss", $warga_id, $tanggal_mutasi, $keterangan);
    mysqli_stmt_execute($mutasi_stmt);

    header("Location: mutasi_warga.php");
    exit();
}

$warga_query = "SELECT w.*, rt.nama_rt, rw.name as nama_rw FROM warga w
                LEFT JOIN rt ON w.rt = rt.id
                LEFT JOIN rw ON w.rw = rw.id
                WHERE w.status = 'aktif'
                ORDER BY w.nama";
$warga_result = mysqli_query($conn, $warga_query);

$mutasi_query = "SELECT m.*, w.nama, w.nik, rt.nama_rt, rw.name as nama_rw
                 FROM mutasi_warga m
                 LEFT JOIN warga w ON m.warga_id = w.id
                 LEFT JOIN rt ON w.rt = rt.id
                 LEFT JOIN rw ON w.rw = rw.id
                 ORDER BY m.tanggal_mutasi DESC LIMIT 50";
$mutasi_result = mysqli_query($conn, $mutasi_query);
?>
