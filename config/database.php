<?php
// Connect without database to create it if needed
$conn = mysqli_connect("localhost","root","");
if(!$conn){ die("DB Error"); }

// Create database if not exists
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS rt_testing");
mysqli_select_db($conn, "rt_testing");

// Create users table if not exists
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'ketua', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

mysqli_query($conn, $sql);

// Create other tables
$sql_warga = "CREATE TABLE IF NOT EXISTS warga (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
)";
mysqli_query($conn, $sql_warga);

$sql_kk = "CREATE TABLE IF NOT EXISTS kk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
)";
mysqli_query($conn, $sql_kk);

$sql_rt = "CREATE TABLE IF NOT EXISTS rt (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
)";
mysqli_query($conn, $sql_rt);

$sql_rw = "CREATE TABLE IF NOT EXISTS rw (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
)";
mysqli_query($conn, $sql_rw);

// Insert default admin user (replace if exists)
$admin_password = password_hash('admin', PASSWORD_DEFAULT);
$sql_admin = "REPLACE INTO users (id, username, email, password, role) VALUES (1, 'admin', 'admin@lurago.id', '$admin_password', 'admin')";

mysqli_query($conn, $sql_admin);
?>
