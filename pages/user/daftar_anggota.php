<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../home");
    exit();
}

include '../../config/database.php';
require_once '../../vendor/autoload.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get user's KK information - simplified query
$kk_result = mysqli_query($conn, "SELECT kk.id, kk.no_kk, kk.kepala_keluaraga,
    (SELECT w.nik FROM warga w WHERE w.nama = kk.kepala_keluaraga LIMIT 1) as kepala_nik
    FROM kk 
    INNER JOIN warga ON warga.kk_id = kk.id 
    WHERE warga.nama = '$username' 
    LIMIT 1");

$user_kk = mysqli_fetch_assoc($kk_result);

// Get all members of the user's KK with complete data
$anggota = [];
if ($user_kk) {
    $kk_id = $user_kk['id'];
    $anggota_result = mysqli_query($conn, "
        SELECT w.id, w.nama, w.nik, w.jk, w.tanggal_lahir, w.pekerjaan, w.alamat, w.status, w.tempat_lahir, w.goldar, w.agama, w.status_kawin,
            CASE 
                WHEN w.nama = '$username' THEN 'Kamu'
                WHEN w.nama = kk.kepala_keluaraga THEN 'Kepala Keluarga'
                ELSE 'Anggota'
            END as peran
        FROM warga w
        LEFT JOIN kk ON w.kk_id = kk.id
        WHERE w.kk_id = $kk_id AND w.status = 'aktif'
        ORDER BY CASE WHEN w.nama = kk.kepala_keluaraga THEN 0 ELSE 1 END, w.nama ASC
    ");
    if ($anggota_result) {
        $anggota = mysqli_fetch_all($anggota_result, MYSQLI_ASSOC);
    }
}

// Handle PDF Export
if (isset($_POST['export_pdf']) && $user_kk) {
    $dompdf = new Dompdf\Dompdf();
    
    // Build HTML for PDF
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
            .header { text-align: center; padding: 15px; border-bottom: 3px solid #1e40af; }
            .header h2 { color: #1e40af; font-size: 18px; margin-bottom: 3px; }
            .header p { color: #666; font-size: 10px; }
            .kk-info { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; padding: 15px 20px; }
            .kk-info h1 { font-size: 18px; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 5px; }
            .kk-info p { font-size: 10px; opacity: 0.9; }
            .info-table { width: 100%; padding: 15px 20px; border-bottom: 2px solid #1e40af; }
            .info-table td { padding: 3px 0; font-size: 11px; }
            .info-table .label { font-weight: bold; color: #1e40af; width: 150px; }
            .info-table .value { font-weight: bold; }
            table { width: 100%; border-collapse: collapse; padding: 15px 20px; }
            th { background: #1e40af; color: white; padding: 8px 6px; text-align: left; font-size: 9px; text-transform: uppercase; }
            td { padding: 6px; border-bottom: 1px solid #e5e5e5; font-size: 10px; }
            tr:nth-child(even) { background: #f9fafb; }
            tr:hover { background: #f0f4f8; }
            .text-center { text-align: center; }
            .footer { background: #1e40af; color: white; padding: 8px 20px; text-align: center; font-size: 9px; }
            .no-data { text-align: center; padding: 30px; color: #666; }
        </style>
    </head>
    <body>
        <div class="header">
            <h2>KARTU KELUARGA</h2>
            <p>Sistem Informasi Kependudukan Desa/Kelurahan</p>
        </div>
        
        <div class="kk-info">
            <h1>Kartu Keluarga</h1>
            <p>Republic of Indonesia</p>
        </div>
        
        <table class="info-table">
            <tr>
                <td class="label">Nomor KK</td>
                <td class="value">: ' . htmlspecialchars($user_kk['no_kk'] ?? '-') . '</td>
                <td class="label">Jumlah Anggota</td>
                <td class="value">: ' . count($anggota) . ' orang</td>
            </tr>
            <tr>
                <td class="label">Nama Kepala Keluarga</td>
                <td class="value" colspan="3">: ' . htmlspecialchars($user_kk['kepala_keluaraga'] ?? '-') . '</td>
            </tr>
        </table>
        
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 30px;">No</th>
                    <th>Nama Lengkap</th>
                    <th style="width: 100px;">NIK</th>
                    <th class="text-center" style="width: 40px;">JK</th>
                    <th style="width: 70px;">Tgl Lahir</th>
                    <th>Tempat Lahir</th>
                    <th class="text-center" style="width: 50px;">Gol. Darah</th>
                    <th>Agama</th>
                    <th>Status Kawin</th>
                    <th>Pekerjaan</th>
                    <th style="width: 80px;">Peran</th>
                </tr>
            </thead>
            <tbody>';
    
    if (count($anggota) > 0) {
        $no = 1;
        foreach ($anggota as $member) {
            $html .= '
                <tr>
                    <td class="text-center">' . $no++ . '</td>
                    <td>' . htmlspecialchars($member['nama']) . '</td>
                    <td>' . htmlspecialchars($member['nik'] ?? '-') . '</td>
                    <td class="text-center">' . ($member['jk'] === 'L' ? 'L' : 'P') . '</td>
                    <td>' . ($member['tanggal_lahir'] ? date('d-m-Y', strtotime($member['tanggal_lahir'])) : '-') . '</td>
                    <td>' . htmlspecialchars($member['tempat_lahir'] ?? '-') . '</td>
                    <td class="text-center">' . htmlspecialchars($member['goldar'] ?? '-') . '</td>
                    <td>' . htmlspecialchars($member['agama'] ?? '-') . '</td>
                    <td>' . htmlspecialchars($member['status_kawin'] ?? '-') . '</td>
                    <td>' . htmlspecialchars($member['pekerjaan'] ?? '-') . '</td>
                    <td>' . htmlspecialchars($member['peran']) . '</td>
                </tr>';
        }
    } else {
        $html .= '
                <tr>
                    <td colspan="11" class="no-data">Belum ada anggota keluarga</td>
                </tr>';
    }
    
    $html .= '
            </tbody>
        </table>
        
        <!-- Tanda Tangan -->
        <table style="margin-top: 20px; padding: 0 20px;">
            <tr>
                <td style="width: 33%; text-align: center; padding: 10px;">
                    <p style="font-size: 10px; margin-bottom: 40px;">Diketahui oleh,</p>
                    <p style="font-size: 11px; font-weight: bold; border-bottom: 1px solid #333; padding-bottom: 5px; margin-bottom: 5px;">KEPALA DESA/KELURAHAN</p>
                    <p style="font-size: 9px;">( _______________________ )</p>
                </td>
                <td style="width: 33%; text-align: center; padding: 10px;">
                    <p style="font-size: 10px; margin-bottom: 40px;">Disetujui oleh,</p>
                    <p style="font-size: 11px; font-weight: bold; border-bottom: 1px solid #333; padding-bottom: 5px; margin-bottom: 5px;">KETUA RT</p>
                    <p style="font-size: 9px;">( _______________________ )</p>
                </td>
                <td style="width: 34%; text-align: center; padding: 10px;">
                    <p style="font-size: 10px; margin-bottom: 40px;">Saya yang bertanda tangan,</p>
                    <p style="font-size: 11px; font-weight: bold; border-bottom: 1px solid #333; padding-bottom: 5px; margin-bottom: 5px;">KEPALA KELUARGA</p>
                    <p style="font-size: 9px;">( ' . strtoupper(htmlspecialchars($user_kk['kepala_keluaraga'] ?? '________________')) . ' )</p>
                </td>
            </tr>
        </table>
        
        <!-- Tanggal Cetak -->
        <table style="margin-top: 15px; padding: 0 20px;">
            <tr>
                <td style="text-align: center; font-size: 9px; color: #666;">
                    Dicetak pada: ' . date('d F Y') . ' | Sistem Informasi Kependudukan
                </td>
            </tr>
        </table>
        
        <div class="footer">
            <p>&copy; ' . date('Y') . ' Sistem Informasi Kepentingan Desa/Kelurahan</p>
        </div>
    </body>
    </html>';
    
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    
    // Download the PDF directly
    $filename = 'Kartu_Keluarga_' . str_replace(' ', '_', $user_kk['no_kk']) . '_' . date('Y-m-d') . '.pdf';
    $dompdf->stream($filename, array('Attachment' => 1));
    exit();
}

include '../../layouts/user/header.php';
include '../../layouts/user/sidebar.php';
?>

<div id="mainContent" class="ml-64 min-h-screen bg-gray-50">
    <div class="p-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Daftar Anggota Keluarga</h1>
                    <p class="text-gray-600">Lihat anggota kartu keluarga Anda</p>
                </div>
                <?php if ($user_kk): ?>
                <form method="POST">
                    <button type="submit" name="export_pdf" class="px-6 py-3 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 transition shadow-lg flex items-center">
                        <i class="fas fa-file-pdf mr-2"></i>Export PDF
                    </button>
                </form>
                <?php endif; ?>
            </div>

            <?php if ($user_kk): ?>
                <!-- KK Info Card -->
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl p-6 mb-6 text-white shadow-lg">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-blue-100 text-sm mb-1">Nomor Kartu Keluarga</p>
                            <p class="text-2xl font-bold"><?php echo htmlspecialchars($user_kk['no_kk']); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-blue-100 text-sm mb-1">Kepala Keluarga</p>
                            <p class="text-xl font-semibold"><?php echo htmlspecialchars($user_kk['kepala_keluaraga']); ?></p>
                            <?php if (!empty($user_kk['kepala_nik'])): ?>
                                <p class="text-blue-200 text-sm">NIK: <?php echo htmlspecialchars($user_kk['kepala_nik']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-blue-400">
                        <p class="text-blue-100 text-sm">
                            <i class="fas fa-users mr-2"></i>
                            Total Anggota: <?php echo count($anggota); ?> orang
                        </p>
                    </div>
                </div>

                <!-- Members List - Complete Columns -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">NIK</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">JK</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tgl Lahir</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tempat Lahir</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Gol. Darah</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Agama</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status Kawin</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pekerjaan</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Peran</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php if (count($anggota) > 0): ?>
                                    <?php $no = 1; foreach ($anggota as $member): ?>
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-3 text-sm text-gray-600"><?php echo $no++; ?></td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 flex items-center justify-center text-white font-bold mr-2 text-sm">
                                                        <?php echo strtoupper(substr($member['nama'], 0, 1)); ?>
                                                    </div>
                                                    <p class="font-semibold text-gray-800 text-sm"><?php echo htmlspecialchars($member['nama']); ?></p>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars($member['nik']); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-600"><?php echo $member['jk'] === 'L' ? 'L' : 'P'; ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-600"><?php echo $member['tanggal_lahir'] ? date('d-m-Y', strtotime($member['tanggal_lahir'])) : '-'; ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars($member['tempat_lahir'] ?? '-'); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars($member['goldar'] ?? '-'); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars($member['agama'] ?? '-'); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars($member['status_kawin'] ?? '-'); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars($member['pekerjaan'] ?? '-'); ?></td>
                                            <td class="px-4 py-3">
                                                <?php 
                                                $peran_class = '';
                                                if ($member['peran'] === 'Kamu') {
                                                    $peran_class = 'bg-green-100 text-green-700';
                                                } elseif ($member['peran'] === 'Kepala Keluarga') {
                                                    $peran_class = 'bg-purple-100 text-purple-700';
                                                } else {
                                                    $peran_class = 'bg-blue-100 text-blue-700';
                                                }
                                                ?>
                                                <span class="px-2 py-1 rounded-full text-xs font-medium <?php echo $peran_class; ?>">
                                                    <?php echo $member['peran']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-users text-4xl mb-3 text-gray-300"></i>
                                            <p>Belum ada anggota keluarga</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <!-- No KK Assigned -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-8 text-center">
                    <i class="fas fa-exclamation-triangle text-4xl text-yellow-400 mb-4"></i>
                    <h3 class="text-lg font-semibold text-yellow-800 mb-2">Anda Belum Terhubung dengan KK</h3>
                    <p class="text-yellow-700 mb-4">Silakan hubungi Ketua RT untuk 加入 kartu keluarga.</p>
                    <a href="input_data_diri.php" class="inline-block px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                        <i class="fas fa-user-plus mr-2"></i>Input Data Diri
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

