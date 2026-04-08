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

if (isset($_POST['add_rt'])) {
    $nama_rt = $_POST['nama_rt'];
    $id_rw = isset($_POST['rw_id']) ? (int)$_POST['rw_id'] : null;
    $ketua_rt_id = (int)$_POST['ketua_rt_id'];

    $check_nama_rt = mysqli_prepare($conn, "SELECT id FROM rt WHERE nama_rt = ?");
    mysqli_stmt_bind_param($check_nama_rt, "s", $nama_rt);
    mysqli_stmt_execute($check_nama_rt);
    mysqli_stmt_store_result($check_nama_rt);
    if (mysqli_stmt_num_rows($check_nama_rt) > 0) {
        $error = "Nama RT sudah ada.";
    } else {
        // Check unique per RW
        if ($id_rw) {
            $check_rw = mysqli_prepare($conn, "SELECT id FROM rt WHERE nama_rt = ? AND id_rw = ?");
            mysqli_stmt_bind_param($check_rw, "si", $nama_rt, $id_rw);
            mysqli_stmt_execute($check_rw);
            mysqli_stmt_store_result($check_rw);
            if (mysqli_stmt_num_rows($check_rw) > 0) {
                $error = "Nama RT ini sudah ada di RW yang dipilih.";
                mysqli_stmt_close($check_rw);
            } else {
                mysqli_stmt_close($check_rw);
                goto insert_rt;
            }
        } else {
            goto insert_rt;
        }
    }
insert_rt:
    // Fetch ketua name for legacy ketua_rt field
    $ketua_query = mysqli_prepare($conn, "SELECT nama FROM warga WHERE id = ? AND status = 'aktif'");
    mysqli_stmt_bind_param($ketua_query, "i", $ketua_rt_id);
    mysqli_stmt_execute($ketua_query);
    $ketua_result = mysqli_stmt_get_result($ketua_query);
    $ketua_data = mysqli_fetch_assoc($ketua_result);
    $ketua_rt_nama = $ketua_data ? $ketua_data['nama'] : '';
    mysqli_stmt_close($ketua_query);

    if (empty($ketua_rt_nama)) {
        $error = "Ketua RT tidak valid atau tidak aktif.";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO rt (nama_rt, id_rw, ketua_rt, ketua_rt_id) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sisi", $nama_rt, $id_rw, $ketua_rt_nama, $ketua_rt_id);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Data RT berhasil ditambahkan.";
            header("Location: manage_rt_rw");
            exit();
        } else {
            $error = "Gagal menambahkan data RT: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}
if (isset($check_nama_rt)) mysqli_stmt_close($check_nama_rt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah RT - Lurahgo.id</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-900 via-indigo-900 to-purple-900">
<div class="ml-64 min-h-screen flex items-center justify-center p-8">
    <div class="max-w-xl w-full bg-white/90 backdrop-blur-md rounded-2xl shadow-lg p-7 border border-white/20 hover:shadow-2xl hover:bg-white/95 transition-all duration-300">

        <h2 class="text-xl font-semibold mb-5 text-center text-blue-700">
            Tambah RT
        </h2>

        <?php if (isset($success)): ?>
            <p class="text-blue-600 mb-3"><?php echo $success; ?></p>
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
                    placeholder="Contoh: RT 01/05"
                    required
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500"
                >
            </div>

            <div class="mb-3">
                <label class="block text-sm text-gray-700 mb-1">
                    RW Parent <span class="text-red-500">*</span>
                </label>
                <select name="rw_id" required class="w-full border rounded px-3 py-2 focus:outline-none focus:border-purple-500">
                    <option value="">Pilih RW</option>
                    <?php 
                    $rw_list = mysqli_query($conn, "SELECT id, name FROM rw WHERE status = 'aktif' ORDER BY name ASC");
                    while ($rw = mysqli_fetch_assoc($rw_list)): 
                    ?>
                        <option value="<?php echo $rw['id']; ?>"><?php echo htmlspecialchars($rw['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm text-gray-700 mb-1">
                    Ketua RT
                </label>
                <select name="ketua_rt_id" required class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500">
                    <option value="">Pilih Ketua RT dari Warga</option>
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
                    name="add_rt"
                    class="flex-1 py-3 rounded-xl font-semibold text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:scale-105 transition-all duration-300">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>

                <a
                    href="manage_rt_rw"
                    class="flex-1 text-center py-3 rounded-xl font-semibold text-gray-700 bg-gray-200 hover:bg-gray-300 hover:scale-105 transition-all duration-300">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>

        </form>

    </div>
</div>

</body>
</html>

