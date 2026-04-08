<?php
include 'config/database.php';

// Safe check and add views column using separate query
$check_views = mysqli_query($conn, "SHOW COLUMNS FROM announcements LIKE 'views'");
if (mysqli_num_rows($check_views) == 0) {
    mysqli_query($conn, "ALTER TABLE announcements ADD COLUMN views INT DEFAULT 0");
}

// Safe increment views (only if announcement exists)
$query = "SELECT id FROM announcements ORDER BY created_at DESC LIMIT 6";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];
    mysqli_query($conn, "UPDATE announcements SET views = COALESCE(views, 0) + 1 WHERE id = $id");
}

// Fetch announcements with views
$query = "SELECT *, COALESCE(views, 0) as views FROM announcements ORDER BY created_at DESC LIMIT 6";
$result = mysqli_query($conn, $query);
$announcements = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<section id="announcements" class="py-20 bg-gradient-to-br from-indigo-50 via-white to-cyan-50 relative overflow-hidden">
    <div class="absolute inset-0 opacity-5">
        <div class="absolute top-10 left-20 w-40 h-40 bg-indigo-500/20 rounded-full blur-xl animate-pulse"></div>
        <div class="absolute bottom-10 right-20 w-32 h-32 bg-cyan-500/20 rounded-full blur-xl animate-pulse" style="animation-delay: -1s;"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <span class="inline-block px-6 py-3 bg-gradient-to-r from-indigo-100 to-cyan-100 text-indigo-800 rounded-2xl text-sm font-semibold mb-6 shadow-lg">
                <i class="fas fa-bolt mr-2"></i>Update & Pengumuman Terbaru
            </span>
            <h2 class="text-5xl md:text-6xl font-black mb-6 bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-600 bg-clip-text text-transparent drop-shadow-2xl">
                Pengumuman & Berita
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                Informasi terkini tentang kegiatan RT/RW dan update sistem
            </p>
        </div>

        <?php if (empty($announcements)) : ?>
            <div class="text-center py-20">
                <div class="w-32 h-32 bg-gray-200 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-2xl">
                    <i class="fas fa-bullhorn text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-500 mb-4">Belum Ada Pengumuman</h3>
                <p class="text-gray-500 max-w-md mx-auto">Pantau terus untuk update terbaru.</p>
            </div>
        <?php else : ?>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($announcements as $index => $announcement) : 
                    $is_new = strtotime($announcement['created_at']) > strtotime('-7 days');
                    $preview = strlen($announcement['content']) > 150 ? substr($announcement['content'], 0, 150) . '...' : $announcement['content'];
                ?>
                    <article class="group bg-white/95 backdrop-blur-xl rounded-3xl shadow-xl hover:shadow-3xl p-8 transform hover:-translate-y-3 transition-all duration-500 border border-white/60 hover:border-indigo-200 overflow-hidden" data-aos="fade-up">
                        <?php if ($is_new): ?>
                            <span class="absolute top-4 right-4 bg-gradient-to-r from-emerald-400 to-green-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                                Baru
                            </span>
                        <?php endif; ?>
                        
                        <div class="flex items-start mb-6 relative z-10">
                            <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl flex items-center justify-center mr-5 group-hover:scale-110 transition-all duration-300 shadow-xl">
                                <i class="fas fa-bullhorn text-2xl text-white"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2 leading-tight group-hover:text-indigo-600 transition-colors line-clamp-2">
                                    <?php echo htmlspecialchars($announcement['title']); ?>
                                </h3>
                                <p class="text-sm text-gray-500 flex items-center mb-1">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    <?php echo date('d M Y H:i', strtotime($announcement['created_at'])); ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <p class="text-gray-700 leading-relaxed text-base" id="preview-<?php echo $announcement['id']; ?>">
                                <?php echo htmlspecialchars($preview); ?>
                            </p>
                            <?php if (strlen($announcement['content']) > 150): ?>
                                <button onclick="toggleContent(<?php echo $announcement['id']; ?>)">
                                    <span class="text-indigo-600 font-semibold hover:text-indigo-800 flex items-center text-sm transition-all duration-200">
                                        Baca selengkapnya <i class="fas fa-chevron-down ml-1 transform -rotate-0 group-hover:rotate-180"></i>
                                    </span>
                                </button>
                                <div id="full-<?php echo $announcement['id']; ?>" class="mt-4 hidden text-gray-700 leading-relaxed text-base max-h-32 overflow-y-auto pr-2">
                                    <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex items-center pt-6 border-t border-gray-200">
                            <div class="text-sm text-gray-500">
                                <i class="fas fa-eye mr-1"></i>
                                <span><?php echo number_format($announcement['views']); ?> views</span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
function toggleContent(id) {
    const full = document.getElementById('full-' + id);
    const preview = document.getElementById('preview-' + id);
    const btn = event.target.closest('button');
    const icon = btn.querySelector('i');
    
    if (full.classList.contains('hidden')) {
        full.classList.remove('hidden');
        preview.style.display = 'none';
        btn.querySelector('span').innerHTML = 'Tutup <i class="fas fa-chevron-up ml-1 transform rotate-180"></i>';
    } else {
        full.classList.add('hidden');
        preview.style.display = 'block';
        btn.querySelector('span').innerHTML = 'Baca selengkapnya <i class="fas fa-chevron-down ml-1 transform -rotate-0"></i>';
    }
}
</script>
