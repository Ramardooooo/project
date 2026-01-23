<?php include 'layouts/header.php'; ?>

<div class="min-h-screen bg-yellow-50">
    <!-- Header Section -->
    <header class="bg-yellow-600 text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">Selamat Datang di Lurago.id</h1>
            <p class="text-xl mb-8">Sistem Informasi RT/RW Terpercaya</p>
            <a href="auth/login.php" class="bg-white text-yellow-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">Masuk Dashboard</a>
        </div>
    </header>

    <!-- Description Section -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Tentang Lurago.id</h2>
                <p class="text-lg text-gray-600 mb-8">
                    Lurago.id adalah platform digital untuk mengelola data warga, kepala keluarga, RT, dan RW
                    dengan mudah dan efisien. Sistem ini dirancang untuk memudahkan administrasi
                    dan komunikasi antara warga dan pengurus RT/RW.
                </p>
                <div class="grid md:grid-cols-3 gap-8 mt-12">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold text-yellow-600 mb-4">Data Warga</h3>
                        <p class="text-gray-600">Kelola data warga dengan lengkap dan akurat</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold text-yellow-600 mb-4">Role Management</h3>
                        <p class="text-gray-600">Sistem role admin, ketua, dan user</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold text-yellow-600 mb-4">Dashboard</h3>
                        <p class="text-gray-600">Pantau statistik RT/RW secara real-time</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Image Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <img src="rtrw"
                     alt="rtrw"
                     class="w-full h-64 md:h-96 object-cover rounded-lg shadow-lg">
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-yellow-600 text-white py-8">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; 2024 Lurago.id - Sistem Informasi RT/RW</p>
        </div>
    </footer>
</div>