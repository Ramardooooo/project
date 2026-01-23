<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
include '../../config/database.php';
include '../../layouts/admin/header.php';
include '../../layouts/admin/sidebar.php';

if ($_SESSION['role'] == 'admin') {
    $warga = mysqli_query($conn, "SELECT * FROM warga");
    $kk = mysqli_query($conn, "SELECT * FROM kk");

    if (isset($_POST['delete_warga'])) {
        $warga_id = $_POST['warga_id'];
        mysqli_query($conn, "DELETE FROM warga WHERE id=$warga_id");
        header("Location: manage_master_data.php");
        exit();
    }

    if (isset($_POST['delete_kk'])) {
        $kk_id = $_POST['kk_id'];
        mysqli_query($conn, "DELETE FROM kk WHERE id=$kk_id");
        header("Location: manage_master_data.php");
        exit();
    }
?>

<div class="ml-64 p-6">
<h1 class="text-2xl font-bold mb-6">Kelola Data Master</h1>

    <h2 class="text-xl font-bold mt-8 mb-4">Data Warga</h2>
    <a href="#" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mb-4 inline-block">Tambah Warga</a>

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
                    <a href="#" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Edit</a>
                    <form method="POST" class="inline ml-2">
                        <input type="hidden" name="warga_id" value="<?php echo $w['id']; ?>">
                        <button type="submit" name="delete_warga" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600" onclick="return confirm('Apakah Anda yakin?')">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <h2 class="text-xl font-bold mt-8 mb-4">Data Kepala Keluarga</h2>
    <a href="#" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mb-4 inline-block">Tambah KK</a>

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
                    <a href="#" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Edit</a>
                    <form method="POST" class="inline ml-2">
                        <input type="hidden" name="kk_id" value="<?php echo $k['id']; ?>">
                        <button type="submit" name="delete_kk" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600" onclick="return confirm('Apakah Anda yakin?')">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php
}
include '../../layouts/admin/footer.php';
?>
