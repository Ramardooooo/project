<?php
session_start();
if (!isset($_SESSION['role']) || strtolower(trim($_SESSION['role'])) !== 'ketua') {
    header("Location: ../../home.php");
    exit();
}


include '../../config/database.php';
require_once '../../vendor/autoload.php';
use Dompdf\Dompdf;

// Check dynamic columns
$has_tempat_lahir = false;
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM warga LIKE 'tempat_lahir'");
if ($check_col && mysqli_num_rows($check_col) > 0) $has_tempat_lahir = true;

$has_goldar = false;
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM warga LIKE 'goldar'");
if ($check_col && mysqli_num_rows($check_col) > 0) $has_goldar = true;

$has_agama = false;
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM warga LIKE 'agama'");
if ($check_col && mysqli_num_rows($check_col) > 0) $has_agama = true;

$has_status_kawin = false;
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM warga LIKE 'status_kawin'");
if ($check_col && mysqli_num_rows($check_col) > 0) $has_status_kawin = true;

$has_status_approval = false;
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM warga LIKE 'status_approval'");
if ($check_col && mysqli_num_rows($check_col) > 0) $has_status_approval = true;

// Get filters
$search = $_GET['search'] ?? '';
$rt_filter = $_GET['rt'] ?? '';
$rw_filter = $_GET['rw'] ?? '';
$status_filter = $_GET['status'] ?? '';
$approval_filter = $_GET['approval'] ?? '';
$format = $_GET['format'] ?? 'pdf';

// Build query
$select_fields = "w.id, w.nik, w.nama, w.jk, w.tanggal_lahir, w.pekerjaan, w.alamat, w.rt, w.rw, w.kk_id, w.status";
if ($has_tempat_lahir) $select_fields .= ", w.tempat_lahir";
if ($has_goldar) $select_fields .= ", w.goldar";
if ($has_agama) $select_fields .= ", w.agama";
if ($has_status_kawin) $select_fields .= ", w.status_kawin";
if ($has_status_approval) $select_fields .= ", w.status_approval";

$query = "SELECT $select_fields, rt.nama_rt, rw.name as nama_rw, kk.no_kk, kk.kepala_keluaraga 
          FROM warga w 
          LEFT JOIN rt ON w.rt = rt.id 
          LEFT JOIN rw ON w.rw = rw.id 
          LEFT JOIN kk ON w.kk_id = kk.id 
          WHERE 1=1";

$params = [];
$types = '';

if (!empty($search)) {
    $query .= " AND (w.nik LIKE ? OR w.nama LIKE ? OR w.alamat LIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param; $params[] = $search_param; $params[] = $search_param;
    $types .= 'sss';
}
if (!empty($rt_filter)) {
    $query .= " AND w.rt = ?";
    $params[] = $rt_filter;
    $types .= 'i';
}
if (!empty($rw_filter)) {
    $query .= " AND w.rw = ?";
    $params[] = $rw_filter;
    $types .= 'i';
}
if (!empty($status_filter)) {
    $query .= " AND w.status = ?";
    $params[] = $status_filter;
    $types .= 's';
}
if (!empty($approval_filter)) {
    $query .= " AND w.status_approval = ?";
    $params[] = $approval_filter;
    $types .= 's';
}

$query .= " ORDER BY w.nama ASC";

