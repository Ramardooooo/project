<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ketua') {
    header("Location: home");
    exit();
}

include '../../config/database.php';
include '../../layouts/ketua/header.php';
include '../../layouts/ketua/sidebar.php';

$total_warga = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM warga WHERE status = 'aktif'"))['total'];
$total_kk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kk"))['total'];
$total_rt = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM rt"))['total'];
$total_rw = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM rw"))['total'];

$laki_laki = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM warga WHERE jk = 'L' AND status = 'aktif'"))['total'];
$perempuan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM warga WHERE jk = 'P' AND status = 'aktif'"))['total'];

$mutasi_datang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM mutasi_warga WHERE jenis_mutasi = 'datang' AND tanggal_mutasi >= DATE_SUB(NOW(), INTERVAL 30 DAY)"))['total'];
$mutasi_pindah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM mutasi_warga WHERE jenis_mutasi = 'pindah'"))['total'];
$mutasi_meninggal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM mutasi_warga WHERE jenis_mutasi = 'meninggal'"))['total'];
?>

<div id="mainContent" class="ml-64 min-h-screen bg-white transition-all duration-300">
    <div class="p-8">
        <div class="mb-8">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Warga</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $total_warga; ?></p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-users text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total KK</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $total_kk; ?></p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-home text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total RT</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $total_rt; ?></p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-map text-2xl text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total RW</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $total_rw; ?></p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-building text-2xl text-yellow-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold mb-4 text-gray-800 flex items-center">
                    <i class="fas fa-venus-mars text-blue-500 mr-3"></i>
                    Statistik Jenis Kelamin
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-mars text-blue-600 mr-3"></i>
                            <span class="text-gray-700">Laki-laki</span>
                        </div>
                        <span class="font-bold text-blue-600 text-lg"><?php echo $laki_laki; ?></span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-venus text-pink-600 mr-3"></i>
                            <span class="text-gray-700">Perempuan</span>
                        </div>
                        <span class="font-bold text-pink-600 text-lg"><?php echo $perempuan; ?></span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold mb-4 text-gray-800 flex items-center">
                    <i class="fas fa-exchange-alt text-green-500 mr-3"></i>
                    Mutasi Warga (30 Hari Terakhir)
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-plus-circle text-green-600 mr-3"></i>
                            <span class="text-gray-700">Datang</span>
                        </div>
                        <span class="font-bold text-green-600 text-lg"><?php echo $mutasi_datang; ?></span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-minus-circle text-red-600 mr-3"></i>
                            <span class="text-gray-700">Pindah</span>
                        </div>
                        <span class="font-bold text-red-600 text-lg"><?php echo $mutasi_pindah; ?></span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-times-circle text-gray-600 mr-3"></i>
                            <span class="text-gray-700">Meninggal</span>
                        </div>
                        <span class="font-bold text-gray-600 text-lg"><?php echo $mutasi_meninggal; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <h3 class="text-xl font-semibold mb-6 text-gray-800 flex items-center">
                <i class="fas fa-rocket text-indigo-500 mr-3"></i>
                Akses Cepat
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <a href="manage_warga" class="group flex flex-col items-center p-4 bg-white rounded-xl border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-users text-3xl text-blue-600 mb-3 group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm font-semibold text-gray-700 text-center">Kelola Warga</span>
                </a>
                <a href="manage_kk" class="group flex flex-col items-center p-4 bg-white rounded-xl border border-gray-200 hover:border-green-300 hover:shadow-md transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-home text-3xl text-green-600 mb-3 group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm font-semibold text-gray-700 text-center">Kelola KK</span>
                </a>
                <a href="manage_wilayah" class="group flex flex-col items-center p-4 bg-white rounded-xl border border-gray-200 hover:border-purple-300 hover:shadow-md transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-map text-3xl text-purple-600 mb-3 group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm font-semibold text-gray-700 text-center">Kelola Wilayah</span>
                </a>
                <a href="mutasi_warga" class="group flex flex-col items-center p-4 bg-white rounded-xl border border-gray-200 hover:border-orange-300 hover:shadow-md transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-exchange-alt text-3xl text-orange-600 mb-3 group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm font-semibold text-gray-700 text-center">Mutasi Warga</span>
                </a>
                <a href="laporan" class="group flex flex-col items-center p-4 bg-white rounded-xl border border-gray-200 hover:border-red-300 hover:shadow-md transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-chart-bar text-3xl text-red-600 mb-3 group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm font-semibold text-gray-700 text-center">Laporan</span>
                </a>
                <a href="pengumuman" class="group flex flex-col items-center p-4 bg-white rounded-xl border border-gray-200 hover:border-indigo-300 hover:shadow-md transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-bullhorn text-3xl text-indigo-600 mb-3 group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm font-semibold text-gray-700 text-center">Pengumuman</span>
                </a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
