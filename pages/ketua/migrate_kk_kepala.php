<?php
include '../../config/database.php';

echo "<h2>Migrate KK Kepala Keluarga ID</h2>";

// 1. Check if column exists
$check = mysqli_query($conn, "SHOW COLUMNS FROM kk LIKE 'kepala_keluarga_id'");
if (mysqli_num_rows($check) == 0) {
    $sql = "ALTER TABLE kk ADD COLUMN kepala_keluarga_id INT NULL, ADD FOREIGN KEY (kepala_keluarga_id) REFERENCES warga(id)";
    if (mysqli_query($conn, $sql)) {
        echo "<p style='color:green;'>✅ Column kepala_keluarga_id added.</p>";
    } else {
        echo "<p style='color:red;'>❌ Add column failed: " . mysqli_error($conn) . "</p>";
        exit;
    }
} else {
    echo "<p>Column already exists.</p>";
}

// 2. Backfill data
$kk_list = mysqli_query($conn, "SELECT id, kepala_keluaraga FROM kk WHERE kepala_keluarga_id IS NULL");
$updated = 0;
$failed = 0;

while ($kk = mysqli_fetch_assoc($kk_list)) {
    $nama = mysqli_real_escape_string($conn, trim($kk['kepala_keluaraga']));
    if (!empty($nama)) {
        // Find matching warga by nama (exact match, case insensitive)
        $warga = mysqli_query($conn, "SELECT id FROM warga WHERE TRIM(LOWER(nama)) = LOWER('$nama') AND status = 'aktif' LIMIT 1");
        if ($w = mysqli_fetch_assoc($warga)) {
            $update_sql = "UPDATE kk SET kepala_keluarga_id = " . (int)$w['id'] . " WHERE id = " . (int)$kk['id'];
            if (mysqli_query($conn, $update_sql)) {
                $updated++;
            } else {
                $failed++;
            }
        } else {
            $failed++;
            echo "<p>No matching warga for KK {$kk['id']}: {$kk['kepala_keluaraga']}</p>";
        }
    }
}

echo "<p>✅ Migration complete: Updated $updated, failed $failed.</p>";
echo "<a href='../manage_kk.php'>Back to Manage KK</a>";
?>

