<?php
include '../../config/database.php';
include '../../layouts/admin/header.php';
include '../../layouts/admin/sidebar.php';

if (isset($_GET['id'])) {
    $rt_id = $_GET['id'];
    $rt = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rt WHERE id = $rt_id"));
    if (!$rt) {
        header("Location: manage_rt_rw.php");
        exit();
    }
}

if (isset($_POST['update_rt'])) {
    $nama_rt = $_POST['nama_rt'];
    $ketua_rt = $_POST['ketua_rt'];

    $stmt = mysqli_prepare($conn, "UPDATE rt SET nama_rt=?, ketua_rt=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssi", $nama_rt, $ketua_rt, $rt_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: manage_rt_rw.php");
    exit();
}

include '../../layouts/admin/header.php';
include '../../layouts/admin/sidebar.php';
?>

<div class="ml-64 min-h-screen flex items-center justify-center p-8">

    <div class="max-w-xl w-full bg-white rounded-md shadow p-7">

        <h1 class="text-xl font-semibold text-gray-800 mb-5">
            Edit Data RT
        </h1>

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

            <div class="flex gap-3 pt-3">
                <button
                    type="submit"
                    name="update_rt"
                    class="flex-1 bg-green-600 hover:bg-green-700
                           text-white py-2 rounded">
                    Update
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

<?php
include '../../layouts/admin/footer.php';
?>
