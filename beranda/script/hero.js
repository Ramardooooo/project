function showNotification(message, type = 'success') {
    const notification = document.getElementById('floating-notification');
    const icon = notification.querySelector('i');
    const title = notification.querySelector('h4');
    const text = notification.querySelector('p');

    if (type === 'success') {
        notification.className = notification.className.replace(/bg-\w+-500/, 'bg-green-500');
        icon.className = 'fas fa-check-circle mr-3 text-xl';
        title.textContent = 'Berhasil!';
    } else if (type === 'error') {
        notification.className = notification.className.replace(/bg-\w+-500/, 'bg-red-500');
        icon.className = 'fas fa-exclamation-circle mr-3 text-xl';
        title.textContent = 'Error!';
    } else if (type === 'info') {
        notification.className = notification.className.replace(/bg-\w+-500/, 'bg-blue-500');
        icon.className = 'fas fa-lightbulb mr-3 text-xl';
        title.textContent = 'Tips Edukasi';
    }

    text.textContent = message;

    notification.classList.remove('translate-x-full');
    notification.classList.add('translate-x-0');

    setTimeout(() => {
        closeNotification();
    }, 5000);
}

function closeNotification() {
    const notification = document.getElementById('floating-notification');
    notification.classList.remove('translate-x-0');
    notification.classList.add('translate-x-full');
}

setInterval(() => {
    const userNotifications = [
        '✨ Update baru: Fitur laporan bulanan kini tersedia di dashboard Anda',
        '📢 Pengumuman: Rapat RT minggu depan pukul 19:00 WIB',
        '🎯 Tips: Gunakan fitur pencarian untuk menemukan data warga dengan cepat',
        '🔒 Keamanan: Data Anda selalu terlindungi dengan enkripsi end-to-end',
        '📊 Info: 15 warga baru terdaftar bulan ini di sistem RT Anda',
        '💡 Fitur baru: Notifikasi real-time untuk update kegiatan RT/RW',
        '📱 Mobile: Akses sistem kapan saja melalui aplikasi mobile',
        '🎉 Selamat: Sistem Anda telah aktif selama 6 bulan!'
    ];
    const randomNotification = userNotifications[Math.floor(Math.random() * userNotifications.length)];
    showNotification(randomNotification, 'info');
}, 25000);

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
