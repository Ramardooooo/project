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
        'users' => "SELECT COUNT(*) total FROM users",
        'rt_aktif' => "SELECT COUNT(*) total FROM rt WHERE status = 'aktif'",
        'rt_tidak_aktif' => "SELECT COUNT(*) total FROM rt WHERE status = 'tidak aktif'"
    ];

    foreach ($queries as $k => $q) {
        $data[$k] = mysqli_fetch_assoc(mysqli_query($conn, $q))['total'];
    }

    $recent_users = mysqli_query($conn, "SELECT username, role, created_at FROM users ORDER BY created_at DESC LIMIT 5");

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
<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes slideInLeft {
    from { opacity: 0; transform: translateX(-30px); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes slideInRight {
    from { opacity: 0; transform: translateX(30px); }
    to { opacity: 1; transform: translateX(0); }
}
.animate-fade-in {
    animation: fadeIn 0.8s ease-out forwards;
}
.animate-slide-left {
    animation: slideInLeft 0.8s ease-out forwards;
}
.animate-slide-right {
    animation: slideInRight 0.8s ease-out forwards;
}
.section-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(0,0,0,0.1), transparent);
    margin: 2rem 0;
}
</style>
<div id="mainContent" class="ml-64 min-h-screen bg-gray-50">
<div class="p-6 lg:p-8">



<!-- Statistics Overview -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 lg:gap-6 mb-12">
    <?php foreach ($stats as $index => $s): ?>
    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl p-6 border border-gray-100 hover:border-<?= $s[3] ?>-200 transition-all duration-300 group">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 group-hover:text-<?= $s[3] ?>-600 transition-colors"><?= $s[0] ?></p>
                <p class="text-2xl lg:text-3xl font-bold text-gray-800 group-hover:text-<?= $s[3] ?>-600 transition-colors"><?= number_format($s[1]) ?></p>
            </div>
            <div class="p-3 rounded-xl bg-gradient-to-br from-<?= $s[3] ?>-500 to-<?= $s[3] ?>-600 text-white shadow-lg group-hover:shadow-xl transition-shadow">
                <i class="fas fa-<?= $s[2] ?> text-xl"></i>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Audit Log and Recent Users -->
