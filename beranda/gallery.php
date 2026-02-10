

<?php
include __DIR__ . '/../config/database.php';

$query = "SELECT g.*, 
          COUNT(l.id) as like_count,
          CASE WHEN EXISTS(SELECT 1 FROM gallery_likes gl WHERE gl.gallery_id = g.id AND gl.user_id = ?) THEN 1 ELSE 0 END as user_liked
          FROM gallery g 
          LEFT JOIN gallery_likes l ON g.id = l.gallery_id 
          GROUP BY g.id 
          ORDER BY g.created_at DESC";

$stmt = mysqli_prepare($conn, $query);
$user_id = $_SESSION['user_id'] ?? null;
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$gallery_items = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<section id="gallery" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">
                Galeri Kegiatan
            </h2>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                Dokumentasi kegiatan dan momen berharga di lingkungan RT/RW kami
            </p>
        </div>
        <?php if (empty($gallery_items)): ?>
            <div class="text-center py-16">
                <div class="bg-gradient-to-br from-gray-100 to-gray-200 rounded-full w-32 h-32 mx-auto mb-6 flex items-center justify-center">
                    <i class="fas fa-images text-5xl text-gray-400"></i>
                </div>
                <p class="text-gray-500 text-xl">Belum ada galeri yang diupload.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($gallery_items as $item): ?>
                    <div class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-500 border border-gray-100">
                        <div class="relative overflow-hidden">
                            <img src="beranda/gallery/<?php echo $item['image_path']; ?>"
                                 alt="<?php echo $item['title']; ?>"
                                 class="gallery-image w-full h-64 object-cover cursor-pointer transition-transform duration-500 group-hover:scale-110"
                                 onclick="openModal('<?php echo $item['image_path']; ?>', '<?php echo $item['title']; ?>', '<?php echo $item['description']; ?>', '<?php echo date('d M Y', strtotime($item['created_at'])); ?>')"
                                 data-title="<?php echo $item['title']; ?>"
                                 data-description="<?php echo $item['description']; ?>"
                                 data-date="<?php echo date('d M Y', strtotime($item['created_at'])); ?>">

                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500">
                                <div class="absolute bottom-0 left-0 right-0 p-6 transform translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                                    <h3 class="text-white font-bold text-lg mb-2"><?php echo $item['title']; ?></h3>
                                    <p class="text-gray-200 text-sm line-clamp-2"><?php echo $item['description']; ?></p>
                                    <div class="flex items-center mt-3 text-gray-300 text-sm">
                                        <i class="fas fa-calendar-alt mr-2"></i>
                                        <?php echo date('d M Y', strtotime($item['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors duration-300"><?php echo $item['title']; ?></h3>
                            <p class="text-gray-600 mb-4 line-clamp-2"><?php echo $item['description']; ?></p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <button class="like-btn flex items-center space-x-1 <?php echo $item['user_liked'] ? 'text-red-500' : 'text-gray-400'; ?> hover:text-red-500 transition-colors duration-300"
                                            data-gallery-id="<?php echo $item['id']; ?>">
                                        <i class="fas fa-heart"></i>
                                        <span class="like-count"><?php echo $item['like_count']; ?></span>
                                    </button>
                                    <span onclick="openModal('<?php echo $item['image_path']; ?>', '<?php echo $item['title']; ?>', '<?php echo $item['description']; ?>', '<?php echo date('d M Y', strtotime($item['created_at'])); ?>')" class="text-gray-500 cursor-pointer hover:text-blue-500 transition-colors duration-300">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-calendar-alt mr-2"></i><?php echo date('d M Y', strtotime($item['created_at'])); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<div id="gallery-modal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden relative">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 id="modal-title" class="text-2xl font-bold text-gray-800"></h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-300">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <img id="modal-image" src="" alt="" class="w-full h-auto max-h-[60vh] object-contain rounded-lg">
            <div class="mt-4">
                <p id="modal-description" class="text-gray-600 mb-2"></p>
                <p id="modal-date" class="text-sm text-gray-500">
                    <i class="fas fa-calendar-alt mr-2"></i>
                </p>
            </div>
        </div>
    </div>
</div>

<script src="beranda/script/gallery.js"></script>
<script src="beranda/script/like.js"></script>
