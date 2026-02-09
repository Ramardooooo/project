<?php

include '../../config/database.php';
include '../../layouts/admin/header.php';
include '../../layouts/admin/sidebar.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: home");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_gallery'])) {

        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

            $upload_dir = '../../beranda/gallery/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {

                $image_path = $file_name;

                $query = "INSERT INTO gallery (title, description, image_path) 
                          VALUES ('$title', '$description', '$image_path')";

                if (mysqli_query($conn, $query)) {
                    $success = "Gallery item added successfully!";
                } else {
                    $error = "Error adding gallery item: " . mysqli_error($conn);
                }

            } else {
                $error = "Error uploading image.";
            }

        } else {
            $error = "Please select an image to upload.";
        }

    } elseif (isset($_POST['delete_gallery'])) {

        $id = (int) $_POST['id'];

        $query = "SELECT image_path FROM gallery WHERE id = $id";
        $result = mysqli_query($conn, $query);

        if ($row = mysqli_fetch_assoc($result)) {

            if (file_exists('../../' . $row['image_path'])) {
                unlink('../../' . $row['image_path']);
            }

            mysqli_query($conn, "DELETE FROM gallery WHERE id = $id");
            $success = "Gallery item deleted successfully!";
        }
    }
}

$search = isset($_GET['search']) 
    ? mysqli_real_escape_string($conn, $_GET['search']) 
    : '';

$where_clause = '';

if (!empty($search)) {
    $where_clause = "WHERE title LIKE '%$search%' 
                     OR description LIKE '%$search%'";
}

$query = "SELECT * FROM gallery 
          $where_clause 
          ORDER BY created_at DESC";

$result = mysqli_query($conn, $query);
$gallery_items = mysqli_fetch_all($result, MYSQLI_ASSOC);

?>

<div id="mainContent" class="ml-64 min-h-screen bg-blue-900">

    <div class="p-8">

        <h1 class="text-4xl font-extrabold mb-8 text-white drop-shadow-lg">
            Kelola Galeri
        </h1>

        <?php if (isset($success)) : ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)) : ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white/90 backdrop-blur-md rounded-2xl shadow-lg p-7 mb-8 border border-white/20 hover:shadow-2xl hover:bg-white/95 transition-all duration-300">

            <h3 class="text-xl font-bold mb-6 text-black drop-shadow-lg flex items-center gap-2">
                <i class="fas fa-plus-circle text-green-600"></i>
                Tambah Item Galeri
            </h3>

            <form method="POST" enctype="multipart/form-data" class="space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-heading text-gray-500"></i>
                        Judul
                    </label>

                    <input type="text" 
                           name="title" 
                           required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-align-left text-gray-500"></i>
                        Deskripsi
                    </label>

                    <textarea name="description" 
                              rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-image text-gray-500"></i>
                        Gambar
                    </label>

                    <input type="file" 
                           name="image" 
                           accept="image/*" 
                           required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <button type="submit" 
                        name="add_gallery" 
                        class="w-full py-3 rounded-xl font-semibold text-white bg-gradient-to-r from-green-400 to-emerald-600 hover:scale-105 transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    Tambah Galeri
                </button>

            </form>
        </div>
