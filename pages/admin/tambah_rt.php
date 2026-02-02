<?php
include '../../config/database.php';

if (isset($_POST['add_rt'])) {
    $nama_rt = $_POST['nama_rt'];
    $ketua_rt = $_POST['ketua_rt'];

    $stmt = mysqli_prepare($conn, "INSERT INTO rt (nama_rt, ketua_rt) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ss", $nama_rt, $ketua_rt);
    if (mysqli_stmt_execute($stmt)) {
        $success = "Data RT berhasil ditambahkan.";
    } else {
        $error = "Gagal menambahkan data RT: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
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

<body class="min-h-screen" style="background-image: url('https://images.unsplash.com/photo-1565102127622-df163cfbdaa4?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'); background-size: cover; background-position: center; background-attachment: fixed;">

<div class="ml-64 min-h-screen flex items-center justify-center p-8 backdrop-blur-sm">

    <div class="max-w-xl w-full bg-white/20 backdrop-blur-md rounded-2xl shadow-lg p-7 border border-white/30">

        <h2 class="text-xl font-semibold mb-5 text-center text-green-700">
            Tambah RT
        </h2>

        <?php if (isset($success)): ?>
            <p class="text-green-600 mb-3"><?php echo $success; ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-3"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" class="space-y-4">

            <div>
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

            <div>
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

            <div class="flex gap-3 pt-3">
                <button
                    type="submit"
                    name="add_rt"
                    class="flex-1 bg-green-600 hover:bg-green-700
                           text-white py-2 rounded">
                    Simpan
                </button>

                <a
                    href="manage_rt_rw.php"
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
