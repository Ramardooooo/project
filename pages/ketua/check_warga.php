<?php
include '../../config/database.php';
echo "Active warga:\n";
$result = mysqli_query($conn, 'SELECT id, nama, nik, status FROM warga WHERE status = "aktif" LIMIT 10');
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo $row['id'] . ' | ' . $row['nama'] . ' | ' . ($row['nik'] ?? 'no nik') . "\n";
    }
} else {
    echo "No active warga found or query failed.\n";
}
$total = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) as c FROM warga WHERE status = "aktif"'));
echo 'Total active: ' . $total['c'] . "\n";
?>
