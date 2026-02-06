<?php
include '../../config/database.php';
include '../../layouts/admin/header.php';
include '../../layouts/admin/sidebar.php';

if (isset($_POST['add_rt'])) {
    $nama_rt = $_POST['nama_rt'];
    $ketua_rt = $_POST['ketua_rt'];
    $status = $_POST['status'];

    $check_nama_rt = mysqli_prepare($conn, "SELECT id FROM rt WHERE nama_rt = ?");
    mysqli_stmt_bind_param($check_nama_rt, "s", $nama_rt);
    mysqli_stmt_execute($check_nama_rt);
    mysqli_stmt_store_result($check_nama_rt);
    if (mysqli_stmt_num_rows($check_nama_rt) > 0) {
        $error = "Nama RT sudah ada.";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO rt (nama_rt, ketua_rt, status) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $nama_rt, $ketua_rt, $status);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Data RT berhasil ditambahkan.";
        } else {
            $error = "Gagal menambahkan data RT: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_stmt_close($check_nama_rt);
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah RT - Lurahgo.id</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-blue-900">
<div class="ml-64 min-h-screen flex items-center justify-center p-8">
    <div class="max-w-xl w-full bg-white/90 backdrop-blur-md rounded-2xl shadow-lg p-7 border border-white/20 hover:shadow-2xl hover:bg-white/95 transition-all duration-300">

        <h2 class="text-xl font-semibold mb-5 text-center text-green-700">
            Tambah RT
        </h2>

        <?php if (isset($success)): ?>
            <p class="text-green-600 mb-3"><?php echo $success; ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-3"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="block text-sm text-gray-700 mb-1">
                    Nama RT
                </label>
                <input
                    type="text"
                    name="nama_rt"
                    placeholder="Contoh: RT 01"
                    required
                    class="w-full border rounded px-3 py-2
                           focus:outline-none focus:border-green-500"
                >
            </div>

            <div class="mb-3">
                <label class="block text-sm text-gray-700 mb-1">
                    Ketua RT
                </label>
                <input
                    type="text"
                    name="ketua_rt"
                    placeholder="Nama Ketua RT"
                    required
                    class="w-full border rounded px-3 py-2
                           focus:outline-none focus:border-green-500"
                >
            </div>

            <div class="mb-3">
                <label class="block text-sm text-gray-700 mb-1">
                    Status
                </label>
                <select
                    name="status"
                    required
                    class="w-full border rounded px-3 py-2
                           focus:outline-none focus:border-green-500"
                >
                    <option value="aktif">Aktif</option>
                    <option value="tidak_aktif">Tidak Aktif</option>
                </select>
            </div>

            <div class="flex gap-3 pt-3">
                <button
                    type="submit"
                    name="add_rt"
                    class="flex-1 py-3 rounded-xl font-semibold text-white bg-gradient-to-r from-green-400 to-emerald-600 hover:scale-105 transition-all duration-300">
                    Simpan
                </button>

                <a
                    href="manage_rt_rw"
                    class="flex-1 text-center bg-gray-200 hover:bg-gray-300
                           text-gray-700 py-2 rounded">
                    Kembali
                </a>
            </div>

        </form>

    </div>
</div>

</body>
</html>
