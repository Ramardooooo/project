<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ketua') {
    header("Location: ../../home.php");
    exit();
}

include '../../config/database.php';
include '../../layouts/ketua/header.php';
include '../../layouts/ketua/sidebar.php';

require_once '../../vendor/autoload.php';

$laporan_warga = mysqli_query($conn, "
    SELECT
        COUNT(*) as total_warga,
        SUM(CASE WHEN jk = 'L' THEN 1 ELSE 0 END) as laki_laki,
        SUM(CASE WHEN jk = 'P' THEN 1 ELSE 0 END) as perempuan,
        SUM(CASE WHEN status = 'aktif' THEN 1 ELSE 0 END) as warga_aktif,
        SUM(CASE WHEN status = 'pindah' THEN 1 ELSE 0 END) as warga_pindah,
        SUM(CASE WHEN status = 'meninggal' THEN 1 ELSE 0 END) as warga_meninggal
    FROM warga
");

$laporan_kk = mysqli_query($conn, "
    SELECT COUNT(*) as total_kk, 0 as rata_rata_anggota
    FROM kk
");

$laporan_wilayah = mysqli_query($conn, "
    SELECT
        COUNT(DISTINCT rt.id) as total_rt,
        COUNT(DISTINCT rw.id) as total_rw,
        COUNT(w.id) as total_warga_wilayah
    FROM rt
    CROSS JOIN rw
    LEFT JOIN warga w ON w.status = 'aktif'
");

$mutasi_bulanan = mysqli_query($conn, "
    SELECT
        DATE_FORMAT(tanggal_mutasi, '%Y-%m') as bulan,
        jenis_mutasi,
        COUNT(*) as jumlah
    FROM mutasi_warga
    WHERE tanggal_mutasi >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(tanggal_mutasi, '%Y-%m'), jenis_mutasi
    ORDER BY bulan DESC, jenis_mutasi
");

$warga_data = mysqli_fetch_assoc($laporan_warga);
$kk_data = mysqli_fetch_assoc($laporan_kk);
$wilayah_data = mysqli_fetch_assoc($laporan_wilayah);

if (isset($_GET['export']) && $_GET['export'] == 'pdf') {
    $dompdf = new Dompdf\Dompdf();
    $html = '
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            h1 { text-align: center; }
            h3 { margin-top: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .center { text-align: center; }
            .bold { font-weight: bold; }
        </style>
    </head>
    <body>
        <h1>Laporan Sistem Informasi Warga</h1>
        <h3>Laporan Jumlah Warga</h3>
        <table>
            <tr><td>Total Warga</td><td class="center bold">' . $warga_data['total_warga'] . '</td></tr>
            <tr><td>Warga Aktif</td><td class="center bold">' . $warga_data['warga_aktif'] . '</td></tr>
            <tr><td>Warga Tidak Aktif</td><td class="center bold">' . ($warga_data['warga_pindah'] + $warga_data['warga_meninggal']) . '</td></tr>
        </table>
        <h3>Laporan Berdasarkan Jenis Kelamin</h3>
        <table>
            <tr><td>Laki-laki</td><td class="center bold">' . $warga_data['laki_laki'] . '</td></tr>
            <tr><td>Perempuan</td><td class="center bold">' . $warga_data['perempuan'] . '</td></tr>
        </table>
        <h3>Laporan Kartu Keluarga</h3>
        <table>
            <tr><td>Total KK</td><td class="center bold">' . $kk_data['total_kk'] . '</td></tr>
            <tr><td>Rata-rata Anggota per KK</td><td class="center bold">' . number_format($kk_data['rata_rata_anggota'], 1) . '</td></tr>
        </table>
        <h3>Laporan Wilayah</h3>
        <table>
            <tr><td>Total RT</td><td class="center bold">' . $wilayah_data['total_rt'] . '</td></tr>
            <tr><td>Total RW</td><td class="center bold">' . $wilayah_data['total_rw'] . '</td></tr>
            <tr><td>Total Warga</td><td class="center bold">' . $wilayah_data['total_warga_wilayah'] . '</td></tr>
        </table>
        <h3>Laporan Mutasi Warga (12 Bulan Terakhir)</h3>
        <table>
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Datang</th>
                    <th>Pindah</th>
                    <th>Meninggal</th>
                </tr>
            </thead>
            <tbody>';

    $mutasi_bulanan_pdf = mysqli_query($conn, "
        SELECT
            DATE_FORMAT(tanggal_mutasi, '%Y-%m') as bulan,
            jenis_mutasi,
            COUNT(*) as jumlah
        FROM mutasi_warga
        WHERE tanggal_mutasi >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(tanggal_mutasi, '%Y-%m'), jenis_mutasi
        ORDER BY bulan DESC, jenis_mutasi
    ");

    $current_month = '';
    $datang = 0;
    $pindah = 0;
    $meninggal = 0;

    while ($mutasi = mysqli_fetch_assoc($mutasi_bulanan_pdf)) {
        if ($current_month != $mutasi['bulan']) {
            if ($current_month != '') {
                $html .= "<tr>
                    <td>" . date('M Y', strtotime($current_month . '-01')) . "</td>
                    <td class='center'>$datang</td>
                    <td class='center'>$pindah</td>
                    <td class='center'>$meninggal</td>
                </tr>";
            }
            $current_month = $mutasi['bulan'];
            $datang = 0;
            $pindah = 0;
            $meninggal = 0;
        }

        switch($mutasi['jenis_mutasi']) {
            case 'datang': $datang = $mutasi['jumlah']; break;
            case 'pindah': $pindah = $mutasi['jumlah']; break;
            case 'meninggal': $meninggal = $mutasi['jumlah']; break;
        }
    }

    if ($current_month != '') {
        $html .= "<tr>
            <td>" . date('M Y', strtotime($current_month . '-01')) . "</td>
            <td class='center'>$datang</td>
            <td class='center'>$pindah</td>
            <td class='center'>$meninggal</td>
        </tr>";
    }

    $html .= '
            </tbody>
        </table>
    </body>
    </html>';

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream('laporan_warga.pdf', array('Attachment' => 0));
    exit();
}
?>

<div class="ml-64 p-8 bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Laporan</h1>
        <a href="?export=pdf" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
            <i class="fas fa-download mr-2"></i>Export to PDF
        </a>
    </div>

    <!-- Laporan Warga -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-8 hover:shadow-xl transition-shadow duration-300">
        <h3 class="text-xl font-semibold mb-6 text-gray-800 flex items-center">
            <i class="fas fa-users mr-3 text-blue-500"></i>
            Laporan Jumlah Warga
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 text-center hover:from-blue-100 hover:to-blue-200 transition-all duration-300 transform hover:scale-105">
                <div class="text-4xl font-bold text-blue-600 mb-2"><?php echo $warga_data['total_warga']; ?></div>
                <div class="text-sm font-medium text-gray-700">Total Warga</div>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 text-center hover:from-green-100 hover:to-green-200 transition-all duration-300 transform hover:scale-105">
                <div class="text-4xl font-bold text-green-600 mb-2"><?php echo $warga_data['warga_aktif']; ?></div>
                <div class="text-sm font-medium text-gray-700">Warga Aktif</div>
            </div>
            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-6 text-center hover:from-yellow-100 hover:to-yellow-200 transition-all duration-300 transform hover:scale-105">
                <div class="text-4xl font-bold text-yellow-600 mb-2"><?php echo $warga_data['warga_pindah'] + $warga_data['warga_meninggal']; ?></div>
                <div class="text-sm font-medium text-gray-700">Warga Tidak Aktif</div>
            </div>
        </div>
    </div>

    <!-- Laporan Jenis Kelamin -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-8 hover:shadow-xl transition-shadow duration-300">
        <h3 class="text-xl font-semibold mb-6 text-gray-800 flex items-center">
            <i class="fas fa-venus-mars mr-3 text-purple-500"></i>
            Laporan Berdasarkan Jenis Kelamin
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 text-center hover:from-blue-100 hover:to-blue-200 transition-all duration-300 transform hover:scale-105">
                <div class="text-4xl font-bold text-blue-600 mb-2"><?php echo $warga_data['laki_laki']; ?></div>
                <div class="text-sm font-medium text-gray-700 mb-3">Laki-laki</div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500" style="width: <?php echo $warga_data['total_warga'] > 0 ? ($warga_data['laki_laki'] / $warga_data['total_warga'] * 100) : 0; ?>%"></div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-lg p-6 text-center hover:from-pink-100 hover:to-pink-200 transition-all duration-300 transform hover:scale-105">
                <div class="text-4xl font-bold text-pink-600 mb-2"><?php echo $warga_data['perempuan']; ?></div>
                <div class="text-sm font-medium text-gray-700 mb-3">Perempuan</div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-pink-500 to-pink-600 h-3 rounded-full transition-all duration-500" style="width: <?php echo $warga_data['total_warga'] > 0 ? ($warga_data['perempuan'] / $warga_data['total_warga'] * 100) : 0; ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Laporan KK -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-8 hover:shadow-xl transition-shadow duration-300">
        <h3 class="text-xl font-semibold mb-6 text-gray-800 flex items-center">
            <i class="fas fa-home mr-3 text-purple-500"></i>
            Laporan Kartu Keluarga
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6 text-center hover:from-purple-100 hover:to-purple-200 transition-all duration-300 transform hover:scale-105">
                <div class="text-4xl font-bold text-purple-600 mb-2"><?php echo $kk_data['total_kk']; ?></div>
                <div class="text-sm font-medium text-gray-700">Total KK</div>
            </div>
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg p-6 text-center hover:from-indigo-100 hover:to-indigo-200 transition-all duration-300 transform hover:scale-105">
                <div class="text-4xl font-bold text-indigo-600 mb-2"><?php echo number_format($kk_data['rata_rata_anggota'], 1); ?></div>
                <div class="text-sm font-medium text-gray-700">Rata-rata Anggota per KK</div>
            </div>
        </div>
    </div>

    <!-- Laporan Wilayah -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-8 hover:shadow-xl transition-shadow duration-300">
        <h3 class="text-xl font-semibold mb-6 text-gray-800 flex items-center">
            <i class="fas fa-map-marked-alt mr-3 text-green-500"></i>
            Laporan Wilayah
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 text-center hover:from-green-100 hover:to-green-200 transition-all duration-300 transform hover:scale-105">
                <div class="text-4xl font-bold text-green-600 mb-2"><?php echo $wilayah_data['total_rt']; ?></div>
                <div class="text-sm font-medium text-gray-700">Total RT</div>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 text-center hover:from-blue-100 hover:to-blue-200 transition-all duration-300 transform hover:scale-105">
                <div class="text-4xl font-bold text-blue-600 mb-2"><?php echo $wilayah_data['total_rw']; ?></div>
                <div class="text-sm font-medium text-gray-700">Total RW</div>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6 text-center hover:from-purple-100 hover:to-purple-200 transition-all duration-300 transform hover:scale-105">
                <div class="text-4xl font-bold text-purple-600 mb-2"><?php echo $wilayah_data['total_warga_wilayah']; ?></div>
                <div class="text-sm font-medium text-gray-700">Total Warga</div>
            </div>
        </div>
    </div>

    <!-- Laporan Mutasi -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Laporan Mutasi Warga (12 Bulan Terakhir)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php
            $mutasi_data = [];
            while ($mutasi = mysqli_fetch_assoc($mutasi_bulanan)) {
                $bulan = $mutasi['bulan'];
                if (!isset($mutasi_data[$bulan])) $mutasi_data[$bulan] = ['datang' => 0, 'pindah' => 0, 'meninggal' => 0];
                $mutasi_data[$bulan][$mutasi['jenis_mutasi']] = $mutasi['jumlah'];
            }
            foreach ($mutasi_data as $bulan => $data) {
                echo "<div class='bg-gray-50 rounded-lg p-4'>
                        <h4 class='font-medium text-gray-900'>" . date('M Y', strtotime($bulan . '-01')) . "</h4>
                        <div class='mt-2 space-y-1'>
                            <div class='flex justify-between'><span class='text-green-600'>Datang:</span><span>{$data['datang']}</span></div>
                            <div class='flex justify-between'><span class='text-red-600'>Pindah:</span><span>{$data['pindah']}</span></div>
                            <div class='flex justify-between'><span class='text-gray-600'>Meninggal:</span><span>{$data['meninggal']}</span></div>
                        </div>
                      </div>";
            }
            ?>
        </div>
    </div>
</div>

</body>
</html>
