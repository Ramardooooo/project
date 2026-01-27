<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}
include 'config/database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lurahgo.id | Ramadhani Fadillah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body class="bg-amber-100 font-montserrat">
    <header class="fixed top-0 left-0 w-full py-6 px-12 bg-yellow-500 bg-opacity-80 backdrop-blur-md flex justify-between items-center z-10">
        <i class='bx bx-menu text-white text-2xl'></i>
        <a href="#" class="text-white text-xl font-semibold hover:text-blue-400 hover:scale-110 transition duration-300"><span>Lurahgo.id</span></a>
        <nav class="flex space-x-10">
            <a href="#" class="text-white text-lg font-medium border-b-2 border-transparent hover:text-orange-400 hover:border-white transition duration-300">Amat</a>
            <a href="#" class="text-white text-lg font-medium border-b-2 border-transparent hover:text-orange-400 hover:border-white transition duration-300">Amat</a>
            <a href="#" class="text-white text-lg font-medium border-b-2 border-transparent hover:text-orange-400 hover:border-white transition duration-300">Amat</a>
            <a href="#" class="text-white text-lg font-medium border-b-2 border-transparent hover:text-orange-400 hover:border-white transition duration-300">Amat</a>
            <a href="#" class="text-white text-lg font-medium border-b-2 border-transparent hover:text-orange-400 hover:border-white transition duration-300">Amat</a>
            <a href="#" class="text-white text-lg font-medium border-b-2 border-transparent hover:text-orange-400 hover:border-white transition duration-300">Amat</a>
        </nav>
    </header>
    <section class="min-h-screen flex items-center justify-center gap-16 px-8 pt-32 pb-8">
        <div class="text-center max-w-lg">
            <h1 class="text-5xl font-bold mb-4">Lurahgo.id</h1>
            <p class="text-xl">Lurahgo adalah Management RT-RW berbasis website
            </p>
        </div>
        <div class="flex-shrink-0">
            <img src="asset/download (1).jpg" alt="Profile" class="w-80 h-80 rounded-full object-cover">
        </div>
    </section>
    <footer class="w-full py-8 bg-black text-center">
        <p class="text-white text-sm">&copy; 2026, Lurahgo.id | All Rights Reserved</p>
    </footer>
</body>
</html>
