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
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
<header class="bg-white shadow-sm border-b border-gray-200 relative ml-64">
    <div class="flex justify-between items-center px-8 py-5">

        <div class="flex items-center">
            <button class="text-gray-600 hover:text-gray-900 mr-6 lg:hidden transition-colors duration-200">
                <i class="fas fa-bars text-2xl"></i>
            </button>
            <h1 class="text-2xl font-bold text-gray-800 tracking-wide"></h1>
        </div>


        <div class="flex items-center space-x-6">

            <div class="flex items-center space-x-3 bg-gray-50 rounded-full px-4 py-2 border border-gray-200">
                <?php if ($user && $user['profile_photo']): ?>
                    <img src="../../<?php echo $user['profile_photo']; ?>" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-gray-300">
                <?php else: ?>
                    <div class="w-10 h-10 rounded-full border-2 border-gray-300 bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-lg">
                        <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
                    </div>
                <?php endif; ?>
                <span class="text-sm font-semibold text-gray-700">
                    <?= $_SESSION['username'] ?? 'User'; ?>
                </span>
            </div>

            <a href="logout" class="text-gray-500 hover:text-gray-700 transition-colors duration-200 p-2 rounded-full hover:bg-gray-100">
                <i class="fas fa-sign-out-alt text-xl"></i>
            </a>
        </div>
    </div>
</header>
