<?php
include '../../config/database.php';
include '../../layouts/admin/header.php';
include '../../layouts/admin/sidebar.php';

if ($_SESSION['role'] == 'admin') {
    $limit = 10;
    $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
    $offset = ($page - 1) * $limit;

    $total_rt = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM rt"))['total'];
    $total_pages = max(1, ceil($total_rt / $limit));

    $rt = mysqli_query($conn, "SELECT * FROM rt LIMIT $limit OFFSET $offset");

    if (isset($_POST['delete_rt'])) {
        $rt_id = $_POST['rt_id'];
        $stmt = mysqli_prepare($conn, "DELETE FROM rt WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $rt_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: manage_rt_rw.php");
        exit();
    }
?>

<div class="ml-64 p-6">
<h1 class="text-2xl font-bold mb-6">Kelola Data Wilayah RT</h1>

    <h2 class="text-xl font-bold mt-8 mb-4">Data RT</h2>
    <a href="tambah_rt" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mb-4 inline-block">Tambah RT</a>

    <table class="w-full bg-white rounded-lg shadow-lg overflow-hidden">
        <thead class="bg-gradient-to-r from-green-500 to-green-600 text-white">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nama RT</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Ketua RT</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php while ($r = mysqli_fetch_assoc($rt)) { ?>
            <tr class="hover:bg-gray-50 transition duration-200">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $r['id']; ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $r['nama_rt']; ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $r['ketua_rt']; ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="/PROJECT/edit_rt?id=<?php echo $r['id']; ?>" class="bg-green-500 text-white px-3 py-1 rounded-md hover:bg-green-600 transition duration-200">Edit</a>
                    <form method="POST" class="inline ml-2">
                        <input type="hidden" name="rt_id" value="<?php echo $r['id']; ?>">
                        <button type="submit" name="delete_rt" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-purple-600 transition duration-200" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="mt-4 flex justify-center">
        <?php if ($total_pages > 0): ?>
            <div class="flex space-x-2">
                <?php if ($page > 1): ?>
                    <a href="/PROJECT/manage_rt_rw?p=<?= $page - 1 ?>" class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="/PROJECT/manage_rt_rw?p=<?= $i ?>" class="px-3 py-2 <?= $i == $page ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700' ?> rounded hover:bg-gray-300"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="/PROJECT/manage_rt_rw?p=<?= $page + 1 ?>" class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
}
include '../../layouts/admin/footer.php';
?>
