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
                        class="px-6 py-2 rounded-lg font-semibold text-white bg-gradient-to-r from-green-400 to-emerald-600 hover:scale-105 transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    Tambah Galeri
                </button>

            </form>
        </div>

        <div class="bg-white/90 backdrop-blur-md rounded-2xl shadow-lg p-7 border border-white/20 hover:shadow-2xl hover:bg-white/95 transition-all duration-300">

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-black drop-shadow-lg flex items-center gap-2">
                    <i class="fas fa-images text-blue-600"></i>
                    Daftar Galeri
                </h3>

                <form method="GET" class="flex gap-2">
                    <input type="text"
                           name="search"
                           value="<?php echo htmlspecialchars($search); ?>"
                           placeholder="Cari galeri..."
                           class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <?php if (empty($gallery_items)) : ?>
                <div class="text-center py-8">
                    <i class="fas fa-image text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">Belum ada item galeri</p>
                </div>
            <?php else : ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($gallery_items as $item) : ?>
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 hover:shadow-lg transition-all duration-300 group">
                            <div class="aspect-w-16 aspect-h-9 mb-4 overflow-hidden rounded-lg">
                                <img src="/PROJECT/beranda/gallery/<?php echo htmlspecialchars($item['image_path']); ?>"
                                     alt="<?php echo htmlspecialchars($item['title']); ?>"
                                     class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>

                            <div class="space-y-2">
                                <h4 class="font-semibold text-gray-800 text-sm"><?php echo htmlspecialchars($item['title']); ?></h4>
                                <p class="text-xs text-gray-600 line-clamp-2"><?php echo htmlspecialchars($item['description']); ?></p>
                                <p class="text-xs text-gray-400"><?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?></p>
                            </div>

                            <div class="flex gap-2 mt-4">
                                <button onclick="openPreview('<?php echo htmlspecialchars($item['image_path']); ?>', '<?php echo htmlspecialchars($item['title']); ?>', '<?php echo htmlspecialchars($item['description']); ?>', '<?php echo date('d M Y', strtotime($item['created_at'])); ?>')"
                                        class="flex-1 px-3 py-2 bg-blue-500 text-white text-xs rounded-md hover:bg-blue-600 transition-colors flex items-center justify-center gap-1">
                                    <i class="fas fa-eye"></i>
                                    Preview
                                </button>

                                <form method="POST" class="flex-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item ini?')">
                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                    <button type="submit"
                                            name="delete_gallery"
                                            class="w-full px-3 py-2 bg-red-500 text-white text-xs rounded-md hover:bg-red-600 transition-colors flex items-center justify-center gap-1">
                                        <i class="fas fa-trash"></i>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>

        <!-- Modal Preview -->
        <div id="previewModal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-800" id="modalTitle"></h3>
                        <button onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <div class="mb-4">
                        <img id="modalImage" src="" alt="" class="w-full h-auto max-h-[60vh] object-contain rounded-lg">
                    </div>

                    <div class="mt-4">
                        <p id="modalDescription" class="text-gray-600 mb-2"></p>
                        <p id="modalDate" class="text-sm text-gray-500">
                            <i class="fas fa-calendar-alt mr-2"></i>
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function openPreview(imagePath, title, description, date) {
    document.getElementById('modalImage').src = '/PROJECT/beranda/gallery/' + imagePath;
    document.getElementById('modalImage').alt = title;
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalDescription').textContent = description;
    document.getElementById('modalDate').innerHTML = '<i class="fas fa-calendar-alt mr-2"></i>' + date;
    document.getElementById('previewModal').classList.remove('hidden');
    document.getElementById('previewModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closePreview() {
    document.getElementById('previewModal').classList.add('hidden');
    document.getElementById('previewModal').classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('previewModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePreview();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePreview();
    }
});
</script>
