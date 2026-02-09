<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: home");
    exit();
}

include '../../config/database.php';
include '../../layouts/admin/header.php';
include '../../layouts/admin/sidebar.php';

if ($_SESSION['role'] === 'admin') {

    $queries = [
        'warga' => "SELECT COUNT(*) total FROM warga",
        'kk'    => "SELECT COUNT(*) total FROM kk",
        'rt'    => "SELECT COUNT(*) total FROM rt",
        'rw'    => "SELECT COUNT(*) total FROM rt",
        'users' => "SELECT COUNT(*) total FROM users"
    ];

    foreach ($queries as $k => $q) {
        $data[$k] = mysqli_fetch_assoc(mysqli_query($conn, $q))['total'];
    }

    $stats = [
        ['Total Warga', $data['warga'], 'users', 'blue'],
        ['Total KK', $data['kk'], 'home', 'green'],
        ['Total RT', $data['rt'], 'map-marker-alt', 'purple'],
        ['Total RW', $data['rw'], 'building', 'yellow'],
        ['Total Users', $data['users'], 'user', 'red'],
    ];

    $days = [];
    $traffic = [];
    $indonesian_days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $day_num = date('w', strtotime($date));
        $days[] = $indonesian_days[$day_num];
        $query = "SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = '$date'";
        $result = mysqli_query($conn, $query);
        $count = mysqli_fetch_assoc($result)['count'];
        $traffic[] = $count;
    }
} else {
    header("Location: home");
    exit();
}
?>
<div id="mainContent" class="ml-64 min-h-screen bg-blue-900">
<div class="p-8">

<!-- Welcome Section -->
<div class="bg-white rounded-3xl p-8 mb-8 border border-blue-200 shadow-2xl">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-extrabold text-blue-900 mb-2">Selamat Datang, Admin!</h1>
            <p class="text-blue-700 text-lg">Kelola sistem Lurahgo.id dengan mudah dan efisien</p>
            <div class="flex items-center mt-4 space-x-4">
                <div class="bg-blue-100 px-3 py-1 rounded-full text-blue-800 text-sm font-medium">
                    <i class="fas fa-calendar-alt mr-1"></i><?php echo date('l, d F Y'); ?>
                </div>
                <div class="bg-green-100 px-3 py-1 rounded-full text-green-800 text-sm font-medium">
                    <i class="fas fa-clock mr-1"></i><?php echo date('H:i'); ?> WIB
                </div>
            </div>
        </div>
        <div class="hidden md:block">
            <i class="fas fa-crown text-yellow-400 text-6xl animate-pulse"></i>
        </div>
    </div>
</div>

<h1 class="text-3xl font-bold mb-8 text-white drop-shadow-lg">Dashboard Overview</h1>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">
<?php foreach ($stats as $s): ?>
<div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-200 hover:bg-gray-50">
    <div class="flex items-center">
        <div class="p-4 rounded-full bg-<?= $s[3] ?>-600 text-white shadow-md">
            <i class="fas fa-<?= $s[2] ?> text-2xl"></i>
        </div>
        <div class="ml-4">
            <p class="text-sm font-medium text-gray-800 uppercase tracking-wide drop-shadow-sm"><?= $s[0] ?></p>
            <p class="text-3xl font-bold text-black drop-shadow-lg"><?= $s[1] ?></p>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
