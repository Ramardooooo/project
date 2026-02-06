<?php
include 'config/database.php';
?>

<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">

        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">
                Galeri Kegiatan
            </h2>
            <div class="w-24 h-1 bg-blue-500 mx-auto rounded-full mb-6"></div>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Dokumentasi kegiatan dan momen berharga di lingkungan RT/RW kami
            </p>
        </div>

        <?php
        $query = "SELECT * FROM gallery ORDER BY created_at DESC";
        $result = mysqli_query($conn, $query);
        $gallery_items = mysqli_fetch_all($result, MYSQLI_ASSOC);
        ?>

        <?php if (empty($gallery_items)): ?>
        <div class="text-center py-20">
            <div class="bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-md rounded-3xl p-12 border border-white/20 max-w-md mx-auto">
                <div class="text-6xl text-gray-400 mb-6">
                    <i class="fas fa-images"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Galeri Kosong</h3>
                <p class="text-gray-400">Belum ada galeri yang diupload. Ayo mulai dokumentasikan momen-momen indah!</p>
            </div>
        </div>
        <?php else: ?>
        <!-- Masonry Grid -->
        <div class="columns-1 md:columns-2 lg:columns-3 xl:columns-4 gap-6 space-y-6">
            <?php foreach ($gallery_items as $index => $item): ?>
            <div class="gallery-item break-inside-avoid group relative overflow-hidden rounded-3xl bg-white/5 backdrop-blur-sm border border-white/10 hover:border-white/30 transition-all duration-500 hover:transform hover:scale-[1.02] hover:-translate-y-2">
                <!-- Image -->
                <div class="relative overflow-hidden">
                    <img src="beranda/gallery/<?php echo $item['image_path']; ?>"
                         alt="<?php echo $item['title']; ?>"
                         class="gallery-image w-full h-auto object-cover cursor-pointer transition-all duration-700 group-hover:scale-110 group-hover:brightness-110"
                         data-title="<?php echo $item['title']; ?>"
                         data-description="<?php echo $item['description']; ?>"
                         data-date="<?php echo date('d M Y', strtotime($item['created_at'])); ?>"
                         loading="lazy">

                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="bg-white/20 backdrop-blur-md rounded-full p-4 transform scale-75 group-hover:scale-100 transition-transform duration-300">
                                <i class="fas fa-expand text-white text-2xl"></i>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="absolute bottom-0 left-0 right-0 p-6 transform translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                            <h3 class="text-white font-bold text-lg mb-2"><?php echo $item['title']; ?></h3>
                            <p class="text-gray-200 text-sm line-clamp-2"><?php echo $item['description']; ?></p>
                            <div class="flex items-center mt-3 text-gray-300 text-sm">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <?php echo date('d M Y', strtotime($item['created_at'])); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Floating elements -->
                    <div class="absolute top-4 right-4 bg-white/20 backdrop-blur-md rounded-full w-10 h-10 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <i class="fas fa-heart text-white text-sm"></i>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- Premium Gallery Modal -->
<div id="gallery-modal" class="fixed inset-0 bg-black/95 backdrop-blur-sm hidden z-50">
    <!-- Close button -->
    <button class="close-modal absolute top-6 right-6 text-white hover:text-gray-300 bg-black/50 hover:bg-black/70 rounded-full w-14 h-14 flex items-center justify-center z-40 transition-all duration-300 shadow-2xl">
        <i class="fas fa-times text-xl"></i>
    </button>

    <!-- Navigation buttons -->
    <button id="prev-btn" class="absolute left-6 top-1/2 transform -translate-y-1/2 text-white bg-black/50 hover:bg-black/70 rounded-full w-16 h-16 flex items-center justify-center z-40 transition-all duration-300 shadow-2xl hover:scale-110">
        <i class="fas fa-chevron-left text-2xl"></i>
    </button>
    <button id="next-btn" class="absolute right-6 top-1/2 transform -translate-y-1/2 text-white bg-black/50 hover:bg-black/70 rounded-full w-16 h-16 flex items-center justify-center z-40 transition-all duration-300 shadow-2xl hover:scale-110">
        <i class="fas fa-chevron-right text-2xl"></i>
    </button>

    <!-- Image counter -->
    <div class="absolute top-6 left-6 bg-black/50 backdrop-blur-md rounded-full px-6 py-3 text-white font-medium z-40 shadow-2xl">
        <span id="current-image">1</span> / <span id="total-images">1</span>
    </div>

    <!-- Main image container -->
    <div class="w-full h-full flex items-center justify-center p-6">
        <div class="relative max-w-6xl max-h-full">
            <img id="modal-image" src="" alt="" class="max-w-full max-h-full object-contain rounded-2xl shadow-2xl">

            <!-- Image info overlay -->
            <div class="absolute -bottom-20 left-0 right-0 bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-2xl">
                <div class="text-center">
                    <h3 id="modal-title" class="text-2xl font-bold text-white mb-3"></h3>
                    <p id="modal-description" class="text-gray-200 mb-3 leading-relaxed"></p>
                    <div class="flex items-center justify-center text-gray-300">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        <span id="modal-date"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading indicator -->
    <div id="loading-indicator" class="absolute inset-0 flex items-center justify-center bg-black/50 hidden">
        <div class="animate-spin rounded-full h-16 w-16 border-4 border-white/20 border-t-white"></div>
    </div>
</div>

<script src="beranda/gallery.js"></script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.columns-1 { columns: 1; }
.columns-2 { columns: 2; }
.columns-3 { columns: 3; }
.columns-4 { columns: 4; }

@media (min-width: 768px) {
    .columns-1 { columns: 2; }
}

@media (min-width: 1024px) {
    .columns-1 { columns: 3; }
}

@media (min-width: 1280px) {
    .columns-1 { columns: 4; }
}
</style>