<div class="flex gap-6 mb-6">
    <!-- Audit Log -->
    <div class="bg-white rounded-lg shadow p-4 border border-gray-100 w-1/2">
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded mr-3">
                    <i class="fas fa-history text-blue-600 text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Audit Log</h3>
                    <p class="text-xs text-gray-500">Riwayat aktivitas sistem</p>
                </div>
            </div>
            <a href="audit_log" class="bg-blue-500 text-white px-3 py-2 rounded hover:bg-blue-600 flex items-center text-sm">
                <i class="fas fa-download mr-1"></i>Export
            </a>
        </div>
        <div class="max-h-48 overflow-y-auto">
            <?php
            $audit_logs = mysqli_query($conn, "SELECT action, table_name, username, created_at FROM audit_log ORDER BY created_at DESC LIMIT 5");
            if (mysqli_num_rows($audit_logs) > 0) {
                echo "<table class='w-full text-sm text-gray-700 border-collapse'>";
                echo "<thead><tr class='border-b border-gray-200'><th class='text-left py-2 px-1'>Aksi</th><th class='text-left py-2 px-1'>Tabel</th><th class='text-left py-2 px-1'>User</th><th class='text-left py-2 px-1'>Waktu</th></tr></thead>";
                echo "<tbody>";
                while ($log = mysqli_fetch_assoc($audit_logs)) {
                    echo "<tr class='border-b border-gray-100'>";
                    echo "<td class='py-2 px-1 font-medium'>{$log['action']}</td>";
                    echo "<td class='py-2 px-1'>{$log['table_name']}</td>";
                    echo "<td class='py-2 px-1'>{$log['username']}</td>";
                    echo "<td class='py-2 px-1'>" . date('d/m H:i', strtotime($log['created_at'])) . "</td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p class='text-gray-500 text-center py-4'>Belum ada aktivitas audit</p>";
            }
            ?>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="bg-white rounded-lg shadow p-4 border border-gray-100 w-1/2">
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded mr-3">
                    <i class="fas fa-users text-green-600 text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Pengguna Terbaru</h3>
                    <p class="text-xs text-gray-500">Pengguna yang baru terdaftar</p>
                </div>
            </div>
            <a href="manage_users" class="bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600 flex items-center text-sm">
                <i class="fas fa-eye mr-1"></i>Lihat Semua
            </a>
        </div>
        <div class="max-h-48 overflow-y-auto">
            <?php
            if (mysqli_num_rows($recent_users) > 0) {
                echo "<table class='w-full text-sm text-gray-700 border-collapse'>";
                echo "<thead><tr class='border-b border-gray-200'><th class='text-left py-2 px-1'>Username</th><th class='text-left py-2 px-1'>Role</th><th class='text-left py-2 px-1'>Dibuat</th></tr></thead>";
                echo "<tbody>";
                while ($user = mysqli_fetch_assoc($recent_users)) {
                    echo "<tr class='border-b border-gray-100'>";
                    echo "<td class='py-2 px-1 font-medium'>{$user['username']}</td>";
                    echo "<td class='py-2 px-1'>{$user['role']}</td>";
                    echo "<td class='py-2 px-1'>" . date('d/m H:i', strtotime($user['created_at'])) . "</td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p class='text-gray-500 text-center py-4'>Belum ada pengguna</p>";
            }
            ?>
        </div>
    </div>
</div>



<div class="grid grid-cols-1 gap-6 mb-10">
    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl p-6 border border-gray-100 transition-all duration-300">
        <div class="flex items-center mb-6">
            <div class="p-3 bg-yellow-100 rounded-xl mr-4">
                <i class="fas fa-bolt text-yellow-600 text-xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-6 text-black">Quick Actions</h3>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <a href="tambah_user" class="group p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl hover:from-blue-100 hover:to-blue-200 transition-all duration-300 border border-blue-200 hover:border-blue-300 hover:shadow-lg">
                <i class="fas fa-user-plus text-blue-600 text-2xl mb-2 transition-transform"></i>
                <h4 class="font-bold text-blue-900 mb-1 text-sm">Tambah User</h4>
                <p class="text-xs text-blue-700">Buat akun baru</p>
            </a>
            <a href="tambah_rt" class="group p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-xl hover:from-green-100 hover:to-green-200 transition-all duration-300 border border-green-200 hover:border-green-300 hover:shadow-lg">
                <i class="fas fa-plus-circle text-green-600 text-2xl mb-2 transition-transform"></i>
                <h4 class="font-bold text-green-900 mb-1 text-sm">Tambah RT</h4>
                <p class="text-xs text-green-700">Buat RT baru</p>
            </a>
            <a href="manage_users" class="group p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl hover:from-purple-100 hover:to-purple-200 transition-all duration-300 border border-purple-200 hover:border-purple-300 hover:shadow-lg">
                <i class="fas fa-users-cog text-purple-600 text-2xl mb-2 transition-transform"></i>
                <h4 class="font-bold text-purple-900 mb-1 text-sm">Kelola User</h4>
                <p class="text-xs text-purple-700">Edit & hapus user</p>
            </a>
            <a href="manage_rt_rw" class="group p-4 bg-gradient-to-r from-orange-50 to-orange-100 rounded-xl hover:from-orange-100 hover:to-orange-200 transition-all duration-300 border border-orange-200 hover:border-orange-300 hover:shadow-lg">
                <i class="fas fa-map-marked-alt text-orange-600 text-2xl mb-2 transition-transform"></i>
                <h4 class="font-bold text-orange-900 mb-1 text-sm">Kelola RT/RW</h4>
                <p class="text-xs text-orange-700">Atur struktur</p>
            </a>
            <a href="gallery" class="group p-4 bg-gradient-to-r from-pink-50 to-pink-100 rounded-xl hover:from-pink-100 hover:to-pink-200 transition-all duration-300 border border-pink-200 hover:border-pink-300 hover:shadow-lg">
                <i class="fas fa-images text-pink-600 text-2xl mb-2 transition-transform"></i>
                <h4 class="font-bold text-pink-900 mb-1 text-sm">Galeri</h4>
                <p class="text-xs text-pink-700">Kelola foto</p>
            </a>
            <a href="export_audit_log" class="group p-4 bg-gradient-to-r from-indigo-50 to-indigo-100 rounded-xl hover:from-indigo-100 hover:to-indigo-200 transition-all duration-300 border border-indigo-200 hover:border-indigo-300 hover:shadow-lg">
                <i class="fas fa-file-export text-indigo-600 text-2xl mb-2 transition-transform"></i>
                <h4 class="font-bold text-indigo-900 mb-1 text-sm">Export Log</h4>
                <p class="text-xs text-indigo-700">Unduh audit log</p>
            </a>
        </div>
    </div>

    <!-- Distribusi RT -->
    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl p-6 border border-gray-100 transition-all duration-300">
        <div class="flex items-center mb-6">
            <div class="p-3 bg-green-100 rounded-xl mr-4">
                <i class="fas fa-chart-pie text-green-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-800">Distribusi RT</h3>
                <p class="text-sm text-gray-500">Status aktif dan tidak aktif RT</p>
            </div>
        </div>
        <div class="flex items-center justify-center mb-4">
            <div class="w-64 h-64">
                <canvas id="rtDistributionChart"></canvas>
            </div>
        </div>
        <div class="mt-4 flex justify-center space-x-6 text-sm">
            <div class="flex items-center">
                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                <span>Aktif: <?= $data['rt_aktif'] ?> RT</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                <span>Tidak Aktif: <?= $data['rt_tidak_aktif'] ?> RT</span>
            </div>
        </div>
    </div>
</div>

<!-- Traffic Analytics -->
<div class="section-divider"></div>
<div class="bg-white rounded-2xl shadow-lg hover:shadow-xl border border-gray-100 mb-8 transition-all duration-300">
    <div class="flex justify-between items-center p-6 cursor-pointer hover:bg-gray-50 transition-colors rounded-t-2xl"
         onclick="toggleTraffic()">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-xl mr-4">
                <i class="fas fa-chart-line text-blue-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-800">Traffic Pengunjung</h3>
                <p class="text-sm text-gray-500">Analitik kunjungan harian</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="hidden lg:flex items-center space-x-2 text-sm text-gray-600">
                <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                <span>Live Data</span>
            </div>
            <i id="trafficIcon" class="fas fa-minus text-gray-600 text-lg hover:text-gray-800 transition-colors"></i>
        </div>
    </div>
    <div id="trafficBody" class="p-6 pt-0">
        <div class="mb-6 flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
            <div class="flex items-center space-x-6">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-blue-500 rounded-full mr-2 animate-pulse"></div>
                    <span class="text-sm font-medium text-gray-700">Pengunjung Harian</span>
                </div>
            </div>
            <div class="text-center bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-3 rounded-xl border border-blue-200">
                <p class="text-3xl font-bold text-blue-600"><?php echo number_format(array_sum($traffic)); ?></p>
                <p class="text-xs text-gray-500 font-medium">Total 7 hari terakhir</p>
            </div>
        </div>
        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
            <canvas id="trafficChart" height="120"></canvas>
        </div>
        <div class="mt-4 flex flex-wrap justify-center gap-4 text-xs text-gray-500">
            <div class="flex items-center">
                <i class="fas fa-mouse-pointer mr-1 text-blue-500"></i>
                Klik untuk detail
            </div>
            <div class="flex items-center">
                <i class="fas fa-sync-alt mr-1 text-green-500"></i>
                Update otomatis
            </div>
        </div>
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

new Chart(document.getElementById('rtDistributionChart'), {
    type: 'pie',
    data: {
        labels: ['Aktif', 'Tidak Aktif'],
        datasets: [{
            data: [<?= $data['rt_aktif'] ?>, <?= $data['rt_tidak_aktif'] ?>],
            backgroundColor: ['#10B981', '#EF4444'],
            borderColor: ['#FFFFFF', '#FFFFFF'],
            borderWidth: 2
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
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += context.parsed + ' RT';
                        return label;
                    }
                }
            }
        }
    }
});
</script>


