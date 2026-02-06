<section class="bg-blue-600 text-white py-4 overflow-hidden">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center">
            <i class="fas fa-bullhorn text-yellow-300 mr-3"></i>
            <span class="font-semibold mr-4">Berita Terkini:</span>
            <div class="flex-1 overflow-hidden">
                <div class="ticker-container">
                    <div class="ticker-content animate-marquee">
                        <?php
                        // Simulasi data berita real-time (dalam implementasi nyata, fetch dari database)
                        $news_items = [
                            '• Rapat RT 01 akan dilaksanakan pada tanggal 15 Juni 2024',
                            '• Program vaksinasi COVID-19 masih berlangsung di Posyandu',
                            '• Pengumpulan sampah plastik untuk daur ulang dimulai minggu depan',
                            '• Jambore anak-anak RT se-Kelurahan akan diadakan bulan Juli',
                            '• Pendaftaran KTP elektronik dapat dilakukan di Kantor Kelurahan'
                        ];
                        echo implode(' <span class="mx-4">|</span> ', $news_items);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
@keyframes marquee {
    0% { transform: translateX(100%); }
    100% { transform: translateX(-100%); }
}

.animate-marquee {
    animation: marquee 30s linear infinite;
}
</style>
