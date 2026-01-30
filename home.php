<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}
include 'config/database.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lurahgo Digital</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-red-600 text-white">

<!-- NAVBAR -->
<nav class="bg-white text-gray-800">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="text-xl font-bold text-red-600">
            Lurahgo<span class="text-black">.id</span>
        </div>
        <ul class="hidden md:flex space-x-6">
            <li><a href="#" class="hover:text-red-600">Home</a></li>
            <li><a href="#" class="hover:text-red-600">Profil</a></li>
            <li><a href="#" class="hover:text-red-600">Layanan</a></li>
            <li><a href="#" class="hover:text-red-600">Blog</a></li>
            <li><a href="#" class="hover:text-red-600">Kontak</a></li>
        </ul>
        <a href="#" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">
            Buat Website
        </a>
    </div>
</nav>

<!-- HERO SECTION -->
<section class="relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 py-20 grid md:grid-cols-2 gap-10 items-center">

        <!-- TEXT -->
        <div>
            <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-6">
                Inilah Cara Membuat Website RT/RW<br>
                <span class="text-yellow-300">Paling Mudah!</span>
            </h1>

            <p class="text-lg mb-8">
                Tidak perlu repot lagi mencari tutorial cara membuat website RT/RW,
                atau mencari jasa pembuatan website RT/RW.
                Kurang dari 1 menit, website RT/RW Anda langsung jadi.
            </p>

            <div class="flex space-x-4">
                <a href="#"
                   class="bg-green-500 text-white px-6 py-3 rounded font-semibold hover:bg-green-600">
                    Buat Sekarang
                </a>
                <a href="#"
                   class="bg-white text-red-600 px-6 py-3 rounded font-semibold hover:bg-gray-100">
                    Lihat Demo
                </a>
            </div>
        </div>

        <!-- IMAGE -->
        <div class="flex justify-center">
            <img src="assets/img/pak-desa.png"
                 alt="Website RT/RW"
                 class="max-w-sm drop-shadow-xl">
        </div>
    </div>

    <!-- BACKGROUND SHAPE -->
    <div class="absolute inset-0 bg-gradient-to-r from-red-700 to-red-500 opacity-30 -z-10"></div>
</section>
<!-- ===== LAYANAN DESAGO ===== -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">

        <h2 class="text-4xl font-serif text-center mb-16">
            Layanan <span class="text-red-600 font-bold">Lurahgo.Id</span>
        </h2>

        <div class="grid md:grid-cols-3 gap-10">

            <!-- CARD 1 -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <img src="assets/img/layanan-1.png" class="mx-auto mb-6" alt="">
                <h3 class="text-2xl font-serif mb-4">Website RT/RW</h3>
                <p class="text-gray-600 leading-relaxed">
                    Website RT/RW adalah sebuah sistem informasi (foto & video)
                    dan komunikasi RT/RW di era digital internet dengan layanan
                    penyajian data kependudukan, berita RT/RW, sosial media,
                    transparasi APB RT/RW.
                </p>
                <div class="mt-6">
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[#d8b08c] text-white text-xl">
                        →
                    </span>
                </div>
            </div>

            <!-- CARD 2 -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <img src="assets/img/layanan-2.png" class="mx-auto mb-6" alt="">
                <h3 class="text-2xl font-serif mb-4">E-Learning</h3>
                <p class="text-gray-600 leading-relaxed">
                    E-learning RT/RW adalah fitur aplikasi Lurahgo untuk meningkatkan
                    mutu pendidikan dan literasi masyarakat RT/RW berupa materi
                    pembelajaran, pustaka online, wifi corner, pelatihan online.
                </p>
                <div class="mt-6">
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[#d8b08c] text-white text-xl">
                        →
                    </span>
                </div>
            </div>

            <!-- CARD 3 -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <img src="assets/img/layanan-3.png" class="mx-auto mb-6" alt="">
                <h3 class="text-2xl font-serif mb-4">Loker RT/RW</h3>
                <p class="text-gray-600 leading-relaxed">
                    Kami menyediakan produk untuk membantu masyarakat RT/RW
                    dalam penyediaan jasa dan lowongan pekerjaan seperti
                    pijat, ojol, tukang, dan bengkel online.
                </p>
                <div class="mt-6">
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[#d8b08c] text-white text-xl">
                        →
                    </span>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ===== MEMPERKENALKAN ===== -->
<section class="py-20 bg-gray-100 relative">
    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-12 items-center">

        <!-- LOGO -->
        <div class="flex justify-center md:justify-start">
            <img src="assets/img/logo-desago.png" alt="Lurahgo Digital" class="max-w-xs">
        </div>

        <!-- TEXT -->
        <div>
            <h2 class="text-4xl font-serif mb-6">Memperkenalkan</h2>
            <p class="text-gray-700 mb-4 leading-relaxed">
                Kami adalah perusahaan pelayanan digital RT/RW yang berlokasi di
                Temanggung, Jawa Tengah dan berada di bawah bendera
                PT. Bumi Tekno Indonesia. Lurahgo berupaya untuk membantu
                pemerintah RT/RW dalam mengembangkan konsep RT/RW digital.
            </p>
            <p class="text-gray-700 mb-4 leading-relaxed">
                Layanan kami meliputi pembuatan website, aplikasi, lowongan kerja,
                penyediaan infrastruktur digital, e-course, dan toko digital.
                Fitur yang kami hadirkan membantu meningkatkan efisiensi
                pekerjaan dan pelayanan RT/RW.
            </p>
            <p class="text-gray-700 leading-relaxed">
                Kami telah melayani berbagai skala usaha, mulai dari UMKM
                hingga perusahaan multi nasional.
            </p>
        </div>
    </div>

    <!-- background map effect -->
    <div class="absolute inset-0 bg-[url('assets/img/map-dot.png')] opacity-10 pointer-events-none"></div>
</section>


</body>
</html>

