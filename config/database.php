<?php
$conn = mysqli_connect("localhost", "root", "", "rt_testing");

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

?>
