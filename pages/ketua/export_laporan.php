<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ketua') {
    header("Location: ../../home.php");
    exit();
}

include '../../config/database.php';
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

$warga_data = mysqli_fetch_assoc($laporan_warga);
$kk_data = mysqli_fetch_assoc($laporan_kk);
$wilayah_data = mysqli_fetch_assoc($laporan_wilayah);

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="laporan_warga.pdf"');

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
$dompdf->stream('laporan_warga.pdf', array('Attachment' => 1));
exit();
