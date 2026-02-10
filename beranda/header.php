<?php if (!session_id()) session_start(); ?>
<?php include __DIR__ . '/../config/database.php'; ?>
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
<header class="bg-white shadow-md border-b border-gray-200 sticky top-0 z-50">


    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <a href="/" class="flex items-center space-x-4 hover:opacity-80 transition-opacity duration-300 cursor-pointer">
            <div>
                <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Lurahgo.id</h1>
                <p class="text-xs text-gray-500 hidden sm:block">Website Digital RT/RW</p>
            </div>
        </a>

        <nav class="hidden lg:flex space-x-8">
            <a href="#hero" class="relative text-gray-700 hover:text-blue-600 transition-all duration-300 font-medium group">
                <span class="relative z-10">Home</span>
                <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-500 to-purple-600 group-hover:w-full transition-all duration-300"></div>
            </a>
            <a href="#about" class="relative text-gray-700 hover:text-blue-600 transition-all duration-300 font-medium group">
                <span class="relative z-10">About</span>
                <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-500 to-purple-600 group-hover:w-full transition-all duration-300"></div>
            </a>
            <a href="#services" class="relative text-gray-700 hover:text-blue-600 transition-all duration-300 font-medium group">
                <span class="relative z-10">Services</span>
                <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-500 to-purple-600 group-hover:w-full transition-all duration-300"></div>
            </a>
            <a href="#announcements" class="relative text-gray-700 hover:text-blue-600 transition-all duration-300 font-medium group">
                <span class="relative z-10">Announcements</span>
                <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-500 to-purple-600 group-hover:w-full transition-all duration-300"></div>
            </a>
            <a href="#gallery" class="relative text-gray-700 hover:text-blue-600 transition-all duration-300 font-medium group">
                <span class="relative z-10">Gallery</span>
                <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-500 to-purple-600 group-hover:w-full transition-all duration-300"></div>
            </a>
            <a href="#contact" class="relative text-gray-700 hover:text-blue-600 transition-all duration-300 font-medium group">
                <span class="relative z-10">Contact</span>
                <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-500 to-purple-600 group-hover:w-full transition-all duration-300"></div>
            </a>
        </nav>

        <div class="flex items-center space-x-4">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="relative">
                    <button id="user-menu-btn" class="flex items-center space-x-2 text-gray-700 hover:text-blue-600 transition-colors duration-300">
                        <?php if ($user && $user['profile_photo']): ?>
                            <img src="../<?php echo $user['profile_photo']; ?>" alt="Avatar" class="w-8 h-8 rounded-full border-2 border-blue-200">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/32x32/3B82F6/FFFFFF?text=<?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>" alt="Avatar" class="w-8 h-8 rounded-full border-2 border-blue-200">
                        <?php endif; ?>
                        <span class="hidden md:block font-medium"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div id="user-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 hidden z-50">
                        <div class="p-3 border-b border-gray-200">
                            <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($_SESSION['role'] ?? 'User'); ?></p>
                        </div>
                        <a href="dashboard_<?php echo $_SESSION['role']; ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <a href="settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                            <i class="fas fa-cog mr-2"></i>Settings
                        </a>
                        <div class="border-t border-gray-200"></div>
                        <a href="logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login" class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-2 rounded-lg font-semibold hover:from-blue-600 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fas fa-sign-in-alt mr-2"></i>Dashboard
                </a>
            <?php endif; ?>
        </div>
        
        <div class="lg:hidden">
            <button id="mobile-menu-button" class="text-gray-700 focus:outline-none p-2 hover:bg-gray-100 rounded-lg transition-colors duration-300">
                <i class="fas fa-bars text-lg"></i>
            </button>
        </div>
    </div>

    <div id="mobile-menu" class="lg:hidden hidden bg-white border-t border-gray-200 shadow-lg">
        <div class="px-4 py-3 space-y-1">
            <a href="#hero" class="block py-3 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg px-3 transition-all duration-300">
                <i class="fas fa-home mr-3"></i>Home
            </a>
            <a href="#about" class="block py-3 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg px-3 transition-all duration-300">
                <i class="fas fa-info-circle mr-3"></i>About
            </a>
            <a href="#services" class="block py-3 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg px-3 transition-all duration-300">
                <i class="fas fa-cogs mr-3"></i>Services
            </a>
            <a href="#announcements" class="block py-3 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg px-3 transition-all duration-300">
                <i class="fas fa-bullhorn mr-3"></i>Announcements
            </a>
            <a href="#gallery" class="block py-3 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg px-3 transition-all duration-300">
                <i class="fas fa-images mr-3"></i>Gallery
            </a>
            <a href="#contact" class="block py-3 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg px-3 transition-all duration-300">
                <i class="fas fa-envelope mr-3"></i>Contact
            </a>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="auth/login.php" class="block py-3 text-blue-600 hover:bg-blue-50 rounded-lg px-3 transition-all duration-300 font-semibold">
                    <i class="fas fa-sign-in-alt mr-3"></i>Dashboard
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<script>
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });

    const userMenuBtn = document.getElementById('user-menu-btn');
    const userDropdown = document.getElementById('user-dropdown');
    if (userMenuBtn && userDropdown) {
        userMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', function(e) {
            if (!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
        });
    }
</script>
