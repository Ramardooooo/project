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
    $rt = mysqli_query($conn, "SELECT * FROM rt");
    $rw = mysqli_query($conn, "SELECT * FROM rw");

    if (isset($_POST['delete_rt'])) {
        $rt_id = $_POST['rt_id'];
        mysqli_query($conn, "DELETE FROM rt WHERE id=$rt_id");
        header("Location: manage_rt_rw.php");
        exit();
    }

    if (isset($_POST['delete_rw'])) {
        $rw_id = $_POST['rw_id'];
        mysqli_query($conn, "DELETE FROM rw WHERE id=$rw_id");
        header("Location: manage_rt_rw.php");
        exit();
    }
?>

<div class="ml-64 p-6">
<h1 class="text-2xl font-bold mb-6">Kelola Data Wilayah RT/RW</h1>

    <h2 class="text-xl font-bold mt-8 mb-4">Data RT</h2>
    <a href="#" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mb-4 inline-block">Tambah RT</a>

    <table class="w-full bg-white rounded shadow mb-8">
        <thead class="bg-yellow-100">
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Nama RT</th>
                <th class="px-4 py-2">Ketua RT</th>
                <th class="px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($r = mysqli_fetch_assoc($rt)) { ?>
            <tr class="border-t">
                <td class="px-4 py-2"><?php echo $r['id']; ?></td>
                <td class="px-4 py-2"><?php echo $r['nama_rt']; ?></td>
                <td class="px-4 py-2"><?php echo $r['ketua_rt']; ?></td>
                <td class="px-4 py-2">
                    <a href="#" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Edit</a>
                    <form method="POST" class="inline ml-2">
                        <input type="hidden" name="rt_id" value="<?php echo $r['id']; ?>">
                        <button type="submit" name="delete_rt" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600" onclick="return confirm('Apakah Anda yakin?')">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <h2 class="text-xl font-bold mt-8 mb-4">Data RW</h2>
    <a href="#" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mb-4 inline-block">Tambah RW</a>

    <table class="w-full bg-white rounded shadow">
        <thead class="bg-yellow-100">
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Nama RW</th>
                <th class="px-4 py-2">Ketua RW</th>
                <th class="px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($w = mysqli_fetch_assoc($rw)) { ?>
            <tr class="border-t">
                <td class="px-4 py-2"><?php echo $w['id']; ?></td>
                <td class="px-4 py-2"><?php echo $w['nama_rw']; ?></td>
                <td class="px-4 py-2"><?php echo $w['ketua_rw']; ?></td>
                <td class="px-4 py-2">
                    <a href="#" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Edit</a>
                    <form method="POST" class="inline ml-2">
                        <input type="hidden" name="rw_id" value="<?php echo $w['id']; ?>">
                        <button type="submit" name="delete_rw" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600" onclick="return confirm('Apakah Anda yakin?')">Hapus</button>
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