<div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-black drop-shadow-lg flex items-center">
                <i class="fas fa-history text-blue-600 mr-3"></i>Audit Log
            </h3>
            <a href="audit_log" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-2 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                <i class="fas fa-download mr-2"></i>Export .txt
            </a>
        </div>
        <div class="max-h-64 overflow-y-auto space-y-2">
            <?php
            $audit_logs = mysqli_query($conn, "SELECT action, table_name, username, created_at FROM audit_log ORDER BY created_at DESC LIMIT 10");
            while ($log = mysqli_fetch_assoc($audit_logs)) {
                echo "<div class='flex items-center justify-between p-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors shadow-sm text-sm'>";
                echo "<span class='font-medium text-gray-700'>{$log['action']} ({$log['table_name']}) oleh {$log['username']}</span>";
                echo "<span class='text-xs text-gray-500'>" . date('d/m/Y H:i', strtotime($log['created_at'])) . "</span>";
                echo "</div>";
            }
            if (mysqli_num_rows($audit_logs) == 0) {
                echo "<div class='p-2 bg-gray-50 rounded-lg text-gray-500 text-sm'>Belum ada aktivitas audit.</div>";
            }
            ?>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
        <h3 class="text-xl font-bold mb-6 text-black drop-shadow-lg flex items-center">
            <i class="fas fa-bell text-orange-600 mr-3"></i>Notifikasi Sistem
        </h3>
        <div class="space-y-4">
            <div class="p-4 bg-gradient-to-r from-yellow-50 to-yellow-100 border-l-4 border-yellow-400 text-yellow-800 rounded-r-lg shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
                    <p class="font-medium">Periksa data RT yang belum lengkap.</p>
                </div>
            </div>
            <div class="p-4 bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-400 text-green-800 rounded-r-lg shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-3"></i>
                    <p class="font-medium">Sistem backup otomatis telah dilakukan.</p>
                </div>
            </div>
            <div class="p-4 bg-gradient-to-r from-blue-50 to-blue-100 border-l-4 border-blue-400 text-blue-800 rounded-r-lg shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                    <p class="font-medium">Update sistem tersedia. Klik untuk info lebih lanjut.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg p-6 mb-10 border border-gray-200">
    <h4 class="text-xl font-bold mb-6 text-black drop-shadow-lg flex items-center">
        <i class="fas fa-chart-pie text-purple-600 mr-3"></i>Ringkasan Data Sistem
    </h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 rounded-xl border border-blue-200 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-800 text-sm font-medium">Total Warga Aktif</p>
                    <p class="text-2xl font-bold text-blue-900"><?php echo $data['warga']; ?></p>
                </div>
                <i class="fas fa-users text-blue-600 text-3xl"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-xl border border-green-200 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-800 text-sm font-medium">Total KK Terdaftar</p>
                    <p class="text-2xl font-bold text-green-900"><?php echo $data['kk']; ?></p>
                </div>
                <i class="fas fa-home text-green-600 text-3xl"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-purple-50 to-purple-100 p-4 rounded-xl border border-purple-200 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-800 text-sm font-medium">Total RT</p>
                    <p class="text-2xl font-bold text-purple-900"><?php echo $data['rt']; ?></p>
                </div>
                <i class="fas fa-map-marker-alt text-purple-600 text-3xl"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 p-4 rounded-xl border border-yellow-200 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-800 text-sm font-medium">Total RW</p>
                    <p class="text-2xl font-bold text-yellow-900"><?php echo $data['rw']; ?></p>
                </div>
                <i class="fas fa-building text-yellow-600 text-3xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg p-6 mb-10 border border-gray-200">
    <h3 class="text-xl font-bold mb-6 text-black drop-shadow-lg">Kelola Data</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="manage_users" class="group p-6 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all duration-300 border border-gray-200 hover:border-gray-300 hover:shadow-lg">
            <i class="fas fa-users text-gray-600 text-3xl mb-3 group-hover:scale-110 transition-transform"></i>
            <h4 class="font-bold text-black mb-2 drop-shadow-sm">Kelola User</h4>
            <p class="text-sm text-gray-600">Tambah, edit, hapus user</p>
        </a>
        <a href="manage_rt_rw" class="group p-6 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all duration-300 border border-gray-200 hover:border-gray-300 hover:shadow-lg">
            <i class="fas fa-map-marker-alt text-gray-600 text-3xl mb-3 group-hover:scale-110 transition-transform"></i>
            <h4 class="font-bold text-black mb-2 drop-shadow-sm">Kelola RT/RW</h4>
            <p class="text-sm text-gray-600">Atur struktur RT dan RW</p>
        </a>
        <a href="manage_master_data" class="group p-6 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all duration-300 border border-gray-200 hover:border-gray-300 hover:shadow-lg">
            <i class="fas fa-database text-gray-600 text-3xl mb-3 group-hover:scale-110 transition-transform"></i>
            <h4 class="font-bold text-black mb-2 drop-shadow-sm">Data Master</h4>
            <p class="text-sm text-gray-600">Kelola data utama sistem</p>
        </a>
        <a href="gallery" class="group p-6 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all duration-300 border border-gray-200 hover:border-gray-300 hover:shadow-lg">
            <i class="fas fa-images text-gray-600 text-3xl mb-3 group-hover:scale-110 transition-transform"></i>
            <h4 class="font-bold text-black mb-2 drop-shadow-sm">Kelola Galeri</h4>
            <p class="text-sm text-gray-600">Tambah dan kelola galeri</p>
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 mb-8">
    <div class="flex justify-between items-center p-6 cursor-pointer hover:bg-gray-50 transition-colors"
         onclick="toggleTraffic()">
        <h3 class="text-xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-chart-line text-blue-600 mr-3"></i>Traffic Pengunjung
        </h3>
        <i id="trafficIcon" class="fas fa-minus text-gray-600"></i>
    </div>
    <div id="trafficBody" class="p-6 pt-0">
        <div class="mb-4 flex items-center justify-center space-x-6">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-blue-500 rounded-full mr-2"></div>
                <span class="text-sm text-gray-600">Pengunjung Harian</span>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-blue-600"><?php echo array_sum($traffic); ?></p>
                <p class="text-xs text-gray-500">Total 7 hari</p>
            </div>
        </div>
        <canvas id="trafficChart" height="120"></canvas>
    </div>
</div>

</div>
</div>

<script>
function toggleTraffic(){
    const body = document.getElementById('trafficBody');
    const icon = document.getElementById('trafficIcon');

    if(body.style.display === 'none'){
        body.style.display = 'block';
        icon.classList.replace('fa-plus','fa-minus');
    } else {
        body.style.display = 'none';
        icon.classList.replace('fa-minus','fa-plus');
    }
}

new Chart(document.getElementById('trafficChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode($days) ?>,
        datasets: [{
            label: 'Pengunjung',
            data: <?= json_encode($traffic) ?>,
            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#3B82F6',
            pointBorderColor: '#FFFFFF',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#FFFFFF',
                bodyColor: '#FFFFFF',
                cornerRadius: 8
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0, 0, 0, 0.1)' },
                ticks: { color: '#6B7280' }
            },
            x: {
                grid: { color: 'rgba(0, 0, 0, 0.1)' },
                ticks: { color: '#6B7280' }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
});
</script>

