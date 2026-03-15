<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ketua') {
    header("Location: ../../home.php");
    exit();
}

include '../../config/database.php';
require_once '../../vendor/autoload.php';

// Check dynamic columns (same as manage_warga.php)
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
$format = $_GET['format'] ?? 'pdf'; // pdf or csv

// Build query (same as manage_warga.php)
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

if ($format === 'csv') {
    // CSV Export
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="data_warga_' . date('Y-m-d_H-i') . '.csv"');
    $output = fopen('php://output', 'w');
    
    // Headers
    $headers = ['NIK', 'Nama', 'JK', 'Tgl Lahir', 'Tempat Lahir', 'Gol Darah', 'Agama', 'Status Kawin', 'Pekerjaan', 'Alamat', 'RT', 'RW', 'No KK', 'Kepala Keluarga', 'Status', 'Approval'];
    if (!$has_tempat_lahir) unset($headers[4]);
    if (!$has_goldar) unset($headers[5]);
    if (!$has_agama) unset($headers[6]);
    if (!$has_status_kawin) unset($headers[7]);
    if (!$has_status_approval) array_pop($headers);
    fputcsv($output, $headers);
    
    // Data rows
    while ($row = mysqli_fetch_assoc($result)) {
        $row_data = [
            $row['nik'] ?? '',
            $row['nama'] ?? '',
            $row['jk'] ?? '',
            $row['tanggal_lahir'] ? date('d-m-Y', strtotime($row['tanggal_lahir'])) : '',
            $row['tempat_lahir'] ?? '',
            $row['goldar'] ?? '',
            $row['agama'] ?? '',
            $row['status_kawin'] ?? '',
            $row['pekerjaan'] ?? '',
            $row['alamat'] ?? '',
            $row['nama_rt'] ?? '',
            $row['nama_rw'] ?? '',
            $row['no_kk'] ?? '',
            $row['kepala_keluaraga'] ?? '',
            $row['status'] ?? '',
            $row['status_approval'] ?? ''
        ];
        if (!$has_tempat_lahir) array_splice($row_data, 4, 1);
        if (!$has_goldar) array_splice($row_data, 5, 1);
        if (!$has_agama) array_splice($row_data, 6, 1);
        if (!$has_status_kawin) array_splice($row_data, 7, 1);
        if (!$has_status_approval) array_pop($row_data);
        fputcsv($output, $row_data);
    }
    fclose($output);
    exit();
} else {
    // PDF Export (enhanced from manage_warga.php)
    $dompdf = new \Dompdf\Dompdf();
    
    $html = '
    <h1 style="text-align: center; color: #1f2937; margin-bottom: 30px;">Data Warga ' . htmlspecialchars($search ?: 'Lengkap') . '</h1>
    <p style="text-align: center; color: #6b7280; margin-bottom: 30px;">
        Di generate pada ' . date('d F Y H:i:s') . '<br>
        Filter: ' . htmlspecialchars("Search: $search, RT: $rt_filter, RW: $rw_filter, Status: $status_filter, Approval: $approval_filter") . '
    </p>
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background-color: #f3f4f6;">
                <th style="border: 1px solid #d1d5db; padding: 12px; text-align: left; font-weight: bold;">NIK</th>
                <th style="border: 1px solid #d1d5db; padding: 12px; text-align: left; font-weight: bold;">Nama</th>
                <th style="border: 1px solid #d1d5db; padding: 12px; text-align: left; font-weight: bold;">JK</th>
                <th style="border: 1px solid #d1d5db; padding: 12px; text-align: left; font-weight: bold;">Tgl Lahir</th>';
    
    if ($has_tempat_lahir) $html .= '<th style="border: 1px solid #d1d5db; padding: 12px; text-align: left; font-weight: bold;">Tempat Lahir</th>';
    if ($has_goldar) $html .= '<th style="border: 1px solid #d1d5db; padding: 12px; text-align: left; font-weight: bold;">Gol. Darah</th>';
    if ($has_agama) $html .= '<th style="border: 1px solid #d1d5db; padding: 12px; text-align: left; font-weight: bold;">Agama</th>';
    if ($has_status_kawin) $html .= '<th style="border: 1px solid #d1d5db; padding: 12px; text-align: left; font-weight: bold;">Status Kawin</th>';
    $html .= '
                <th style="border: 1px solid #d1d5db; padding: 12px; text-align: left; font-weight: bold;">Pekerjaan</th>
                <th style="border: 1px solid #d1d5db; padding: 12px; text-align: left; font-weight: bold;">Alamat</th>
                <th style="border: 1px solid #d1d5db; padding: 12px; text-align: left; font-weight: bold;">RT/RW</th>
                <th style="border: 1px solid #d1d5db; padding: 12px; text-align: left; font-weight: bold;">No KK</th>
                <th style="border: 1px solid #d1d5db; padding: 12px; text-align: left; font-weight: bold;">Status</th>';
    if ($has_status_approval) $html .= '<th style="border: 1px solid #d1d5db; padding: 12px; text-align: left; font-weight: bold;">Approval</th>';
    $html .= '
            </tr>
        </thead>
        <tbody>';
    
    $total = 0;
    mysqli_data_seek($result, 0); // Reset result
    while ($row = mysqli_fetch_assoc($result)) {
        $total++;
        $html .= '
            <tr>
                <td style="border: 1px solid #d1d5db; padding: 10px;">' . htmlspecialchars($row['nik'] ?? '-') . '</td>
                <td style="border: 1px solid #d1d5db; padding: 10px;">' . htmlspecialchars($row['nama'] ?? '-') . '</td>
                <td style="border: 1px solid #d1d5db; padding: 10px;">' . ($row['jk'] ?? '-') . '</td>
                <td style="border: 1px solid #d1d5db; padding: 10px;">' . ($row['tanggal_lahir'] ? date('d-m-Y', strtotime($row['tanggal_lahir'])) : '-') . '</td>';
        
        if ($has_tempat_lahir) $html .= '<td style="border: 1px solid #d1d5db; padding: 10px;">' . htmlspecialchars($row['tempat_lahir'] ?? '-') . '</td>';
        if ($has_goldar) $html .= '<td style="border: 1px solid #d1d5db; padding: 10px;">' . htmlspecialchars($row['goldar'] ?? '-') . '</td>';
        if ($has_agama) $html .= '<td style="border: 1px solid #d1d5db; padding: 10px;">' . htmlspecialchars($row['agama'] ?? '-') . '</td>';
        if ($has_status_kawin) $html .= '<td style="border: 1px solid #d1d5db; padding: 10px;">' . htmlspecialchars($row['status_kawin'] ?? '-') . '</td>';
        
        $html .= '
                <td style="border: 1px solid #d1d5db; padding: 10px;">' . htmlspecialchars($row['pekerjaan'] ?? '-') . '</td>
                <td style="border: 1px solid #d1d5db; padding: 10px;">' . htmlspecialchars(substr($row['alamat'] ?? '-', 0, 40)) . (strlen($row['alamat'] ?? '') > 40 ? '...' : '') . '</td>
                <td style="border: 1px solid #d1d5db; padding: 10px;">' . htmlspecialchars(($row['nama_rt'] ?? '-') . '/' . ($row['nama_rw'] ?? '-')) . '</td>
                <td style="border: 1px solid #d1d5db; padding: 10px;">' . htmlspecialchars($row['no_kk'] ?? '-') . '</td>
                <td style="border: 1px solid #d1d5db; padding: 10px;">' . htmlspecialchars(ucfirst($row['status'] ?? '-')) . '</td>';
        
        if ($has_status_approval) $html .= '<td style="border: 1px solid #d1d5db; padding: 10px;">' . htmlspecialchars(ucfirst($row['status_approval'] ?? '-')) . '</td>';
        $html .= '
            </tr>';
    }
    
    $html .= '
        </tbody>
    </table>
    <div style="margin-top: 40px; text-align: center; color: #6b7280; font-size: 14px;">
        Total Warga: ' . $total . ' | ' . date('d F Y H:i:s') . '
    </div>';
    
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("data_warga_" . date('Y-m-d') . ".pdf", ["Attachment" => true]);
    exit();
}
?>

