<section class="bg-gray-100 py-4">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex justify-center items-center space-x-6">
            <div class="text-center">
                <i class="fas fa-clock text-2xl text-blue-600 mb-2"></i>
                <div id="live-clock" class="text-2xl font-bold text-gray-800"></div>
                <div class="text-sm text-gray-600" id="live-date"></div>
            </div>
            <div class="text-center">
                <i class="fas fa-calendar-alt text-2xl text-green-600 mb-2"></i>
                <div class="text-lg font-semibold text-gray-800">Jadwal Hari Ini</div>
                <div class="text-sm text-gray-600">Rapat RT pukul 19:00</div>
            </div>
            <div class="text-center">
                <i class="fas fa-thermometer-half text-2xl text-red-600 mb-2"></i>
                <div class="text-lg font-semibold text-gray-800">Cuaca</div>
                <div class="text-sm text-gray-600">Cerah, 28°C</div>
            </div>
        </div>
    </div>
</section>

<script>
function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID', {
        hour12: false,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    const dateString = now.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    document.getElementById('live-clock').textContent = timeString;
    document.getElementById('live-date').textContent = dateString;
}

updateClock();
setInterval(updateClock, 1000);
</script>
