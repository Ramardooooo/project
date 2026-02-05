<?php
include 'config/database.php';

if (isset($_POST['delete_warga'])) {
    $warga_id = $_POST['warga_id'];
    mysqli_query($conn, "DELETE FROM warga WHERE id=$warga_id");
    header("Location: /PROJECT/manage_master_data");
    exit();
}

if (isset($_POST['delete_kk'])) {
    $kk_id = $_POST['kk_id'];
    mysqli_query($conn, "DELETE FROM kk WHERE id=$kk_id");
    header("Location: /PROJECT/manage_master_data");
    exit();
}

include 'layouts/admin/header.php';
include 'layouts/admin/sidebar.php';

if ($_SESSION['role'] == 'admin') {
    $limit = 10;
    $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

    $page_warga = isset($_GET['page_warga']) ? (int)$_GET['page_warga'] : 1;
    $offset_warga = ($page_warga - 1) * $limit;
    $total_warga_query = "SELECT COUNT(*) as total FROM warga";
    $warga_query = "SELECT * FROM warga";
    if ($search) {
        $total_warga_query .= " WHERE nama LIKE '%$search%' OR alamat LIKE '%$search%'";
        $warga_query .= " WHERE nama LIKE '%$search%' OR alamat LIKE '%$search%'";
    }
    $total_warga = mysqli_fetch_assoc(mysqli_query($conn, $total_warga_query))['total'];
    $total_pages_warga = max(1, ceil($total_warga / $limit));
    $warga_query .= " LIMIT $limit OFFSET $offset_warga";
    $warga = mysqli_query($conn, $warga_query);

    $page_kk = isset($_GET['page_kk']) ? (int)$_GET['page_kk'] : 1;
    $offset_kk = ($page_kk - 1) * $limit;
    $total_kk_query = "SELECT COUNT(*) as total FROM kk";
    $kk_query = "SELECT * FROM kk";
    if ($search) {
        $total_kk_query .= " WHERE nama_kk LIKE '%$search%' OR alamat LIKE '%$search%'";
        $kk_query .= " WHERE nama_kk LIKE '%$search%' OR alamat LIKE '%$search%'";
    }
    $total_kk = mysqli_fetch_assoc(mysqli_query($conn, $total_kk_query))['total'];
    $total_pages_kk = max(1, ceil($total_kk / $limit));
    $kk_query .= " LIMIT $limit OFFSET $offset_kk";
    $kk = mysqli_query($conn, $kk_query);
?>

<div class="ml-64 p-6">
<h1 class="text-2xl font-bold mb-6">Kelola Data Master</h1>

    <h2 class="text-xl font-bold mt-8 mb-4">Data Warga</h2>
    <a href="tambah_warga.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mb-4 inline-block">Tambah Warga</a>

    <table class="w-full bg-white rounded shadow mb-8">
        <thead class="bg-yellow-100">
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Nama</th>
                <th class="px-4 py-2">Alamat</th>
                <th class="px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($w = mysqli_fetch_assoc($warga)) { ?>
            <tr class="border-t">
                <td class="px-4 py-2"><?php echo $w['id']; ?></td>
                <td class="px-4 py-2"><?php echo $w['nama']; ?></td>
                <td class="px-4 py-2"><?php echo $w['alamat']; ?></td>
                <td class="px-4 py-2">
                    <a href="edit_warga.php?id=<?php echo $w['id']; ?>" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Edit</a>
                    <form method="POST" class="inline ml-2">
                        <input type="hidden" name="warga_id" value="<?php echo $w['id']; ?>">
                        <button type="submit" name="delete_warga" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600" onclick="return confirm('Apakah Anda yakin?')">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="mt-4 flex justify-center mb-8">
        <?php if ($total_pages_warga > 0): ?>
            <div class="flex space-x-2">
                <?php if ($page_warga > 1): ?>
                    <a href="/PROJECT/manage_master_data?search=<?= urlencode($search) ?>&page_warga=<?= $page_warga - 1 ?>" class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages_warga; $i++): ?>
                    <a href="/PROJECT/manage_master_data?search=<?= urlencode($search) ?>&page_warga=<?= $i ?>" class="px-3 py-2 <?= $i == $page_warga ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' ?> rounded hover:bg-gray-300"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($page_warga < $total_pages_warga): ?>
                    <a href="/PROJECT/manage_master_data?search=<?= urlencode($search) ?>&page_warga=<?= $page_warga + 1 ?>" class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <h2 class="text-xl font-bold mt-8 mb-4">Data Kepala Keluarga</h2>
    <a href="tambah_kk.php" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 mb-4 inline-block">Tambah KK</a>

    <table class="w-full bg-white rounded shadow">
        <thead class="bg-yellow-100">
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Nama KK</th>
                <th class="px-4 py-2">Alamat</th>
                <th class="px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($k = mysqli_fetch_assoc($kk)) { ?>
            <tr class="border-t">
                <td class="px-4 py-2"><?php echo $k['id']; ?></td>
                <td class="px-4 py-2"><?php echo $k['nama_kk']; ?></td>
                <td class="px-4 py-2"><?php echo $k['alamat']; ?></td>
                <td class="px-4 py-2">
                    <a href="edit_kk.php?id=<?php echo $k['id']; ?>" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Edit</a>
                    <form method="POST" class="inline ml-2">
                        <input type="hidden" name="kk_id" value="<?php echo $k['id']; ?>">
                        <button type="submit" name="delete_kk" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600" onclick="return confirm('Apakah Anda yakin?')">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="mt-4 flex justify-center">
        <?php if ($total_pages_kk > 0): ?>
            <div class="flex space-x-2">
                <?php if ($page_kk > 1): ?>
                    <a href="/PROJECT/manage_master_data?search=<?= urlencode($search) ?>&page_kk=<?= $page_kk - 1 ?>" class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages_kk; $i++): ?>
                    <a href="/PROJECT/manage_master_data?search=<?= urlencode($search) ?>&page_kk=<?= $i ?>" class="px-3 py-2 <?= $i == $page_kk ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' ?> rounded hover:bg-gray-300"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($page_kk < $total_pages_kk): ?>
                    <a href="/PROJECT/manage_master_data?search=<?= urlencode($search) ?>&page_kk=<?= $page_kk + 1 ?>" class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
}
?>