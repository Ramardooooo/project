<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../home");
    exit();
}

ob_start();

include '../../config/database.php';
include '../../layouts/user/header.php';
include '../../layouts/user/sidebar.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get user's KK information - simplified query
$kk_result = mysqli_query($conn, "SELECT kk.id, kk.no_kk, kk.kepala_keluaraga
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

// Export to PDF - just set flag
$show_print = isset($_POST['export_pdf']) && $user_kk;
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
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm mb-1">Nomor Kartu Keluarga</p>
                            <p class="text-2xl font-bold"><?php echo htmlspecialchars($user_kk['no_kk']); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-blue-100 text-sm mb-1">Kepala Keluarga</p>
                            <p class="text-xl font-semibold"><?php echo htmlspecialchars($user_kk['kepala_keluaraga']); ?></p>
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

<?php if ($show_print): ?>
<script>
window.onload = function() {
    window.print();
};
</script>
<style>
@media print {
    body * { visibility: hidden; }
    .kk-container, .kk-container * { visibility: visible; }
    .kk-container { position: absolute; left: 0; top: 0; width: 100%; }
}
</style>
<div class="kk-container" style="display: block !important;">
    <div style="text-align: center; padding: 15px; border-bottom: 3px solid #1e40af;">
        <h2 style="margin: 0; color: #1e40af; font-size: 18px;">KARTU KELUARGA</h2>
        <p style="color: #666; font-size: 11px;">KELURAHAN/DESA APPLICATION</p>
    </div>
    <div style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; padding: 20px 30px; text-align: center;">
        <h1 style="margin: 0; font-size: 24px; text-transform: uppercase; letter-spacing: 3px;">Kartu Keluarga</h1>
        <p style="margin: 5px 0 0 0; font-size: 12px;">Republic of Isfi</p>
    </div>
    <div style="padding: 20px 30px; border-bottom: 2px solid #1e40af;">
        <table style="width: 100%; font-size: 12px;">
            <tr>
                <td style="font-weight: bold; width: 150px; color: #1e40af;">Nomor KK</td>
                <td>: <strong><?php echo htmlspecialchars($user_kk['no_kk'] ?? ''); ?></strong></td>
                <td style="font-weight: bold; width: 100px; color: #1e40af;">Jumlah Anggota</td>
                <td>: <?php echo count($anggota); ?> orang</td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: #1e40af;">Nama Kepala Keluarga</td>
                <td>: <strong><?php echo htmlspecialchars($user_kk['kepala_keluaraga'] ?? ''); ?></strong></td>
            </tr>
        </table>
    </div>
    <div style="padding: 20px 30px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
            <thead>
                <tr style="background: #1e40af; color: white;">
                    <th style="padding: 8px; border: 1px solid #1e40af; text-align: center;">No</th>
                    <th style="padding: 8px; border: 1px solid #1e40af;">Nama Lengkap</th>
                    <th style="padding: 8px; border: 1px solid #1e40af;">NIK</th>
                    <th style="padding: 8px; border: 1px solid #1e40af; text-align: center;">JK</th>
                    <th style="padding: 8px; border: 1px solid #1e40af;">Tgl Lahir</th>
                    <th style="padding: 8px; border: 1px solid #1e40af;">Tempat Lahir</th>
                    <th style="padding: 8px; border: 1px solid #1e40af; text-align: center;">Gol. Darah</th>
                    <th style="padding: 8px; border: 1px solid #1e40af;">Agama</th>
                    <th style="padding: 8px; border: 1px solid #1e40af;">Status Kawin</th>
                    <th style="padding: 8px; border: 1px solid #1e40af;">Pekerjaan</th>
                    <th style="padding: 8px; border: 1px solid #1e40af;">Peran</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($anggota) > 0): ?>
                    <?php $no = 1; foreach ($anggota as $member): ?>
                    <tr>
                        <td style="padding: 6px; border: 1px solid #1e40af; text-align: center;"><?php echo $no++; ?></td>
                        <td style="padding: 6px; border: 1px solid #1e40af;"><?php echo htmlspecialchars($member['nama']); ?></td>
                        <td style="padding: 6px; border: 1px solid #1e40af;"><?php echo htmlspecialchars($member['nik']); ?></td>
                        <td style="padding: 6px; border: 1px solid #1e40af; text-align: center;"><?php echo $member['jk'] === 'L' ? 'L' : 'P'; ?></td>
                        <td style="padding: 6px; border: 1px solid #1e40af;"><?php echo $member['tanggal_lahir'] ? date('d-m-Y', strtotime($member['tanggal_lahir'])) : '-'; ?></td>
                        <td style="padding: 6px; border: 1px solid #1e40af;"><?php echo htmlspecialchars($member['tempat_lahir'] ?? '-'); ?></td>
                        <td style="padding: 6px; border: 1px solid #1e40af; text-align: center;"><?php echo htmlspecialchars($member['goldar'] ?? '-'); ?></td>
                        <td style="padding: 6px; border: 1px solid #1e40af;"><?php echo htmlspecialchars($member['agama'] ?? '-'); ?></td>
                        <td style="padding: 6px; border: 1px solid #1e40af;"><?php echo htmlspecialchars($member['status_kawin'] ?? '-'); ?></td>
                        <td style="padding: 6px; border: 1px solid #1e40af;"><?php echo htmlspecialchars($member['pekerjaan'] ?? '-'); ?></td>
                        <td style="padding: 6px; border: 1px solid #1e40af;"><?php echo htmlspecialchars($member['peran']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div style="background: #1e40af; color: white; padding: 10px 30px; text-align: center; font-size: 10px;">
        <p>&copy; <?php echo date('Y'); ?> Lurahgo.id - Sistem Informasi Kependudukan</p>
    </div>
</div>
<?php endif; ?>