<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: home");
    exit();
}

include '../../config/database.php';
include '../../layouts/user/header.php';
include '../../layouts/user/sidebar.php';

$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

$nama = $user['nama'] ?? '';
$kk_query = "SELECT * FROM kk WHERE kepala_keluaraga = '$nama'";
$kk_result = mysqli_query($conn, $kk_query);
$kk = mysqli_fetch_assoc($kk_result);

$warga_query = "SELECT rt, rw FROM warga WHERE nama = '$nama'";
$warga_result = mysqli_query($conn, $warga_query);
$warga = mysqli_fetch_assoc($warga_result);

$rt_name = 'Belum ada';
$rw_name = 'Belum ada';
if ($warga) {
    $rt_rw_names_query = "SELECT rt.nama_rt, rw.name as nama_rw FROM rt JOIN rw ON rt.id_rw = rw.id WHERE rt.id = '{$warga['rt']}' AND rw.id = '{$warga['rw']}'";
    $rt_rw_names_result = mysqli_query($conn, $rt_rw_names_query);
    $rt_rw_names = mysqli_fetch_assoc($rt_rw_names_result);
    if ($rt_rw_names) {
        $rt_name = $rt_rw_names['nama_rt'];
        $rw_name = $rt_rw_names['nama_rw'];
    }
}

$personal_query = "SELECT nik, alamat FROM warga WHERE nama = '$nama'";
$personal_result = mysqli_query($conn, $personal_query);
$personal = mysqli_fetch_assoc($personal_result);

$warga_list_query = "SELECT nama, nik, alamat, jk FROM warga WHERE rt = '{$warga['rt']}' AND rw = '{$warga['rw']}'";
$warga_list_result = mysqli_query($conn, $warga_list_query);
$warga_list = mysqli_fetch_all($warga_list_result, MYSQLI_ASSOC);

$announcements_query = "SELECT title, content, created_at FROM announcements ORDER BY created_at DESC LIMIT 3";
$announcements_result = mysqli_query($conn, $announcements_query);
$announcements = mysqli_fetch_all($announcements_result, MYSQLI_ASSOC);

$gallery_query = "SELECT title, image_path FROM gallery ORDER BY created_at DESC LIMIT 4";
$gallery_result = mysqli_query($conn, $gallery_query);
$gallery_images = mysqli_fetch_all($gallery_result, MYSQLI_ASSOC);


?>
<div id="mainContent" class="ml-64 min-h-screen bg-blue-900">
<div class="p-8">

