<?php
include '../../../config/database.php';
include 'wilayah/manage_wilayah_handler.php';
include 'common.php';

// Pagination settings
$items_per_page = 10;

// RT Pagination
$rt_page = isset($_GET['rt_page']) ? (int)$_GET['rt_page'] : 1;
$rt_offset = ($rt_page - 1) * $items_per_page;

// Get total RT count
$rt_total_query = "SELECT COUNT(*) as total FROM rt";
$rt_total_result = mysqli_query($conn, $rt_total_query);
$rt_total_row = mysqli_fetch_assoc($rt_total_result);
$rt_total = $rt_total_row['total'];
$rt_total_pages = ceil($rt_total / $items_per_page);

// RT Query with pagination
$rt_query = "SELECT rt.*, rw.name as nama_rw, COUNT(w.id) as jumlah_warga
             FROM rt
             LEFT JOIN rw ON rt.id_rw = rw.id
             LEFT JOIN warga w ON rt.id = w.rt AND w.status = 'aktif'
             GROUP BY rt.id
             ORDER BY rt.nama_rt
             LIMIT $items_per_page OFFSET $rt_offset";
$rt_result = mysqli_query($conn, $rt_query);

// RW Pagination
$rw_page = isset($_GET['rw_page']) ? (int)$_GET['rw_page'] : 1;
$rw_offset = ($rw_page - 1) * $items_per_page;

// Get total RW count
$rw_total_query = "SELECT COUNT(*) as total FROM rw";
$rw_total_result = mysqli_query($conn, $rw_total_query);
$rw_total_row = mysqli_fetch_assoc($rw_total_result);
$rw_total = $rw_total_row['total'];
$rw_total_pages = ceil($rw_total / $items_per_page);

// RW Query with pagination
$rw_query = "SELECT rw.*, COUNT(rt.id) as jumlah_rt, SUM(rt_count.jumlah_warga) as total_warga
             FROM rw
             LEFT JOIN rt ON rw.id = rt.id_rw
             LEFT JOIN (
                 SELECT rt.id, COUNT(w.id) as jumlah_warga
                 FROM rt
                 LEFT JOIN warga w ON rt.id = w.rt AND w.status = 'aktif'
                 GROUP BY rt.id
             ) rt_count ON rt.id = rt_count.id
             GROUP BY rw.id
             ORDER BY rw.name
             LIMIT $items_per_page OFFSET $rw_offset";
$rw_result = mysqli_query($conn, $rw_query);

include 'wilayah/manage_wilayah_view.php';
?>
