<header class="bg-white shadow-md">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <div class="flex items-center">
            <h1 class="text-2xl font-bold text-blue-600">Lurahgo.id</h1>
        </div>

        <nav class="hidden md:flex space-x-6">
            <a href="#hero" class="text-gray-700 hover:text-blue-600 transition duration-300">Home</a>
            <a href="#about" class="text-gray-700 hover:text-blue-600 transition duration-300">About</a>
            <a href="#services" class="text-gray-700 hover:text-blue-600 transition duration-300">Services</a>
            <a href="#announcements" class="text-gray-700 hover:text-blue-600 transition duration-300">Announcements</a>
            <a href="#gallery" class="text-gray-700 hover:text-blue-600 transition duration-300">Gallery</a>
            <a href="#faq" class="text-gray-700 hover:text-blue-600 transition duration-300">FAQ</a>
            <a href="#contact" class="text-gray-700 hover:text-blue-600 transition duration-300">Contact</a>
        </nav>

        <div class="flex items-center space-x-4">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="auth/logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition duration-300">Logout</a>
            <?php else: ?>
                <a href="auth/login.php" class="block py-2 text-blue-500">Dashboard</a>
            <?php endif; ?>
        </div>

        <div class="md:hidden">
            <button id="mobile-menu-button" class="text-gray-700 focus:outline-none">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>

    <div id="mobile-menu" class="md:hidden hidden bg-gray-100 px-4 py-2">
        <a href="#hero" class="block py-2 text-gray-700 hover:text-blue-600">Home</a>
        <a href="#about" class="block py-2 text-gray-700 hover:text-blue-600">About</a>
        <a href="#services" class="block py-2 text-gray-700 hover:text-blue-600">Services</a>
        <a href="#announcements" class="block py-2 text-gray-700 hover:text-blue-600">Announcements</a>
        <a href="#gallery" class="block py-2 text-gray-700 hover:text-blue-600">Gallery</a>
        <a href="#faq" class="block py-2 text-gray-700 hover:text-blue-600">FAQ</a>
        <a href="#contact" class="block py-2 text-gray-700 hover:text-blue-600">Contact</a>
            <a href="auth/login.php" class="block py-2 text-blue-500">Dashboard</a>
    </div>
</header>

<script>
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>
