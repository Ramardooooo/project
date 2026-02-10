<?php
include '../../config/database.php';


$audit_logs = mysqli_query($conn, "SELECT * FROM audit_log ORDER BY created_at DESC");

header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="audit_log_' . date('Y-m-d_H-i-s') . '.txt"');

echo "Audit Log Export - " . date('Y-m-d H:i:s') . "\n";
echo "=====================================\n\n";

while ($log = mysqli_fetch_assoc($audit_logs)) {
    echo "ID: " . $log['id'] . "\n";
    echo "Action: " . $log['action'] . "\n";
    echo "Table: " . $log['table_name'] . "\n";
    echo "Record ID: " . $log['record_id'] . "\n";
    echo "User: " . $log['username'] . " (ID: " . ($log['user_id'] ?? 'N/A') . ")\n";
    echo "Timestamp: " . $log['created_at'] . "\n";
    if ($log['old_value']) {
        echo "Old Value: " . $log['old_value'] . "\n";
    }
    if ($log['new_value']) {
        echo "New Value: " . $log['new_value'] . "\n";
    }
    echo "-------------------------------------\n";
}

mysqli_close($conn);
?>
