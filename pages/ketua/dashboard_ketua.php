<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ketua') {
    header("Location: home");
    exit();
}

include '../../config/database.php';
include '../../layouts/ketua/header.php';
include '../../layouts/ketua/sidebar.php';

// Get user info
$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['username'] ?? 'Chairman';

// Statistics Queries - Total Warga (semua status)
$total_warga = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM warga"))['total'];
$total_kk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kk"))['total'];
$total_rt = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM rt"))['total'];
$total_rw = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM rw"))['total'];

// Gender statistics
$laki_laki = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM warga WHERE jk = 'L' AND status = 'aktif'"))['total'];
$perempuan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM warga WHERE jk = 'P' AND status = 'aktif'"))['total'];

// Mutasi statistics (30 days)
$mutasi_datang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM mutasi_warga WHERE jenis_mutasi = 'datang' AND tanggal_mutasi >= DATE_SUB(NOW(), INTERVAL 30 DAY)"))['total'];
$mutasi_pindah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM mutasi_warga WHERE jenis_mutasi = 'pindah' AND tanggal_mutasi >= DATE_SUB(NOW(), INTERVAL 30 DAY)"))['total'];
$mutasi_meninggal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM mutasi_warga WHERE jenis_mutasi = 'meninggal' AND tanggal_mutasi >= DATE_SUB(NOW(), INTERVAL 30 DAY)"))['total'];

// Additional statistics - using available columns only
// $warga_baru_bulan_ini removed - no created_at column in warga table

// Get recent activities
$recent_activities = mysqli_query($conn, "SELECT * FROM activities ORDER BY created_at DESC LIMIT 5");

// Get recent announcements
$recent_announcements = mysqli_query($conn, "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 3");

// Get recent warga additions - using available columns
$recent_warga = mysqli_query($conn, "SELECT nama, jk FROM warga ORDER BY id DESC LIMIT 5");

// Calculate percentages for gender chart
$total_jk = $laki_laki + $perempuan;
$persen_laki = $total_jk > 0 ? round(($laki_laki / $total_jk) * 100) : 0;
$persen_perempuan = $total_jk > 0 ? round(($perempuan / $total_jk) * 100) : 0;

// Get notifications count
$notif_belum_dibaca = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM notifications WHERE is_read = 0"))['total'];

// Get warga status distribution
$warga_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM warga WHERE status = 'aktif'"))['total'];
$warga_tidak_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM warga WHERE status = 'tidak_aktif'"))['total'];
$warga_meninggal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM warga WHERE status = 'meninggal'"))['total'];
$warga_pindah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM warga WHERE status = 'pindah'"))['total'];

// Get latest mutasi (preview)
$latest_mutasi = mysqli_query($conn, "SELECT mw.*, COALESCE(mw.nama_warga, w.nama) as nama_warga, w.nik FROM mutasi_warga mw LEFT JOIN warga w ON mw.warga_id = w.id ORDER BY mw.tanggal_mutasi DESC LIMIT 5");

// Get gallery count
$total_gallery = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM gallery"))['total'];

// Default values to avoid undefined variable errors
$warga_baru_bulan_ini = 0;
?>

