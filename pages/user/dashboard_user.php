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
$rt_id = null;
$rw_id = null;

if ($warga && isset($warga['rt']) && isset($warga['rw'])) {
    $rt_id = $warga['rt'];
    $rw_id = $warga['rw'];
    
    $rt_rw_names_query = "SELECT rt.nama_rt, rw.name as nama_rw FROM rt JOIN rw ON rt.id_rw = rw.id WHERE rt.id = '$rt_id' AND rw.id = '$rw_id'";
    $rt_rw_names_result = mysqli_query($conn, $rt_rw_names_query);
    $rt_rw_names = mysqli_fetch_assoc($rt_rw_names_result);
    if ($rt_rw_names) {
        $rt_name = $rt_rw_names['nama_rt'];
        $rw_name = $rt_rw_names['nama_rw'];
    }
}

$personal_query = "SELECT nik, alamat, tanggal_lahir, jk FROM warga WHERE nama = '$nama'";
$personal_result = mysqli_query($conn, $personal_query);
$personal = mysqli_fetch_assoc($personal_result);

$warga_list = [];
if ($rt_id && $rw_id) {
    $warga_list_query = "SELECT nama, nik, alamat, jk, tanggal_lahir FROM warga WHERE rt = '$rt_id' AND rw = '$rw_id' ORDER BY nama ASC";
    $warga_list_result = mysqli_query($conn, $warga_list_query);
    $warga_list = mysqli_fetch_all($warga_list_result, MYSQLI_ASSOC);
}

$total_warga = count($warga_list);

$laki_laki = 0;
$perempuan = 0;
foreach ($warga_list as $w) {
    if ($w['jk'] === 'L') $laki_laki++;
    if ($w['jk'] === 'P') $perempuan++;
}

$announcements_query = "SELECT title, content, created_at FROM announcements ORDER BY created_at DESC LIMIT 5";
$announcements_result = mysqli_query($conn, $announcements_query);
$announcements = mysqli_fetch_all($announcements_result, MYSQLI_ASSOC);

$gallery_query = "SELECT title, image_path FROM gallery ORDER BY created_at DESC LIMIT 6";
$gallery_result = mysqli_query($conn, $gallery_query);
$gallery_images = mysqli_fetch_all($gallery_result, MYSQLI_ASSOC);

?>
<div id="mainContent" class="ml-64 min-h-screen bg-gray-50">
    <div class="p-8">
        
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 rounded-2xl p-8 mb-8 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Selamat Datang, <?php echo htmlspecialchars($nama); ?>!</h1>
                    <p class="text-blue-100">Dashboard Warga - RT <?php echo $rt_name; ?> / RW <?php echo $rw_name; ?></p>
                </div>
                <div class="hidden md:block">
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-home text-4xl text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow p-5 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Nama Lengkap</p>
                        <p class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($nama); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow p-5 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">No. KK</p>
                        <p class="text-xl font-bold text-gray-800"><?php echo $kk ? htmlspecialchars($kk['no_kk']) : '-'; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-id-card text-green-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow p-5 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">RT / RW</p>
                        <p class="text-xl font-bold text-gray-800"><?php echo $rt_name; ?> / <?php echo $rw_name; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-purple-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow p-5 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Warga</p>
                        <p class="text-xl font-bold text-gray-800"><?php echo $total_warga; ?> Orang</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Demographics -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-pie text-blue-500 mr-2"></i>
                    Komposisi Jenis Kelamin
                </h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600">Laki-laki</span>
                            <span class="text-sm font-semibold text-gray-800"><?php echo $laki_laki; ?> orang</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: <?php echo $total_warga > 0 ? ($laki_laki / $total_warga * 100) : 0; ?>%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600">Perempuan</span>
                            <span class="text-sm font-semibold text-gray-800"><?php echo $perempuan; ?> orang</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-pink-500 h-2 rounded-full" style="width: <?php echo $total_warga > 0 ? ($perempuan / $total_warga * 100) : 0; ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>

