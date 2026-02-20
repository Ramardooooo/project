<?php
include 'config/database.php';

// Create announcements table if not exists
$create_table = "CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $create_table);

// Fetch announcements from database
$query = "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 6";
$result = mysqli_query($conn, $query);
$announcements = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<section id="announcements" class="py-20 bg-gradient-to-br from-indigo-50 via-white to-cyan-50 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute top-10 left-20 w-40 h-40 bg-indigo-500 rounded-full"></div>
        <div class="absolute bottom-10 right-20 w-32 h-32 bg-cyan-500 rounded-full"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-6">

        <div class="text-center mb-16">
            <span class="inline-block px-4 py-2 bg-gradient-to-r from-indigo-100 to-cyan-100 text-indigo-800 rounded-full text-sm font-medium mb-4">
                <i class="fas fa-bell mr-2 text-indigo-600"></i>Update Terbaru
            </span>
            <h2 class="text-5xl font-bold mb-6 bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-600 bg-clip-text text-transparent">
                Pengumuman & Berita
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Informasi terbaru tentang sistem dan kegiatan RT/RW
            </p>
        </div>

        <?php if (empty($announcements)) : ?>
            <div class="text-center py-12">
                <i class="fas fa-bullhorn text-gray-400 text-5xl mb-4"></i>
                <p class="text-gray-500 text-lg">Belum ada pengumuman</p>
            </div>
        <?php else : ?>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($announcements as $index => $announcement) : ?>
                    <?php 
                    $colors = ['blue', 'green', 'purple', 'indigo', 'cyan', 'pink'];
                    $color = $colors[$index % count($colors)];
                    $icons = ['fa-code', 'fa-graduation-cap', 'fa-chart-bar', 'fa-bell', 'fa-newspaper', 'fa-calendar'];
                    $icon = $icons[$index % count($icons)];
                    ?>
                    <div class="group bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-2xl p-8 transform hover:-translate-y-1 transition-all duration-300 border border-white/50">
                        <div class="flex items-center mb-6">
                            <div class="w-14 h-14 bg-gradient-to-br from-<?php echo $color; ?>-500 to-<?php echo $color; ?>-600 rounded-2xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                                <i class="fas <?php echo $icon; ?> text-2xl text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800 mb-1"><?php echo htmlspecialchars($announcement['title']); ?></h3>
                                <p class="text-sm text-gray-500 flex items-center">
                                    <i class="fas fa-calendar-alt mr-1"></i><?php echo date('d M Y', strtotime($announcement['created_at'])); ?>
                                </p>
                            </div>
                        </div>
                        <p class="text-gray-600 leading-relaxed">
                            <?php echo htmlspecialchars($announcement['content']); ?>
                        </p>
                        <div class="mt-4 flex items-center text-<?php echo $color; ?>-600 font-medium">
                            <span class="text-sm">Baca selengkapnya</span>
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform duration-300"></i>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