<h1 class="text-4xl font-extrabold mb-8 text-white drop-shadow-lg">Dashboard User</h1>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-200 hover:bg-gray-50">
        <div class="flex items-center">
            <div class="p-4 rounded-full bg-blue-600 text-white shadow-md">
                <i class="fas fa-user text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-800 uppercase tracking-wide drop-shadow-sm">Nama</p>
                <p class="text-2xl font-bold text-black drop-shadow-lg"><?php echo $nama; ?></p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-200 hover:bg-gray-50">
        <div class="flex items-center">
            <div class="p-4 rounded-full bg-green-600 text-white shadow-md">
                <i class="fas fa-home text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-800 uppercase tracking-wide drop-shadow-sm">KK</p>
                <p class="text-2xl font-bold text-black drop-shadow-lg"><?php echo $kk ? $kk['no_kk'] : 'Belum ada'; ?></p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-200 hover:bg-gray-50">
        <div class="flex items-center">
            <div class="p-4 rounded-full bg-purple-600 text-white shadow-md">
                <i class="fas fa-map-marker-alt text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-800 uppercase tracking-wide drop-shadow-sm">RT</p>
                <p class="text-2xl font-bold text-black drop-shadow-lg"><?php echo $rt_name; ?></p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-200 hover:bg-gray-50">
        <div class="flex items-center">
            <div class="p-4 rounded-full bg-yellow-600 text-white shadow-md">
                <i class="fas fa-building text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-800 uppercase tracking-wide drop-shadow-sm">RW</p>
                <p class="text-2xl font-bold text-black drop-shadow-lg"><?php echo $rw_name; ?></p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg p-6 mb-10 border border-gray-200">
    <h3 class="text-xl font-bold mb-6 text-black drop-shadow-lg">Informasi Pribadi</h3>
    <div class="space-y-4">
        <div class="p-4 bg-blue-50 border-l-4 border-blue-400 text-blue-800 rounded-r-lg shadow-sm">
            <p class="font-medium">NIK: <?php echo $personal ? $personal['nik'] : 'Belum ada'; ?></p>
        </div>
        <div class="p-4 bg-green-50 border-l-4 border-green-400 text-green-800 rounded-r-lg shadow-sm">
            <p class="font-medium">Alamat: <?php echo $personal ? $personal['alamat'] : 'Belum ada'; ?></p>
        </div>
        <div class="p-4 bg-purple-50 border-l-4 border-purple-400 text-purple-800 rounded-r-lg shadow-sm">
            <p class="font-medium">No. HP: <?php echo $user['no_hp']; ?></p>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg p-6 mb-10 border border-gray-200">
    <h3 class="text-xl font-bold mb-6 text-black drop-shadow-lg">Daftar Warga RT/RW</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">JK</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if ($warga_list && count($warga_list) > 0): ?>
                    <?php foreach ($warga_list as $warga_item): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($warga_item['nama']); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($warga_item['nik'] ?? 'Belum ada'); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($warga_item['jk'] ?? 'Belum ada'); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($warga_item['alamat'] ?? 'Belum ada'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-center text-sm text-gray-500">Tidak ada data warga di RT/RW ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg p-6 mb-10 border border-gray-200">
    <h3 class="text-xl font-bold mb-6 text-black drop-shadow-lg">Pengumuman Terbaru</h3>
    <div class="space-y-4">
        <?php if ($announcements && count($announcements) > 0): ?>
            <?php foreach ($announcements as $announcement): ?>
                <div class="p-4 bg-blue-50 border-l-4 border-blue-400 text-blue-800 rounded-r-lg shadow-sm">
                    <h4 class="font-bold text-lg"><?php echo htmlspecialchars($announcement['title']); ?></h4>
                    <p class="text-sm mt-2"><?php echo htmlspecialchars(substr($announcement['content'], 0, 150)) . (strlen($announcement['content']) > 150 ? '...' : ''); ?></p>
                    <p class="text-xs mt-2 text-blue-600"><?php echo date('d M Y', strtotime($announcement['created_at'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-gray-500">Tidak ada pengumuman terbaru.</p>
        <?php endif; ?>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg p-6 mb-10 border border-gray-200">
    <h3 class="text-xl font-bold mb-6 text-black drop-shadow-lg">Galeri Terbaru</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <?php if ($gallery_images && count($gallery_images) > 0): ?>
            <?php foreach ($gallery_images as $image): ?>
                <div class="group relative overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="<?php echo htmlspecialchars($image['title']); ?>" class="w-full h-32 object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-300 flex items-center justify-center">
                        <p class="text-white font-medium opacity-0 group-hover:opacity-100 transition-opacity duration-300"><?php echo htmlspecialchars($image['title']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-gray-500 col-span-full text-center">Tidak ada gambar galeri.</p>
        <?php endif; ?>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg p-6 mb-10 border border-gray-200">
    <h4 class="text-xl font-bold mb-4 text-black drop-shadow-lg">Akses Cepat</h4>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="profile" class="group p-6 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all duration-300 border border-gray-200 hover:border-gray-300 hover:shadow-lg">
            <i class="fas fa-user text-gray-600 text-3xl mb-3 group-hover:scale-110 transition-transform"></i>
            <h4 class="font-bold text-black mb-2 drop-shadow-sm">Profile</h4>
            <p class="text-sm text-gray-600">Kelola data pribadi</p>
        </a>
        <a href="settings" class="group p-6 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all duration-300 border border-gray-200 hover:border-gray-300 hover:shadow-lg">
            <i class="fas fa-cog text-gray-600 text-3xl mb-3 group-hover:scale-110 transition-transform"></i>
            <h4 class="font-bold text-black mb-2 drop-shadow-sm">Pengaturan</h4>
            <p class="text-sm text-gray-600">Ubah preferensi akun</p>
        </a>
        <a href="gallery" class="group p-6 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all duration-300 border border-gray-200 hover:border-gray-300 hover:shadow-lg">
            <i class="fas fa-images text-gray-600 text-3xl mb-3 group-hover:scale-110 transition-transform"></i>
            <h4 class="font-bold text-black mb-2 drop-shadow-sm">Galeri</h4>
            <p class="text-sm text-gray-600">Lihat galeri desa</p>
        </a>
    </div>
</div>

</div>
</div>