<style>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes slideInLeft {
    from { opacity: 0; transform: translateX(-30px); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}
.animate-fade-in { animation: fadeInUp 0.6s ease-out forwards; }
.animate-slide-left { animation: slideInLeft 0.6s ease-out forwards; }
.glass-effect {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
}
.card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}
.stat-card {
    transition: all 0.3s ease;
}
.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
}
.gradient-blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.gradient-green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.gradient-purple { background: linear-gradient(135deg, #7F7FD5 0%, #86A8E7 100%); }
.gradient-orange { background: linear-gradient(135deg, #f12711 0%, #f5af19 100%); }
.gradient-pink { background: linear-gradient(135deg, #ec008c 0%, #fc6767 100%); }
.gradient-teal { background: linear-gradient(135deg, #0ba360 0%, #3cba92 100%); }
.gradient-red { background: linear-gradient(135deg, #cb2d3e 0%, #ef473a 100%); }
.gradient-indigo { background: linear-gradient(135deg, #5B86E5 0%, #36D1DC 100%); }
.progress-bar {
    transition: width 1s ease-in-out;
}
</style>

<div id="mainContent" class="ml-64 min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 transition-all duration-300">
    <div class="p-8">
        

        <!-- Main Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card bg-white rounded-2xl shadow-lg p-6 border-l-4 border-blue-500 animate-fade-in" style="animation-delay: 0.1s">
                <div class="flex items-center justify-between">
                    <div>
<p class="text-gray-500 text-sm font-medium">Total Warga</p>
                        <p class="text-4xl font-bold text-gray-800 mt-2"><?php echo number_format($total_warga); ?></p>
                        <p class="text-green-500 text-sm mt-2 font-semibold">
                            <i class="fas fa-arrow-up mr-1"></i>+<?php echo $warga_baru_bulan_ini; ?> bulan ini
                        </p>
                    </div>
                    <div class="w-16 h-16 gradient-blue rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-users text-3xl text-white"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500 animate-fade-in" style="animation-delay: 0.2s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Kepala Keluarga</p>
                        <p class="text-4xl font-bold text-gray-800 mt-2"><?php echo number_format($total_kk); ?></p>
                        <p class="text-blue-500 text-sm mt-2 font-semibold">
                            <i class="fas fa-home mr-1"></i>KK Terdaftar
                        </p>
                    </div>
                    <div class="w-16 h-16 gradient-green rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-house-user text-3xl text-white"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white rounded-2xl shadow-lg p-6 border-l-4 border-purple-500 animate-fade-in" style="animation-delay: 0.3s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total RT</p>
                        <p class="text-4xl font-bold text-gray-800 mt-2"><?php echo number_format($total_rt); ?></p>
                        <p class="text-purple-500 text-sm mt-2 font-semibold">
                            <i class="fas fa-map-marker-alt mr-1"></i>RT Aktif
                        </p>
                    </div>
                    <div class="w-16 h-16 gradient-purple rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-building text-3xl text-white"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white rounded-2xl shadow-lg p-6 border-l-4 border-orange-500 animate-fade-in" style="animation-delay: 0.4s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total RW</p>
                        <p class="text-4xl font-bold text-gray-800 mt-2"><?php echo number_format($total_rw); ?></p>
                        <p class="text-orange-500 text-sm mt-2 font-semibold">
                            <i class="fas fa-layer-group mr-1"></i>RW Aktif
                        </p>
                    </div>
                    <div class="w-16 h-16 gradient-orange rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-city text-3xl text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Stats Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Gender Statistics -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-venus-mars text-blue-600"></i>
                    </div>
                    Statistik Jenis Kelamin
                </h3>
                
                <div class="space-y-4">
                    <div class="relative pt-1">
                        <div class="flex mb-2 items-center justify-between">
                            <div class="text-left">
                                <span class="text-xs font-semibold inline-block text-blue-600">
                                    <i class="fas fa-mars mr-1"></i>Laki-laki
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-semibold inline-block text-blue-600"><?php echo $laki_laki; ?> (<?php echo $persen_laki; ?>%)</span>
                            </div>
                        </div>
                        <div class="overflow-hidden h-3 mb-4 text-xs flex rounded bg-blue-100">
                            <div style="width: <?php echo $persen_laki; ?>%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-500"></div>
                        </div>
                    </div>
                    
                    <div class="relative pt-1">
                        <div class="flex mb-2 items-center justify-between">
                            <div class="text-left">
                                <span class="text-xs font-semibold inline-block text-pink-600">
                                    <i class="fas fa-venus mr-1"></i>Perempuan
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-semibold inline-block text-pink-600"><?php echo $perempuan; ?> (<?php echo $persen_perempuan; ?>%)</span>
                            </div>
                        </div>
                        <div class="overflow-hidden h-3 mb-4 text-xs flex rounded bg-pink-100">
                            <div style="width: <?php echo $persen_perempuan; ?>%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-pink-500 transition-all duration-500"></div>
                        </div>
                    </div>
                </div>

                <!-- Visual representation -->
                <div class="flex items-center justify-center mt-6">
                    <div class="flex space-x-8">
                        <div class="text-center">
                            <div class="w-20 h-20 rounded-full bg-blue-500 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                                <?php echo $laki_laki; ?>
                            </div>
                            <p class="text-gray-500 text-sm mt-2">Laki-laki</p>
                        </div>
                        <div class="text-center">
                            <div class="w-20 h-20 rounded-full bg-pink-500 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                                <?php echo $perempuan; ?>
                            </div>
                            <p class="text-gray-500 text-sm mt-2">Perempuan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mutasi Statistics -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-exchange-alt text-green-600"></i>
                    </div>
                    Mutasi Warga (30 Hari)
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-green-50 to-green-100 border border-green-200">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                                <i class="fas fa-arrow-right text-white text-lg"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Pendatang Baru</p>
                                <p class="text-sm text-gray-500">Warga yang pindah masuk</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-green-600"><?php echo $mutasi_datang; ?></p>
                            <p class="text-xs text-green-500">orang</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-red-50 to-red-100 border border-red-200">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                                <i class="fas fa-arrow-left text-white text-lg"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Pindah Keluar</p>
                                <p class="text-sm text-gray-500">Warga yang pindah</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-red-600"><?php echo $mutasi_pindah; ?></p>
                            <p class="text-xs text-red-500">orang</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-200">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gray-500 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                                <i class="fas fa-times text-white text-lg"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Meninggal</p>
                                <p class="text-sm text-gray-500">Warga yang wafat</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-600"><?php echo $mutasi_meninggal; ?></p>
                            <p class="text-xs text-gray-500">orang</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Distribution and Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Warga Status -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-chart-pie text-indigo-600"></i>
                    </div>
                    Status Warga
                </h3>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-lg bg-green-50">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-gray-700">Aktif</span>
                        </div>
                        <span class="font-bold text-green-600"><?php echo $warga_aktif; ?></span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-yellow-50">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                            <span class="text-gray-700">Tidak Aktif</span>
                        </div>
                        <span class="font-bold text-yellow-600"><?php echo $warga_tidak_aktif; ?></span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-gray-500 rounded-full mr-3"></div>
                            <span class="text-gray-700">Meninggal</span>
                        </div>
                        <span class="font-bold text-gray-600"><?php echo $warga_meninggal; ?></span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-red-50">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                            <span class="text-gray-700">Pindah</span>
                        </div>
                        <span class="font-bold text-red-600"><?php echo $warga_pindah; ?></span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 lg:col-span-2">
                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-bolt text-yellow-600"></i>
                    </div>
                    Akses Cepat
                </h3>
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <a href="manage_warga" class="group flex flex-col items-center p-4 bg-gradient-to-b from-blue-50 to-blue-100 rounded-xl border border-blue-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mb-3 shadow-md group-hover:scale-110 transition-transform">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-700 text-center">Kelola Warga</span>
                    </a>
                    
                    <a href="manage_kk" class="group flex flex-col items-center p-4 bg-gradient-to-b from-green-50 to-green-100 rounded-xl border border-green-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center mb-3 shadow-md group-hover:scale-110 transition-transform">
                            <i class="fas fa-home text-white text-xl"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-700 text-center">Kelola KK</span>
                    </a>
                    
                    <a href="manage_wilayah" class="group flex flex-col items-center p-4 bg-gradient-to-b from-purple-50 to-purple-100 rounded-xl border border-purple-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mb-3 shadow-md group-hover:scale-110 transition-transform">
                            <i class="fas fa-map text-white text-xl"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-700 text-center">Kelola Wilayah</span>
                    </a>
                    
                    <a href="mutasi_warga" class="group flex flex-col items-center p-4 bg-gradient-to-b from-orange-50 to-orange-100 rounded-xl border border-orange-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center mb-3 shadow-md group-hover:scale-110 transition-transform">
                            <i class="fas fa-exchange-alt text-white text-xl"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-700 text-center">Mutasi Warga</span>
                    </a>
                    
                    <a href="laporan" class="group flex flex-col items-center p-4 bg-gradient-to-b from-red-50 to-red-100 rounded-xl border border-red-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center mb-3 shadow-md group-hover:scale-110 transition-transform">
                            <i class="fas fa-chart-bar text-white text-xl"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-700 text-center">Laporan</span>
                    </a>
                    
                    <a href="pengumuman" class="group flex flex-col items-center p-4 bg-gradient-to-b from-indigo-50 to-indigo-100 rounded-xl border border-indigo-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        <div class="w-12 h-12 bg-indigo-500 rounded-xl flex items-center justify-center mb-3 shadow-md group-hover:scale-110 transition-transform">
                            <i class="fas fa-bullhorn text-white text-xl"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-700 text-center">Pengumuman</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Data Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Recent Activities -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-history text-blue-600"></i>
                    </div>
                    Aktivitas Terbaru
                </h3>
                
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    <?php if (mysqli_num_rows($recent_activities) > 0): ?>
                        <?php while ($activity = mysqli_fetch_assoc($recent_activities)): ?>
                            <div class="flex items-start p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="fas fa-circle text-blue-600 text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate"><?php echo htmlspecialchars($activity['description'] ?? 'Aktivitas'); ?></p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <span class="bg-blue-100 text-blue-600 px-2 py-0.5 rounded"><?php echo htmlspecialchars($activity['action']); ?></span>
                                        <span class="ml-2"><?php echo date('d/m H:i', strtotime($activity['created_at'])); ?></span>
                                    </p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                            <p>Belum ada aktivitas</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Announcements -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-bullhorn text-red-600"></i>
                    </div>
                    Pengumuman Terbaru
                </h3>
                
                <div class="space-y-3">
                    <?php if (mysqli_num_rows($recent_announcements) > 0): ?>
                        <?php while ($announcement = mysqli_fetch_assoc($recent_announcements)): ?>
                            <div class="p-4 rounded-xl bg-gradient-to-r from-red-50 to-red-100 border border-red-200 hover:shadow-md transition-shadow">
                                <h4 class="font-semibold text-gray-800 mb-1"><?php echo htmlspecialchars($announcement['title']); ?></h4>
                                <p class="text-sm text-gray-600 line-clamp-2"><?php echo htmlspecialchars($announcement['content']); ?></p>
                                <p class="text-xs text-gray-400 mt-2">
                                    <i class="fas fa-clock mr-1"></i><?php echo date('d F Y', strtotime($announcement['created_at'])); ?>
                                </p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-bullhorn text-4xl mb-3 text-gray-300"></i>
                            <p>Belum ada pengumuman</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Latest Mutasi -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-list text-green-600"></i>
                </div>
                Riwayat Mutasi Terbaru
            </h3>
            

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Nama Warga</th>
                            <th class="px-6 py-3">Jenis Mutasi</th>
                    <tbody>
                        <?php if (mysqli_num_rows($latest_mutasi) > 0): ?>
                            <?php while ($mutasi = mysqli_fetch_assoc($latest_mutasi)): ?>
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900"><?php echo htmlspecialchars($mutasi['nama_warga']); ?></td>
                                    <td class="px-6 py-4">
                                        <?php 
                                        $badge_class = '';
                                        $icon = '';
                                        if($mutasi['jenis_mutasi'] == 'datang') {
                                            $badge_class = 'bg-green-100 text-green-800';
                                            $icon = '<i class="fas fa-arrow-right mr-1"></i>';
                                        } elseif($mutasi['jenis_mutasi'] == 'pindah') {
                                            $badge_class = 'bg-red-100 text-red-800';
                                            $icon = '<i class="fas fa-arrow-left mr-1"></i>';
                                        } else {
                                            $badge_class = 'bg-gray-100 text-gray-800';
                                            $icon = '<i class="fas fa-times mr-1"></i>';
                                        }
                                        ?>
                                        <span class="px-3 py-1 rounded-full text-xs font-medium <?php echo $badge_class; ?>">
                                            <?php echo $icon; ?><?php echo ucfirst($mutasi['jenis_mutasi']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4"><?php echo date('d/m/Y', strtotime($mutasi['tanggal_mutasi'])); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($mutasi['keterangan'] ?? '-'); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                                    <p>Belum ada data mutasi</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
</body>
</html>

</body>
</html>

