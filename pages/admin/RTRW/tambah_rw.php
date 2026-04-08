<?php
ob_start();
session_start();
include '../../../config/database.php';
include '../../../layouts/admin/header.php';
include '../../../layouts/admin/sidebar.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: home");
    exit();
}

if (isset($_POST['add_rw'])) {
    $nama_rw = $_POST['nama_rw'];
    $ketua_rw_id = (int)$_POST['ketua_rw_id'];

    $check_nama_rw = mysqli_prepare($conn, "SELECT id FROM rw WHERE name = ?");
    mysqli_stmt_bind_param($check_nama_rw, "s", $nama_rw);
    mysqli_stmt_execute($check_nama_rw);
    mysqli_stmt_store_result($check_nama_rw);
    if (mysqli_stmt_num_rows($check_nama_rw) > 0) {
        $error = "Nama RW sudah ada.";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO rw (name, ketua_rw_id) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "si", $nama_rw, $ketua_rw_id);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Data RW berhasil ditambahkan.";
            header("Location: manage_rw");
            exit();
        } else {
            $error = "Gagal menambahkan data RW: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_stmt_close($check_nama_rw);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah RW - Lurahgo.id</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-blue-900">
<div class="ml-64 min-h-screen flex items-center justify-center p-8">
    <div class="max-w-xl w-full bg-white/90 backdrop-blur-md rounded-2xl shadow-lg p-7 border border-white/20 hover:shadow-2xl hover:bg-white/95 transition-all duration-300">

        <h2 class="text-xl font-semibold mb-5 text-center text-green-700">
            Tambah RW
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
                    Nama RW
                </label>
                <input
                    type="text"
                    name="nama_rw"
                    placeholder="Contoh: RW 01"
                    required
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:border-green-500"
                >
            </div>

            <div class="mb-6">
                <label class="block text-sm text-gray-700 mb-1">
                    Ketua RW
                </label>
                <select name="ketua_rw_id" required class="w-full border rounded px-3 py-2 focus:outline-none focus:border-green-500">
                    <option value="">Pilih Ketua RW dari Warga</option>
                    <?php 
                    $warga_list = mysqli_query($conn, "SELECT id, nama, nik FROM warga WHERE status = 'aktif' ORDER BY nama ASC");
                    while ($w = mysqli_fetch_assoc($warga_list)): 
                    ?>
                        <option value="<?php echo $w['id']; ?>"><?php echo htmlspecialchars($w['nama']) . ' (' . $w['nik'] . ')'; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="flex gap-3 pt-3">
                <button
                    type="submit"
                    name="add_rw"
                    class="flex-1 py-3 rounded-xl font-semibold text-white bg-gradient-to-r from-green-400 to-emerald-600 hover:scale-105 transition-all duration-300">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>

                <a
                    href="manage_rw"
                    class="flex-1 text-center py-3 rounded-xl font-semibold text-gray-700 bg-gray-200 hover:bg-gray-300 hover:scale-105 transition-all duration-300">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>

        </form>

    </div>
</div>

</body>
</html>

