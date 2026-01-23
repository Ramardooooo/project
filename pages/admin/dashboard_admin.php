<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
include '../../config/database.php';
include '../../layouts/admin/header.php';
include '../../layouts/admin/sidebar.php';

if ($_SESSION['role'] == 'admin') {
    $totalWarga = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM warga"))['total'];
    $totalKK = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kk"))['total'];
    $totalRT = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM rt"))['total'];
    $totalRW = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM rw"))['total'];
?>

<div class="ml-64 p-6">
<h1 class="text-2xl font-bold mb-6">Dashboard Admin Lurahgo.id</h1>

  <div class="grid grid-cols-4 gap-6">
        <div class="bg-white p-4 rounded shadow border-l-4 border-yellow-500">
            <p class="text-gray-500">Total Warga</p>
            <h2 class="text-3xl font-bold text-yellow-600"><?= $totalWarga ?></h2>
        </div>
        <div class="bg-white p-4 rounded shadow border-l-4 border-yellow-500">
            <p class="text-gray-500">Total Kepala Keluarga</p>
            <h2 class="text-3xl font-bold text-yellow-600"><?= $totalKK ?></h2>
        </div>
        <div class="bg-white p-4 rounded shadow border-l-4 border-yellow-500">
            <p class="text-gray-500">Total RT</p>
            <h2 class="text-3xl font-bold text-yellow-600"><?= $totalRT ?></h2>
        </div>
        <div class="bg-white p-4 rounded shadow border-l-4 border-yellow-500">
            <p class="text-gray-500">Total RW</p>
            <h2 class="text-3xl font-bold text-yellow-600"><?= $totalRW ?></h2>
        </div>
    </div>

    <h2 class="text-xl font-bold mt-8 mb-4">Fungsi Admin</h2>
    <div class="grid grid-cols-3 gap-6">
        <div class="bg-white p-4 rounded shadow border-l-4 border-blue-500">
            <h3 class="text-lg font-bold text-blue-600">Mengelola Akun Pengguna</h3>
            <p class="text-gray-500">Tambah, edit, dan hapus akun pengguna</p>
            <a href="manage_users.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mt-2 inline-block">Kelola Pengguna</a>
        </div>
        <div class="bg-white p-4 rounded shadow border-l-4 border-green-500">
            <h3 class="text-lg font-bold text-green-600">Mengelola Data Wilayah (RT/RW)</h3>
            <p class="text-gray-500">Atur dan perbarui data RT/RW</p>
            <a href="manage_rt_rw.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mt-2 inline-block">Kelola RT/RW</a>
        </div>
        <div class="bg-white p-4 rounded shadow border-l-4 border-purple-500">
            <h3 class="text-lg font-bold text-purple-600">Mengelola Data Master</h3>
            <p class="text-gray-500">Kelola data master untuk sistem</p>
            <a href="#" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600 mt-2 inline-block">Kelola Data Master</a>
        </div>
    </div>

</div>
<?php
}
