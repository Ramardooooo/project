<?php
include 'config/database.php';

$request_uri = $_SERVER['REQUEST_URI'];
$parsed_url = parse_url($request_uri);
$path = $parsed_url['path'];
$base = '/PROJECT/';
if (strpos($path, $base) === 0) {
    $page = substr($path, strlen($base));
} else {
    $page = trim($path, '/');
}
$page = $page ?: 'login';

if (!isset($_SESSION['user_id'])) {
    switch ($page) {
        case 'home':
            include 'home.php';
            break;
        case 'login':
            include 'auth/login.php';
            break;
        case 'register':
            include 'auth/register.php';
            break;
        default:
            header("Location: /PROJECT/home");
            exit();
    }
} else {
    switch ($page) {
        case 'home':
            include 'home.php';
            break;
        case 'logout':
            include 'auth/logout.php';
            break;
        case 'dashboard_admin':
            if ($_SESSION['role'] == 'admin') {
                include 'pages/admin/dashboard_admin.php';
            } else {
                echo "Access Denied";
            }
            break;
        case 'manage_users':
            if ($_SESSION['role'] == 'admin') {
                include 'pages/admin/manage_users.php';
            } else {
                echo "Access Denied";
            }
            break;
        case 'manage_rt_rw':
            if ($_SESSION['role'] == 'admin') {
                include 'pages/admin/manage_rt_rw.php';
            } else {
                echo "Access Denied";
            }
            break;
        case 'manage_master_data':
            if ($_SESSION['role'] == 'admin') {
                include 'pages/admin/manage_master_data.php';
            } else {
                echo "Access Denied";
            }
            break;
        case 'tambah_rt':
            if ($_SESSION['role'] == 'admin') {
                include 'pages/admin/tambah_rt.php';
            } else {
                echo "Access Denied";
            }
            break;
        case 'tambah_user':
            if ($_SESSION['role'] == 'admin') {
                include 'pages/admin/tambah_user.php';
            } else {
                echo "Access Denied";
            }
            break;
        case 'edit_rt':
            if ($_SESSION['role'] == 'admin') {
                include 'pages/admin/edit_rt.php';
            } else {
                echo "Access Denied";
            }
            break;
        case 'edit_user':
            if ($_SESSION['role'] == 'admin') {
                include 'pages/admin/edit_user.php';
            } else {
                echo "Access Denied";
            }
            break;
        case 'tambah_warga':
            if ($_SESSION['role'] == 'admin') {
                include 'pages/admin/tambah_warga.php';
            } else {
                echo "Access Denied";
            }
            break;
        case 'edit_warga':
            if ($_SESSION['role'] == 'admin') {
                include 'pages/admin/edit_warga.php';
            } else {
                echo "Access Denied";
            }
            break;
        case 'tambah_kk':
            if ($_SESSION['role'] == 'admin') {
                include 'pages/admin/tambah_kk.php';
            } else {
                echo "Access Denied";
            }
            break;
        case 'edit_kk':
            if ($_SESSION['role'] == 'admin') {
                include 'pages/admin/edit_kk.php';
            } else {
                echo "Access Denied";
            }
            break;

        case 'dashboard_ketua':
            if ($_SESSION['role'] == 'ketua') {
                include 'pages/ketua/dashboard_ketua.php';
            } else {
                echo "Access Denied";
            }
            break;
        case 'dashboard_user':
            if ($_SESSION['role'] == 'user') {
                include 'pages/user/dashboard_user.php';
            } else {
                echo "Access Denied";
            }
            break;
        default:
            if ($_SESSION['role'] == 'admin') {
                header("Location: /PROJECT/dashboard_admin");
            } elseif ($_SESSION['role'] == 'ketua') {
                header("Location: /PROJECT/dashboard_ketua");
            } else {
                header("Location: /PROJECT/dashboard_user");
            }
            exit();
    }
}
?>
