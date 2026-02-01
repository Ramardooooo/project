<?php if (!session_id()) session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Lurahgo.id - Dashboard RT/RW</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body { background-color: #dbeafe; }
</style>
</head>
<body class="bg-blue-50 min-h-screen flex flex-col">
<header class="bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg border-b border-blue-500 relative">
    <div class="flex justify-between items-center px-8 py-5">
    
        <div class="flex items-center">
            <button class="text-blue-200 hover:text-white mr-6 lg:hidden transition-colors duration-200">
                <i class="fas fa-bars text-2xl"></i>
            </button>
            <h1 class="text-2xl font-bold text-white tracking-wide">Dashboard Admin</h1>
        </div>

    
        <div class="flex items-center space-x-6">
           
            <div class="flex items-center space-x-3 bg-blue-800 bg-opacity-50 rounded-full px-4 py-2 backdrop-blur-sm border border-blue-700">
                <div class="w-10 h-10 rounded-full border-2 border-blue-600 bg-blue-700 flex items-center justify-center text-white font-bold text-lg">
                    <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
                </div>
                <span class="text-sm font-semibold text-white">
                    <?= $_SESSION['username'] ?? 'User'; ?>
                </span>
            </div>

        <!-- LOGOUT -->
            <a href="../auth/logout.php" class="text-blue-200 hover:text-white transition-colors duration-200 p-2 rounded-full hover:bg-blue-700 hover:bg-opacity-50">
                <i class="fas fa-sign-out-alt text-xl"></i>
            </a>
        </div>
    </div>
</header>
