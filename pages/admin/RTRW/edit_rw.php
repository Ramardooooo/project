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

if (isset($_GET['id'])) {
    $rw_id = (int)$_GET['id'];
    $rw = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rw WHERE id = $rw_id"));
    if (!$rw) {
    header("Location: manage_rw");
    exit();
    }
}

if (isset($_POST['update_rw'])) {
    $nama_rw = $_POST['nama_rw'];
    $ketua_rw_id = (int)$_POST['ketua_rw_id'];
    $status = $_POST['status'];

    $old_rw = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name, ketua_rw_id, status FROM rw WHERE id = $rw_id"));

    $stmt = mysqli_prepare($conn, "UPDATE rw SET name=?, ketua_rw_id=?, status=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "sisi", $nama_rw, $ketua_rw_id, $status, $rw_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $action = "Update RW";
    $table_name = "rw";
    $record_id = $rw_id;
    $old_value = json_encode($old_rw);
    $new_value = json_encode(['name' => $nama_rw, 'ketua_rw_id' => $ketua_rw_id, 'status' => $status]);
    $user_id = $_SESSION['user_id'] ?? null;
    $username = $_SESSION['username'] ?? 'Unknown';
    $audit_stmt = mysqli_prepare($conn, "INSERT INTO audit_log (action, table_name, record_id, old_value, new_value, user_id, username) VALUES (?, ?, ?, ?, ?, ?, ?)"); 
    mysqli_stmt_bind_param($audit_stmt, "ssissis", $action, $table_name, $record_id, $old_value, $new_value, $user_id, $username);
    mysqli_stmt_execute($audit_stmt);
    mysqli_stmt_close($audit_stmt);

    header("Location: manage_rw");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Edit RW: Lurahgo.id</title>
</head>
<body class="min-h-screen bg-blue-900">
<div class="ml-64 min-h-screen flex items-center justify-center p-8">
    <div class="max-w-xl w-full bg-white/90 backdrop-blur-md rounded-2xl shadow-lg p-7 border border-white/20 hover:shadow-2xl hover:bg-white/95 transition-all duration-300">

        <h1 class="text-xl font-semibold text-gray-800 mb-5">
            Edit Data RW
        </h1>

        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-3"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" class="space-y-4">

            <div>
                <label class="block text-sm text-gray-700 mb-1">
                    Nama RW
                </label>
                <input
                    type="text"
                    name="nama_rw"
                    value="<?php echo $rw['name']; ?>"
                    placeholder="Contoh: RW 01"
                    required
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:border-green-500"
                >
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">
                    Ketua RW
                </label>
                <select name="ketua_rw_id" required class="w-full border rounded px-3 py-2 focus:outline-none focus:border-green-500">
                    <option value="">Pilih Ketua RW dari Warga</option>
                    <?php 
                    $warga_list = mysqli_query($conn, "SELECT id, nama, nik FROM warga WHERE status = 'aktif' ORDER BY nama ASC");
                    while ($w = mysqli_fetch_assoc($warga_list)): 
                    $selected = (isset($rw['ketua_rw_id']) && $rw['ketua_rw_id'] == $w['id']) ? 'selected' : '';
                    ?>
                        <option value="<?php echo $w['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($w['nama']) . ' (' . $w['nik'] . ')'; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">
                    Status
                </label>
                <select
                    name="status"
                    required
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:border-green-500"
                >
                    <option value="aktif" <?php echo ($rw['status'] == 'aktif') ? 'selected' : ''; ?> >Aktif</option>
                    <option value="tidak_aktif" <?php echo ($rw['status'] == 'tidak_aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                </select>
            </div>

            <div class="flex gap-3 pt-3">
                <button
                    type="submit"
                    name="update_rw"
                    class="flex-1 py-3 rounded-xl font-semibold text-white bg-gradient-to-r from-blue-400 to-blue-600 hover:scale-105 transition-all duration-300">
                    Update
                </button>

                <a
                    href="manage_rw"
                    class="flex-1 text-center bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 rounded">
                    Kembali
                </a>
            </div>

        </form>

    </div>
</div>

</body>
</html>
