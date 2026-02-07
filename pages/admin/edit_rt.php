<?php
include '../../config/database.php';

if (isset($_GET['id'])) {
    $rt_id = $_GET['id'];
    $rt = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rt WHERE id = $rt_id"));
    if (!$rt) {
        header("Location: manage_rt_rw");
        exit();
    }
}

if (isset($_POST['update_rt'])) {
    $nama_rt = $_POST['nama_rt'];
    $ketua_rt = $_POST['ketua_rt'];
    $status = $_POST['status'];

    $old_rt = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_rt, ketua_rt, status FROM rt WHERE id = $rt_id"));

    $stmt = mysqli_prepare($conn, "UPDATE rt SET nama_rt=?, ketua_rt=?, status=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "sssi", $nama_rt, $ketua_rt, $status, $rt_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $action = "Update RT";
    $table_name = "rt";
    $record_id = $rt_id;
    $old_value = json_encode($old_rt);
    $new_value = json_encode(['nama_rt' => $nama_rt, 'ketua_rt' => $ketua_rt, 'status' => $status]);
    $user_id = $_SESSION['user_id'] ?? null;
    $username = $_SESSION['username'] ?? 'Unknown';
    $audit_stmt = mysqli_prepare($conn, "INSERT INTO audit_log (action, table_name, record_id, old_value, new_value, user_id, username) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($audit_stmt, "ssissis", $action, $table_name, $record_id, $old_value, $new_value, $user_id, $username);
    mysqli_stmt_execute($audit_stmt);
    mysqli_stmt_close($audit_stmt);

    header("Location: manage_rt_rw");
    exit();
}

include '../../layouts/admin/header.php';
include '../../layouts/admin/sidebar.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: home");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Edit RT: Lurahgo.id</title>
</head>
<body class="min-h-screen bg-blue-900">
<div class="ml-64 min-h-screen flex items-center justify-center p-8">
    <div class="max-w-xl w-full bg-white/90 backdrop-blur-md rounded-2xl shadow-lg p-7 border border-white/20 hover:shadow-2xl hover:bg-white/95 transition-all duration-300">

        <h1 class="text-xl font-semibold text-gray-800 mb-5">
            Edit Data RT
        </h1>

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
                    value="<?php echo $rt['nama_rt']; ?>"
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
                    value="<?php echo $rt['ketua_rt']; ?>"
                    placeholder="Nama Ketua RT"
                    required
                    class="w-full border rounded px-3 py-2
                           focus:outline-none focus:border-green-500"
                >
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">
                    Status
                </label>
                <select
                    name="status"
                    required
                    class="w-full border rounded px-3 py-2
                           focus:outline-none focus:border-green-500"
                >
                    <option value="aktif" <?php echo ($rt['status'] == 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                    <option value="tidak_aktif" <?php echo ($rt['status'] == 'tidak_aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                </select>
            </div>

            <div class="flex gap-3 pt-3">
                <button
                    type="submit"
                    name="update_rt"
                    class="flex-1 py-3 rounded-xl font-semibold text-white bg-gradient-to-r from-blue-400 to-blue-600 hover:scale-105 transition-all duration-300">
                    Update
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
