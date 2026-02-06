<section class="relative py-20 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full animate-pulse"></div>
        <div class="absolute top-32 right-20 w-16 h-16 bg-blue-300/20 rounded-full animate-bounce" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-20 left-1/4 w-12 h-12 bg-purple-300/20 rounded-full animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 right-10 w-8 h-8 bg-indigo-300/20 rounded-full animate-bounce" style="animation-delay: 0.5s;"></div>
        <div class="absolute bottom-32 right-1/3 w-24 h-24 bg-white/5 rounded-full animate-pulse" style="animation-delay: 3s;"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-6 text-center">
        <div class="mb-8">
            <span class="inline-block px-4 py-2 bg-white/10 backdrop-blur-md rounded-full text-white text-sm font-medium mb-6 border border-white/20">
                <i class="fas fa-crown mr-2 text-yellow-400"></i>Platform Terdepan untuk RT/RW Indonesia
            </span>
        </div>

        <h1 class="text-6xl md:text-7xl font-bold text-white mb-6 leading-tight">
            Kelola RT/RW dengan
            <span class="bg-gradient-to-r from-yellow-400 via-orange-500 to-red-500 bg-clip-text text-transparent animate-pulse">
                Teknologi Modern
            </span>
        </h1>

        <p class="text-xl text-blue-100 mb-12 max-w-3xl mx-auto leading-relaxed">
            Platform digital terintegrasi untuk mengelola data warga, komunikasi transparan,
            dan pelaporan real-time. Bergabunglah dengan ribuan RT/RW di seluruh Indonesia.
        </p>

        <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-16">
            <a href="#services" class="group px-8 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold rounded-full hover:from-blue-600 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-2xl transform hover:scale-105">
                <i class="fas fa-rocket mr-2 group-hover:animate-bounce"></i>Mulai Sekarang
            </a>
            <a href="#about" class="group px-8 py-4 bg-white/10 backdrop-blur-md text-white font-semibold rounded-full border border-white/20 hover:bg-white/20 transition-all duration-300">
                <i class="fas fa-play-circle mr-2 group-hover:animate-pulse"></i>Lihat Demo
            </a>
        </div>

        <!-- Trust Indicators -->
        <div class="grid md:grid-cols-3 gap-8 max-w-4xl mx-auto">
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all duration-300">
                <div class="text-3xl text-green-400 mb-3"><i class="fas fa-shield-alt"></i></div>
                <h3 class="text-white font-semibold mb-2">Data Aman 100%</h3>
                <p class="text-blue-100 text-sm">Enkripsi end-to-end & backup otomatis</p>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all duration-300">
                <div class="text-3xl text-blue-400 mb-3"><i class="fas fa-clock"></i></div>
                <h3 class="text-white font-semibold mb-2">Update Real-time</h3>
                <p class="text-blue-100 text-sm">Informasi terbaru dalam hitungan detik</p>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all duration-300">
                <div class="text-3xl text-purple-400 mb-3"><i class="fas fa-users"></i></div>
                <h3 class="text-white font-semibold mb-2">Komunitas Besar</h3>
                <p class="text-blue-100 text-sm">500+ RT/RW aktif menggunakan platform</p>
            </div>
        </div>
    </div>

    <!-- Floating Notification -->
    <div id="floating-notification" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg transform translate-x-full transition-transform duration-500 z-50 max-w-sm">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-3 text-xl"></i>
            <div>
                <h4 class="font-semibold">Berhasil!</h4>
                <p class="text-sm opacity-90">Data RT berhasil diperbarui</p>
            </div>
            <button onclick="closeNotification()" class="ml-4 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <div class="w-6 h-10 border-2 border-white/30 rounded-full flex justify-center">
            <div class="w-1 h-3 bg-white/50 rounded-full mt-2 animate-pulse"></div>
        </div>
    </div>
</section>

<script>
// Floating notification system
function showNotification(message, type = 'success') {
    const notification = document.getElementById('floating-notification');
    const icon = notification.querySelector('i');
    const title = notification.querySelector('h4');
    const text = notification.querySelector('p');

    // Update content based on type
    if (type === 'success') {
        notification.className = notification.className.replace('bg-green-500', 'bg-green-500');
        icon.className = 'fas fa-check-circle mr-3 text-xl';
        title.textContent = 'Berhasil!';
    } else if (type === 'error') {
        notification.className = notification.className.replace('bg-green-500', 'bg-red-500');
        icon.className = 'fas fa-exclamation-circle mr-3 text-xl';
        title.textContent = 'Error!';
    }

    text.textContent = message;

    // Show notification
    notification.classList.remove('translate-x-full');
    notification.classList.add('translate-x-0');

    // Auto hide after 5 seconds
    setTimeout(() => {
        closeNotification();
    }, 5000);
}

function closeNotification() {
    const notification = document.getElementById('floating-notification');
    notification.classList.remove('translate-x-0');
    notification.classList.add('translate-x-full');
}

// Simulate random notifications (for demo)
setInterval(() => {
    const messages = [
        'Data RT berhasil diperbarui',
        'Pengumuman baru ditambahkan',
        'Laporan bulanan tersedia',
        'Status warga diperbarui'
    ];
    const randomMessage = messages[Math.floor(Math.random() * messages.length)];
    showNotification(randomMessage);
}, 15000); // Show every 15 seconds

// Add loading animation to buttons
document.querySelectorAll('a[href="#services"], a[href="#about"]').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...';
        setTimeout(() => {
            window.location.hash = this.getAttribute('href');
            this.innerHTML = this.getAttribute('href') === '#services' ?
                '<i class="fas fa-rocket mr-2"></i>Mulai Sekarang' :
                '<i class="fas fa-play-circle mr-2"></i>Lihat Demo';
        }, 1000);
    });
});
</script>