<div class="bg-white rounded-xl shadow-md p-6 lg:col-span-2">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user-circle text-green-500 mr-2"></i>
                    Data Diri Warga
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">NIK</p>
                        <p class="text-sm font-semibold text-gray-800"><?php echo $personal ? htmlspecialchars($personal['nik']) : '-'; ?></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Tanggal Lahir</p>
                        <p class="text-sm font-semibold text-gray-800"><?php echo $personal && $personal['tanggal_lahir'] ? date('d-m-Y', strtotime($personal['tanggal_lahir'])) : '-'; ?></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Jenis Kelamin</p>
                        <p class="text-sm font-semibold text-gray-800"><?php echo $personal && $personal['jk'] === 'L' ? 'Laki-laki' : ($personal['jk'] === 'P' ? 'Perempuan' : '-'); ?></p>
                    </div>
                </div>
                <div class="mt-4 bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Alamat</p>
                    <p class="text-sm font-semibold text-gray-800"><?php echo $personal ? htmlspecialchars($personal['alamat'] ?? '-') : '-'; ?></p>
                </div>
            </div>
        </div>

        <!-- Warga Table -->
        <div class="bg-white rounded-xl shadow-md mb-8">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-users text-indigo-500 mr-2"></i>
                    Daftar Warga RT <?php echo $rt_name; ?> / RW <?php echo $rw_name; ?>
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
<tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">JK</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Lahir</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if ($warga_list && count($warga_list) > 0): $no = 1; ?>
                            <?php foreach ($warga_list as $warga_item): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $no++; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($warga_item['nama']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($warga_item['nik'] ?? '-'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php if ($warga_item['jk'] === 'L'): ?>
                                            <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">Laki-laki</span>
                                        <?php elseif ($warga_item['jk'] === 'P'): ?>
                                            <span class="px-2 py-1 text-xs font-semibold bg-pink-100 text-pink-800 rounded-full">Perempuan</span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $warga_item['tanggal_lahir'] ? date('d-m-Y', strtotime($warga_item['tanggal_lahir'])) : '-'; ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500"><?php echo htmlspecialchars($warga_item['alamat'] ?? '-'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada data warga.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Announcements -->
        <div class="bg-white rounded-xl shadow-md mb-8">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-bullhorn text-red-500 mr-2"></i>
                    Pengumuman Terbaru
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <?php if ($announcements && count($announcements) > 0): ?>
                    <?php foreach ($announcements as $announcement): ?>
                        <div class="border-l-4 border-red-400 bg-red-50 rounded-r-lg p-4 hover:shadow-md transition-shadow">
                            <h4 class="font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($announcement['title']); ?></h4>
                            <p class="text-sm text-gray-600 mb-2"><?php echo htmlspecialchars(substr($announcement['content'], 0, 100)) . (strlen($announcement['content']) > 100 ? '...' : ''); ?></p>
                            <p class="text-xs text-gray-400"><i class="far fa-calendar-alt mr-1"></i><?php echo date('d M Y', strtotime($announcement['created_at'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-500 text-center py-8">Tidak ada pengumuman.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Access -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                Akses Cepat
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="../../account/settings" class="group flex items-center p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl hover:shadow-lg transition-all duration-300 border border-blue-100">
                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-cog text-white text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">Pengaturan</h4>
                        <p class="text-sm text-gray-500">Kelola akun</p>
                    </div>
                </a>
                <a href="../../beranda/gallery" class="group flex items-center p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl hover:shadow-lg transition-all duration-300 border border-green-100">
                    <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-images text-white text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">Galeri</h4>
                        <p class="text-sm text-gray-500">Lihat foto desa</p>
                    </div>
                </a>
                <a href="../../beranda/announcements" class="group flex items-center p-4 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl hover:shadow-lg transition-all duration-300 border border-purple-100">
                    <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-bullhorn text-white text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">Pengumuman</h4>
                        <p class="text-sm text-gray-500">Info terbaru</p>
                    </div>
                </a>
            </div>
        </div>

    </div>
</div>