$stmt = mysqli_prepare($conn, $query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// CSV Export removed as per request - PDF only

// PDF Export
$single_warga = null;
if (isset($_GET['id'])) {
    $warga_id = (int)$_GET['id'];
    $single_query = "SELECT $select_fields, rt.nama_rt, rw.name as nama_rw, kk.no_kk, kk.kepala_keluaraga FROM warga w 
                     LEFT JOIN rt ON w.rt = rt.id LEFT JOIN rw ON w.rw = rw.id LEFT JOIN kk ON w.kk_id = kk.id WHERE w.id = ?";
    $single_stmt = mysqli_prepare($conn, $single_query);
    mysqli_stmt_bind_param($single_stmt, 'i', $warga_id);
    mysqli_stmt_execute($single_stmt);
    $single_result = mysqli_stmt_get_result($single_stmt);
    $single_warga = mysqli_fetch_assoc($single_result);
}

if ($single_warga) {
    // Single warga PDF (portrait, no logo)
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; margin: 30px; color: #333; line-height: 1.4; font-size: 12px; }
            .header { text-align: center; border-bottom: 3px solid #1e40af; padding-bottom: 15px; margin-bottom: 20px; }
            .title { color: #1e40af; font-size: 20pt; font-weight: bold; margin: 10px 0; }
            .subtitle { color: #64748b; font-size: 12pt; }
            .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 20px 0; }
            .detail-item { border-left: 4px solid #1e40af; padding-left: 12px; margin-bottom: 12px; }
            .label { font-weight: bold; color: #374151; display: block; margin-bottom: 4px; font-size: 11px; }
            .value { font-size: 14px; color: #111827; word-wrap: break-word; }
            .status { padding: 3px 10px; border-radius: 20px; font-weight: bold; font-size: 11px; }
            .aktif { background: #dcfce7; color: #166534; }
            .menunggu { background: #fef3c7; color: #92400e; }
            .diterima { background: #d1fae5; color: #065f46; }
            .ditolak { background: #fee2e2; color: #991b1b; }
            .footer { margin-top: 40px; text-align: center; color: #6b7280; font-size: 11px; border-top: 1px solid #e5e7eb; padding-top: 15px; }
            @page { margin: 0.5in; }
            .detail-item .value { text-align: left; }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="title">DETAIL WARGA</div>
            <div class="subtitle">Data Kartu Keluarga & Warga</div>
        </div>
        <div class="detail-grid">
            <div class="detail-item">
                <span class="label">NIK:</span>
                <span class="value">' . htmlspecialchars($single_warga['nik'] ?? '-') . '</span>
            </div>
            <div class="detail-item">
                <span class="label">Nama:</span>
                <span class="value">' . htmlspecialchars($single_warga['nama'] ?? '-') . '</span>
            </div>
            <div class="detail-item">
                <span class="label">Jenis Kelamin:</span>
                <span class="value">' . htmlspecialchars($single_warga['jk'] ?? '-') . '</span>
            </div>
            <div class="detail-item">
                <span class="label">Tanggal Lahir:</span>
                <span class="value">' . ($single_warga['tanggal_lahir'] ? date('d-m-Y', strtotime($single_warga['tanggal_lahir'])) : '-') . '</span>
            </div>';
    
    if ($has_tempat_lahir) {
        $html .= '
            <div class="detail-item">
                <span class="label">Tempat Lahir:</span>
                <span class="value">' . htmlspecialchars($single_warga['tempat_lahir'] ?? '-') . '</span>
            </div>';
    }
    if ($has_goldar) {
        $html .= '
            <div class="detail-item">
                <span class="label">Golongan Darah:</span>
                <span class="value">' . htmlspecialchars($single_warga['goldar'] ?? '-') . '</span>
            </div>';
    }
    if ($has_agama) {
        $html .= '
            <div class="detail-item">
                <span class="label">Agama:</span>
                <span class="value">' . htmlspecialchars($single_warga['agama'] ?? '-') . '</span>
            </div>';
    }
    if ($has_status_kawin) {
        $html .= '
            <div class="detail-item">
                <span class="label">Status Kawin:</span>
                <span class="value">' . htmlspecialchars($single_warga['status_kawin'] ?? '-') . '</span>
            </div>';
    }
    
    $html .= '
            <div class="detail-item">
                <span class="label">Pekerjaan:</span>
                <span class="value">' . htmlspecialchars($single_warga['pekerjaan'] ?? '-') . '</span>
            </div>
            <div class="detail-item">
                <span class="label">Alamat:</span>
                <span class="value">' . htmlspecialchars($single_warga['alamat'] ?? '-') . '</span>
            </div>
            <div class="detail-item">
                <span class="label">RT/RW:</span>
                <span class="value">' . htmlspecialchars(($single_warga['nama_rt'] ?? '-') . '/' . ($single_warga['nama_rw'] ?? '-')) . '</span>
            </div>
            <div class="detail-item">
                <span class="label">No KK:</span>
                <span class="value">' . htmlspecialchars($single_warga['no_kk'] ?? '-') . '</span>
            </div>
            <div class="detail-item">
                <span class="label">Kepala Keluarga:</span>
                <span class="value">' . htmlspecialchars($single_warga['kepala_keluaraga'] ?? '-') . '</span>
            </div>
            <div class="detail-item">
                <span class="label">Status:</span>
                <span class="value status ' . strtolower($single_warga['status'] ?? '') . '">' . htmlspecialchars(ucfirst($single_warga['status'] ?? '-')) . '</span>
            </div>';
    
    if ($has_status_approval) {
        $html .= '
            <div class="detail-item">
                <span class="label">Approval:</span>
                <span class="value status ' . strtolower($single_warga['status_approval'] ?? '') . '">' . htmlspecialchars(ucfirst($single_warga['status_approval'] ?? '-')) . '</span>
            </div>';
    }
    
    $html .= '
        </div>
        <div class="footer">
            Dicetak pada ' . date('d F Y H:i:s') . '
        </div>
    </body>
    </html>';
    
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream('detail_warga_' . $single_warga['nik'] . '_' . date('Y-m-d') . '.pdf', ['Attachment' => true]);
    exit();
} else {
    // List PDF (landscape table, no logo)
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
            .header { text-align: center; border-bottom: 3px solid #1e40af; padding-bottom: 15px; margin-bottom: 20px; }
            .title { color: #1e40af; font-size: 20pt; font-weight: bold; margin: 0; }
            .subtitle { color: #64748b; font-size: 12pt; margin: 5px 0 0 0; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #d1d5db; padding: 6px 8px; text-align: center; font-size: 10px; word-wrap: break-word; max-width: 1in; }
            th { background: #f9fafb; font-weight: bold; font-size: 9px; }
            td { font-size: 9px; }
            tr:nth-child(even) { background: #f9fafb; }
            .footer { margin-top: 20px; text-align: center; color: #6b7280; font-size: 11px; }
            @page { margin: 0.3in; }
            table { font-size: 9px; width: 100%; table-layout: fixed; }
            th, td { width: auto; }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="title">LAPORAN DATA WARGA</div>
            <div class="subtitle">' . date('d F Y H:i:s') . '</div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>JK</th>
                    <th>Tgl Lahir</th>';
    
    if ($has_tempat_lahir) $html .= '<th>Tempat Lahir</th>';
    if ($has_goldar) $html .= '<th>Gol. Darah</th>';
    if ($has_agama) $html .= '<th>Agama</th>';
    if ($has_status_kawin) $html .= '<th>Status Kawin</th>';
    
    $html .= '
                    <th>Pekerjaan</th>
                    <th>Alamat</th>
                    <th>RT/RW</th>
                    <th>No KK</th>
                    <th>Status</th>';
    
    if ($has_status_approval) $html .= '<th>Approval</th>';
    
    $html .= '
                </tr>
            </thead>
            <tbody>';
    
    $total = 0;
    mysqli_data_seek($result, 0);
    while ($row = mysqli_fetch_assoc($result)) {
        $total++;
        $html .= '
                <tr>
                    <td>' . htmlspecialchars($row['nik'] ?? '-') . '</td>
                    <td>' . htmlspecialchars($row['nama'] ?? '-') . '</td>
                    <td>' . htmlspecialchars($row['jk'] ?? '-') . '</td>
                    <td>' . ($row['tanggal_lahir'] ? date('d-m-Y', strtotime($row['tanggal_lahir'])) : '-') . '</td>';
        
        if ($has_tempat_lahir) $html .= '<td>' . htmlspecialchars($row['tempat_lahir'] ?? '-') . '</td>';
        if ($has_goldar) $html .= '<td>' . htmlspecialchars($row['goldar'] ?? '-') . '</td>';
        if ($has_agama) $html .= '<td>' . htmlspecialchars($row['agama'] ?? '-') . '</td>';
        if ($has_status_kawin) $html .= '<td>' . htmlspecialchars($row['status_kawin'] ?? '-') . '</td>';
        
        $html .= '
                    <td>' . htmlspecialchars($row['pekerjaan'] ?? '-') . '</td>
                    <td>' . htmlspecialchars(substr($row['alamat'] ?? '-', 0, 30) . (strlen($row['alamat'] ?? '') > 30 ? '...' : '')) . '</td>
                    <td>' . htmlspecialchars(($row['nama_rt'] ?? '-') . '/' . ($row['nama_rw'] ?? '-')) . '</td>
                    <td>' . htmlspecialchars($row['no_kk'] ?? '-') . '</td>
                    <td>' . htmlspecialchars(ucfirst($row['status'] ?? '-')) . '</td>';
        
        if ($has_status_approval) $html .= '<td>' . htmlspecialchars(ucfirst($row['status_approval'] ?? '-')) . '</td>';
        
        $html .= '
                </tr>';
    }
    
    $html .= '
            </tbody>
        </table>
        <div class="footer">
            Total Warga: ' . $total . ' | Filter: ' . (empty($search) ? 'Semua' : 'Search: ' . htmlspecialchars($search)) . ', RT: ' . htmlspecialchars($rt_filter ?: 'Semua') . ', RW: ' . htmlspecialchars($rw_filter ?: 'Semua') . '
        </div>
    </body>
    </html>';
    
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream('data_warga_' . date('Y-m-d') . '.pdf', ['Attachment' => true]);
    exit();
}
?>

