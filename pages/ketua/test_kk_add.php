<?php
include '../../config/database.php';
if (isset($_POST['test_add'])) {
    $no_kk = 'TEST-' . time();
    $kepala_keluaraga = $_POST['kepala_keluaraga'];
    $query = "INSERT INTO kk (no_kk, kepala_keluaraga) VALUES ('$no_kk', '$kepala_keluaraga')";
    if (mysqli_query($conn, $query)) {
        echo "Success! KK ID: " . mysqli_insert_id($conn) . "<br>";
    } else {
        echo "Error: " . mysqli_error($conn) . "<br>";
    }
}
?>
<!DOCTYPE html>
<html>
<body>
<form method="POST">
No KK: <input name="no_kk" value="TEST<?php echo time(); ?>"><br>
Kepala Keluarga: <input name="kepala_keluaraga" value="Test Warga">
<button name="test_add">Test Add KK</button>
</form>
<hr>
<a href="../manage_kk.php">Back to Manage KK</a>
</body>
</html>

