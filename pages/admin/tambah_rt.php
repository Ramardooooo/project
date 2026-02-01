<?php

include 'config/database.php';

$success = '';
$error   = '';

if ($_SESSION['role'] === 'admin' && isset($_POST['add_rt'])) {

    $nama_rt  = trim($_POST['nama_rt']);
    $ketua_rt = trim($_POST['ketua_rt']);

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO rt (nama_rt, ketua_rt) VALUES (?, ?)"
    );

    mysqli_stmt_bind_param($stmt, "ss", $nama_rt, $ketua_rt);

    if (mysqli_stmt_execute($stmt)) {
        $success = "RT berhasil ditambahkan";
    } else {
        $error = "Gagal menambahkan RT";
    }

    mysqli_stmt_close($stmt);
}

if ($_SESSION['role'] !== 'admin') {
    exit('Akses ditolak');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Data RT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-slate-100 to-slate-200 min-h-screen">

<div class="ml-64 min-h-screen flex items-center justify-center p-8">


    <div class="max-w-xl bg-white rounded-2xl shadow-lg p-8">

        <h1 class="text-2xl font-bold text-gray-800 mb-6">
            Tambah Data RT
        </h1>

        <?php if ($success): ?>
            <div class="mb-4 rounded-lg bg-green-100 text-green-700 px-4 py-3">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="mb-4 rounded-lg bg-red-100 text-red-700 px-4 py-3">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">

            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">
                    Nama RT
                </label>
                <input
                    type="text"
                    name="nama_rt"
                    required
                    placeholder="Contoh: RT 01"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2
                           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                           outline-none"
                >
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">
                    Ketua RT
                </label>
                <input
                    type="text"
                    name="ketua_rt"
                    required
                    placeholder="Nama Ketua RT"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2
                           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                           outline-none"
                >
            </div>

            <div class="flex gap-3 pt-4">
                <button
                    type="submit"
                    name="add_rt"
                    class="flex-1 bg-indigo-600 hover:bg-indigo-700
                           text-white font-semibold py-2 rounded-lg transition"
                >
                    Simpan
                </button>

                <a
                    href="manage_rt_rw.php"
                    class="flex-1 text-center bg-gray-200 hover:bg-gray-300
                           text-gray-700 font-semibold py-2 rounded-lg transition"
                >
                    Kembali
                </a>
            </div>

        </form>

    </div>
</div>

</body>
</html>
