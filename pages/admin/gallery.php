<?php
include '../../config/database.php';
include '../../layouts/admin/header.php';
include '../../layouts/admin/sidebar.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
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

                $query = "INSERT INTO gallery (title, description, image_path) VALUES ('$title', '$description', '$image_path')";
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
        $id = (int)$_POST['id'];
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


$query = "SELECT * FROM gallery ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
$gallery_items = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div id="mainContent" class="ml-64 min-h-screen bg-blue-900">
<div class="p-8">

<h1 class="text-4xl font-extrabold mb-8 text-white drop-shadow-lg">Kelola Galeri</h1>

<?php if (isset($success)): ?>
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
    <?php echo $success; ?>
</div>
<?php endif; ?>

<?php if (isset($error)): ?>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
    <?php echo $error; ?>
</div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-200">
    <h3 class="text-xl font-bold mb-6 text-black drop-shadow-lg">Tambah Item Galeri</h3>
    <form method="POST" enctype="multipart/form-data" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
            <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
            <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Gambar</label>
            <input type="file" name="image" accept="image/*" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit" name="add_gallery" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors">
            Tambah Galeri
        </button>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
    <h3 class="text-xl font-bold mb-6 text-black drop-shadow-lg">Daftar Galeri</h3>
    <?php if (empty($gallery_items)): ?>
    <p class="text-gray-500">Belum ada item galeri.</p>
    <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($gallery_items as $item): ?>
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <img src="beranda/gallery/<?php echo str_replace(['uploads/gallery/', 'beranda/gallery/'], '', $item['image_path']); ?>" alt="<?php echo $item['title']; ?>" class="w-full h-48 object-cover rounded-md mb-4">
            <h4 class="font-semibold text-gray-800 mb-2"><?php echo $item['title']; ?></h4>
            <p class="text-sm text-gray-600 mb-4"><?php echo $item['description']; ?></p>
            <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item ini?')">
                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                <button type="submit" name="delete_gallery" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">
                    Hapus
                </button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

</div>
</div>

<?php include '../../layouts/admin/footer.php'; ?>
