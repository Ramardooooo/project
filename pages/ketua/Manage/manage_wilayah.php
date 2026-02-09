<?php
include '../../../config/database.php';
include 'common.php';
include 'wilayah/manage_wilayah_handler.php';

$rt_query = "SELECT rt.*, rw.name as nama_rw, COUNT(w.id) as jumlah_warga
             FROM rt
             LEFT JOIN rw ON rt.id_rw = rw.id
             LEFT JOIN warga w ON rt.id = w.rt AND w.status = 'aktif'
             GROUP BY rt.id
             ORDER BY rt.nama_rt";
$rt_result = mysqli_query($conn, $rt_query);

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
             ORDER BY rw.name";
$rw_result = mysqli_query($conn, $rw_query);

include 'wilayah/manage_wilayah_view.php';
?>
