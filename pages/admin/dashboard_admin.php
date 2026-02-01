<?php
include 'config/database.php';
include 'layouts/admin/header.php';
include 'layouts/admin/sidebar.php';

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
?>
<div class="ml-64 bg-gradient-to-br from-blue-700 to-blue-800 min-h-screen">
<div class="p-8">

<h1 class="text-4xl font-extrabold mb-8 text-white">Dashboard Admin</h1>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">
<?php foreach ($stats as $s): ?>
<div class="bg-blue-800 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-6 border border-blue-700">
    <div class="flex items-center">
        <div class="p-4 rounded-full bg-<?= $s[3] ?>-600 text-white shadow-md">
            <i class="fas fa-<?= $s[2] ?> text-2xl"></i>
        </div>
        <div class="ml-4">
            <p class="text-sm font-medium text-blue-200 uppercase tracking-wide"><?= $s[0] ?></p>
            <p class="text-3xl font-bold text-white"><?= $s[1] ?></p>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
    <div class="bg-blue-800 rounded-2xl shadow-lg p-6 border border-blue-700">
        <h3 class="text-xl font-bold mb-6 text-white">Aktivitas Terbaru</h3>
        <div class="space-y-4">
            <?php
            $recent_users = mysqli_query($conn, "SELECT username, created_at FROM users ORDER BY created_at DESC LIMIT 5");
            while ($user = mysqli_fetch_assoc($recent_users)) {
                echo "<div class='flex items-center justify-between p-3 bg-blue-600 rounded-lg hover:bg-blue-500 transition-colors'>";
                echo "<span class='font-medium text-blue-100'>User baru: {$user['username']}</span>";
                echo "<span class='text-sm text-blue-200'>" . date('d/m/Y H:i', strtotime($user['created_at'])) . "</span>";
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <div class="bg-blue-800 rounded-2xl shadow-lg p-6 border border-blue-700">
        <h3 class="text-xl font-bold mb-6 text-white">Notifikasi</h3>
        <div class="space-y-4">
            <div class="p-4 bg-yellow-700 border-l-4 border-yellow-400 text-yellow-100 rounded-r-lg">
                <p class="font-medium">Periksa data RT yang belum lengkap.</p>
            </div>
            <div class="p-4 bg-blue-700 border-l-4 border-blue-400 text-blue-100 rounded-r-lg">
                <p class="font-medium">Sistem backup otomatis telah dilakukan.</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-blue-700 rounded-2xl shadow-lg p-6 mb-10 border border-blue-600">
    <h4 class="text-xl font-bold mb-4 text-white">Ringkasan Data</h4>
    <ul class="space-y-3">
        <li class="flex justify-between items-center p-2 bg-blue-700 rounded-lg">
            <span class="font-medium text-blue-100">Total Warga Aktif:</span>
            <span class="font-bold text-white"><?php echo $data['warga']; ?></span>
        </li>
        <li class="flex justify-between items-center p-2 bg-blue-700 rounded-lg">
            <span class="font-medium text-blue-100">Total KK Terdaftar:</span>
            <span class="font-bold text-white"><?php echo $data['kk']; ?></span>
        </li>
        <li class="flex justify-between items-center p-2 bg-blue-700 rounded-lg">
            <span class="font-medium text-blue-100">Total RT:</span>
            <span class="font-bold text-white"><?php echo $data['rt']; ?></span>
        </li>
        <li class="flex justify-between items-center p-2 bg-blue-700 rounded-lg">
            <span class="font-medium text-blue-100">Total RW:</span>
            <span class="font-bold text-white"><?php echo $data['rw']; ?></span>
        </li>
    </ul>
</div>

<div class="bg-blue-800 rounded-2xl shadow-lg p-6 mb-10 border border-blue-700">
    <h3 class="text-xl font-bold mb-6 text-white">Kelola Data</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="/PROJECT/manage_users" class="group p-6 bg-gradient-to-br from-blue-800 to-blue-700 rounded-xl hover:from-blue-700 hover:to-blue-600 transition-all duration-300 border border-blue-700 hover:border-blue-600 hover:shadow-lg">
            <i class="fas fa-map-marker-alt text-blue-200 text-3xl mb-3 group-hover:scale-110 transition-transform"></i>
            <h4 class="font-bold text-white mb-2">Kelola User</h4>
            <p class="text-sm text-blue-100">Tambah, edit, hapus user</p>
        </a>
        <a href="/PROJECT/manage_rt_rw" class="group p-6 bg-gradient-to-br from-blue-800 to-blue-700 rounded-xl hover:from-blue-700 hover:to-blue-600 transition-all duration-300 border border-blue-700 hover:border-blue-600 hover:shadow-lg">
            <i class="fas fa-map-marker-alt text-blue-200 text-3xl mb-3 group-hover:scale-110 transition-transform"></i>
            <h4 class="font-bold text-white mb-2">Kelola RT/RW</h4>
            <p class="text-sm text-blue-100">Atur struktur RT dan RW</p>
        </a>
        <a href="manage_master_data.php" class="group p-6 bg-gradient-to-br from-blue-800 to-blue-700 rounded-xl hover:from-blue-700 hover:to-blue-600 transition-all duration-300 border border-blue-700 hover:border-blue-600 hover:shadow-lg">
            <i class="fas fa-database text-blue-200 text-3xl mb-3 group-hover:scale-110 transition-transform"></i>
            <h4 class="font-bold text-white mb-2">Data Master</h4>
            <p class="text-sm text-blue-100">Kelola data utama sistem</p>
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-100 mb-8">
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

<?php
}
include 'layouts/admin/footer.php';
?>
