<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: home");
    exit();
}

include '../../config/database.php';
include '../../layouts/admin/header.php';
include '../../layouts/admin/sidebar.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: home");
    exit();
}

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

<h1 class="text-4xl font-extrabold mb-8 text-white drop-shadow-lg">Dashboard Admin</h1>
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
            <h3 class="text-xl font-bold text-black drop-shadow-lg">Audit Log</h3>
            <a href="audit_log" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">Export .txt</a>
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
        <h3 class="text-xl font-bold mb-6 text-black drop-shadow-lg">Notifikasi</h3>
        <div class="space-y-4">
            <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 rounded-r-lg shadow-sm">
                <p class="font-medium">Periksa data RT yang belum lengkap.</p>
            </div>
            <div class="p-4 bg-green-50 border-l-4 border-green-400 text-green-800 rounded-r-lg shadow-sm">
                <p class="font-medium">Sistem backup otomatis telah dilakukan.</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg p-6 mb-10 border border-gray-200">
    <h4 class="text-xl font-bold mb-4 text-black drop-shadow-lg">Ringkasan Data</h4>
    <ul class="space-y-3">
        <li class="flex justify-between items-center p-2 bg-gray-50 rounded-lg shadow-sm">
            <span class="font-medium text-gray-700">Total Warga Aktif:</span>
            <span class="font-bold text-gray-800"><?php echo $data['warga']; ?></span>
        </li>
        <li class="flex justify-between items-center p-2 bg-gray-50 rounded-lg shadow-sm">
            <span class="font-medium text-gray-700">Total KK Terdaftar:</span>
            <span class="font-bold text-gray-800"><?php echo $data['kk']; ?></span>
        </li>
        <li class="flex justify-between items-center p-2 bg-gray-50 rounded-lg shadow-sm">
            <span class="font-medium text-gray-700">Total RT:</span>
            <span class="font-bold text-gray-800"><?php echo $data['rt']; ?></span>
        </li>
        <li class="flex justify-between items-center p-2 bg-gray-50 rounded-lg shadow-sm">
            <span class="font-medium text-gray-700">Total RW:</span>
            <span class="font-bold text-gray-800"><?php echo $data['rw']; ?></span>
        </li>
    </ul>
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
        <h3 class="text-xl font-bold text-gray-800">Traffic Pengunjung</h3>
        <i id="trafficIcon" class="fas fa-minus text-gray-600"></i>
    </div>
    <div id="trafficBody" class="p-6 pt-0">
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
            data: <?= json_encode($traffic) ?>,
            borderWidth: 2,
            fill: true
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>

