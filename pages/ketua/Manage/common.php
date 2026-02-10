<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ketua') {
    header("Location: ../../../home.php");
    exit();
}

include '../../../config/database.php';
include '../../../layouts/ketua/header.php';
include '../../../layouts/ketua/sidebar.php';
?>
