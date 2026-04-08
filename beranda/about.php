<section id="about" class="animate-on-scroll animate-fade-up py-20 bg-gray-50">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Tentang Lurahgo.Id</h2>
            <p class="text-gray-600 text-lg">Platform digital untuk mengelola RT/RW</p>
        </div>

        <div class="grid md:grid-cols-2 gap-12 items-center mb-16">
            <div>
                <h3 class="text-2xl font-semibold mb-6">Fitur Utama</h3>
                <ul class="space-y-3 text-gray-600">
                    <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i>Digitalisasi administrasi RT/RW</li>
                    <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i>Manajemen data warga lengkap</li>
                    <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i>Sistem informasi terintegrasi</li>
                    <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i>Keamanan data tinggi</li>
                    <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i>Dashboard analytics real-time</li>
                </ul>
                <a href="<?php echo $_SESSION['role'] === 'admin' ? 'dashboard_admin' : ($_SESSION['role'] === 'ketua' ? 'dashboard_ketua' : 'dashboard_user'); ?>" class="inline-block mt-6 bg-gradient-to-r from-purple-600 to-purple-700 text-white px-8 py-3 rounded-lg hover:from-purple-700 hover:to-purple-800 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">Masuk</a>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
                <h4 class="text-xl font-bold mb-6 text-gray-800 flex items-center">
                    <i class="text-blue-500 mr-3"></i>Dashboard Analytics Real-time
                </h4>
                <?php 
                include 'config/database.php';
                $total_warga = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM warga"))['total'];
                $rt_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM rt WHERE status = 'aktif'"))['total'];
                $pengguna_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM users WHERE DATE(created_at) >= DATE_SUB(NOW(), INTERVAL 30 DAY)"))['total'];
                ?>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                        <span class="text-gray-700 font-medium">Total Warga</span>
                        <span class="font-bold text-2xl text-blue-600"><?php echo number_format($total_warga); ?></span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <span class="text-gray-700 font-medium">RT Aktif</span>
                        <span class="font-bold text-2xl text-green-600"><?php echo number_format($rt_aktif); ?></span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                        <span class="text-gray-700 font-medium">Pengguna Aktif (30 hari)</span>
                        <span class="font-bold text-2xl text-purple-600"><?php echo number_format($pengguna_aktif); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-4 gap-8 text-center">
            <?php
            $warga = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as num FROM warga"))['num'] ?? 0;
            $rtrw = (mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as rt FROM rt"))['rt'] ?? 0) + (mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as rw FROM rw"))['rw'] ?? 0);
            $users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as num FROM users"))['num'] ?? 0;
            $kegiatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as num FROM activities"))['num'] ?? 0;
            ?>
            <div class="group cursor-pointer p-6 bg-white rounded-2xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 border border-gray-100 group-hover:border-blue-200">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-all">
                    <i class="fas fa-users text-2xl text-white"></i>
                </div>
                <h3 class="text-3xl font-bold text-gray-800 mb-2"><?php echo number_format($warga); ?>+</h3>
                <p class="text-gray-600 font-semibold text-lg group-hover:text-blue-600 transition-colors">Warga</p>
            </div>
            <div class="group cursor-pointer p-6 bg-white rounded-2xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 border border-gray-100 group-hover:border-green-200">
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-all">
                    <i class="fas fa-map-marker-alt text-2xl text-white"></i>
                </div>
                <h3 class="text-3xl font-bold text-gray-800 mb-2"><?php echo number_format($rtrw); ?></h3>
                <p class="text-gray-600 font-semibold text-lg group-hover:text-green-600 transition-colors">RT/RW</p>
            </div>
            <div class="group cursor-pointer p-6 bg-white rounded-2xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 border border-gray-100 group-hover:border-purple-200">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-all">
                    <i class="fas fa-user-check text-2xl text-white"></i>
                </div>
                <h3 class="text-3xl font-bold text-gray-800 mb-2"><?php echo number_format($users); ?>+</h3>
                <p class="text-gray-600 font-semibold text-lg group-hover:text-purple-600 transition-colors">Pengguna</p>
            </div>
            <div class="group cursor-pointer p-6 bg-white rounded-2xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 border border-gray-100 group-hover:border-red-200">
                <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-all">
                    <i class="fas fa-calendar-alt text-2xl text-white"></i>
                </div>
                <h3 class="text-3xl font-bold text-gray-800 mb-2"><?php echo number_format($kegiatan); ?>+</h3>
                <p class="text-gray-600 font-semibold text-lg group-hover:text-red-600 transition-colors">Kegiatan</p>
            </div>
        </div>
    </div>
</section>
