<?php if (!session_id()) session_start(); ?>
<?php
$user_id = $_SESSION['user_id'] ?? null;
$user = null;
if ($user_id) {
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Lurahgo.id - Dashboard RT/RW</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body { background-color: #1e3a8a; }
</style>
</head>
<body class="bg-blue-900 min-h-screen flex flex-col">
<header class="bg-gradient-to-r from-slate-900/70 to-slate-800/70 backdrop-blur-md shadow-lg border-b border-slate-600/30 relative ml-64">
    <div class="flex justify-between items-center px-8 py-5">

        <div class="flex items-center">
            <button class="text-white/80 hover:text-white mr-6 lg:hidden transition-colors duration-200">
                <i class="fas fa-bars text-2xl"></i>
            </button>
            <h1 class="text-2xl font-bold text-white tracking-wide"></h1>
        </div>


        <div class="flex items-center space-x-6">

            <div class="flex items-center space-x-3 bg-white/10 rounded-full px-4 py-2 backdrop-blur-md border border-white/20">
                <?php if ($user && $user['profile_photo']): ?>
                    <img src="../../<?php echo $user['profile_photo']; ?>" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-white/30">
                <?php else: ?>
                    <div class="w-10 h-10 rounded-full border-2 border-white/30 bg-white/20 flex items-center justify-center text-white font-bold text-lg">
                        <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
                    </div>
                <?php endif; ?>
                <span class="text-sm font-semibold text-white">
                    <?= $_SESSION['username'] ?? 'User'; ?>
                </span>
            </div>

            <a href="logout" class="text-white/80 hover:text-white transition-colors duration-200 p-2 rounded-full hover:bg-white/20">
                <i class="fas fa-sign-out-alt text-xl"></i>
            </a>
        </div>
    </div>
</header>
