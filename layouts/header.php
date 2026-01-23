<?php if (!session_id()) session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Lurahgo.id - Dashboard RT/RW</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
body { background-color: #fef3c7; }
</style>
</head>
<body class="bg-yellow-50 min-h-screen flex flex-col">
<header class="bg-yellow-200 text-white py-4 px-6 shadow-md">
    <div class="flex justify-between items-center">
        <div class="flex items-center">
            <h1 class="text-xl font-bold">Lurahgo.id</h1>
            <span class="ml-2 text-sm">Dashboard RT/RW</span>
        </div>
        <div class="flex items-center">
            <span class="mr-4">Welcome, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'User'; ?> (<?php echo isset($_SESSION['role']) ? $_SESSION['role'] : 'Role'; ?>)</span>
            <a href="../auth/logout.php" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Logout</a>
        </div>
    </div>
</header>
