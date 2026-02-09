<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ketua') {
    header("Location: ../../home.php");
    exit();
}

include '../../config/database.php';
include '../../layouts/ketua/header.php';
include '../../layouts/ketua/sidebar.php';

require_once '../../vendor/dompdf/dompdf/autoload.inc.php';

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

<div class="ml-64 p-8 bg-white min-h-screen">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Laporan</h1>
        <a href="?export=pdf" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Export to PDF
        </a>
    </div>

    <!-- Laporan Warga -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Laporan Jumlah Warga</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600"><?php echo $warga_data['total_warga']; ?></div>
                <div class="text-sm text-gray-600">Total Warga</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600"><?php echo $warga_data['warga_aktif']; ?></div>
                <div class="text-sm text-gray-600">Warga Aktif</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-yellow-600"><?php echo $warga_data['warga_pindah'] + $warga_data['warga_meninggal']; ?></div>
                <div class="text-sm text-gray-600">Warga Tidak Aktif</div>
            </div>
        </div>
    </div>

    <!-- Laporan Jenis Kelamin -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Laporan Berdasarkan Jenis Kelamin</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600"><?php echo $warga_data['laki_laki']; ?></div>
                <div class="text-sm text-gray-600">Laki-laki</div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo $warga_data['total_warga'] > 0 ? ($warga_data['laki_laki'] / $warga_data['total_warga'] * 100) : 0; ?>%"></div>
                </div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-pink-600"><?php echo $warga_data['perempuan']; ?></div>
                <div class="text-sm text-gray-600">Perempuan</div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-pink-600 h-2 rounded-full" style="width: <?php echo $warga_data['total_warga'] > 0 ? ($warga_data['perempuan'] / $warga_data['total_warga'] * 100) : 0; ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Laporan KK -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Laporan Kartu Keluarga</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600"><?php echo $kk_data['total_kk']; ?></div>
                <div class="text-sm text-gray-600">Total KK</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-indigo-600"><?php echo number_format($kk_data['rata_rata_anggota'], 1); ?></div>
                <div class="text-sm text-gray-600">Rata-rata Anggota per KK</div>
            </div>
        </div>
    </div>

    <!-- Laporan Wilayah -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Laporan Wilayah</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600"><?php echo $wilayah_data['total_rt']; ?></div>
                <div class="text-sm text-gray-600">Total RT</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600"><?php echo $wilayah_data['total_rw']; ?></div>
                <div class="text-sm text-gray-600">Total RW</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600"><?php echo $wilayah_data['total_warga_wilayah']; ?></div>
                <div class="text-sm text-gray-600">Total Warga</div>
            </div>
        </div>
    </div>

    <!-- Laporan Mutasi -->
    <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-lg p-6 mb-8 border border-white/20">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Laporan Mutasi Warga (12 Bulan Terakhir)</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100/50">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Datang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pindah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meninggal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100/50">
                    <?php
                    $current_month = '';
                    $datang = 0;
                    $pindah = 0;
                    $meninggal = 0;

                    while ($mutasi = mysqli_fetch_assoc($mutasi_bulanan)) {
                        if ($current_month != $mutasi['bulan']) {
                            if ($current_month != '') {
                                echo "<tr class='hover:bg-gray-50/50 transition-colors duration-200'>
                                        <td class='px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900'>" . date('M Y', strtotime($current_month . '-01')) . "</td>
                                        <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>$datang</td>
                                        <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>$pindah</td>
                                        <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>$meninggal</td>
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

                     Output the last month
                    if ($current_month != '') {
                        echo "<tr class='hover:bg-gray-50/50 transition-colors duration-200'>
                                <td class='px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900'>" . date('M Y', strtotime($current_month . '-01')) . "</td>
                                <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>$datang</td>
                                <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>$pindah</td>
                                <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>$meninggal</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
