<?php
$conn = mysqli_connect("localhost","root","");
if(!$conn){ die("DB Error"); }

mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS rt_testing");
mysqli_select_db($conn, "rt_testing");

$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'ketua', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

mysqli_query($conn, $sql);

$admin_password = password_hash('admin', PASSWORD_DEFAULT);
$sql_admin = "REPLACE INTO users (id, username, email, password, role) VALUES (1, 'admin', 'admin@lurago.id', '$admin_password', 'admin')";

mysqli_query($conn, $sql_admin);
?>
