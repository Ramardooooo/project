<?php

include '../../config/database.php';
include '../../layouts/admin/header.php';
include '../../layouts/admin/sidebar.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: home");
    exit();
}

// Create announcements table if not exists
$create_table = "CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $create_table);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_announcement'])) {

        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $content = mysqli_real_escape_string($conn, $_POST['content']);

        $query = "INSERT INTO announcements (title, content) 
                  VALUES ('$title', '$content')";

        if (mysqli_query($conn, $query)) {
            $success = "Pengumuman berhasil ditambahkan!";
        } else {
            $error = "Error menambahkan pengumuman: " . mysqli_error($conn);
        }

    } elseif (isset($_POST['delete_announcement'])) {

        $id = (int) $_POST['id'];
        mysqli_query($conn, "DELETE FROM announcements WHERE id = $id");
        $success = "Pengumuman berhasil dihapus!";
    }
}

$search = isset($_GET['search']) 
    ? mysqli_real_escape_string($conn, $_GET['search']) 
    : '';

$where_clause = '';

if (!empty($search)) {
    $where_clause = "WHERE title LIKE '%$search%' 
                     OR content LIKE '%$search%'";
}

$query = "SELECT * FROM announcements 
          $where_clause 
          ORDER BY created_at DESC";

$result = mysqli_query($conn, $query);
$announcements = mysqli_fetch_all($result, MYSQLI_ASSOC);

?>

<div id="mainContent" class="ml-64 min-h-screen bg-gray-100">

    <div class="p-8">

        <h1 class="text-4xl font-extrabold mb-8 text-gray-800 drop-shadow-lg">
            Kelola Pengumuman
        </h1>

        <?php if (isset($success)) : ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)) : ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white/90 backdrop-blur-md rounded-2xl shadow-lg p-7 mb-8 border border-white/20 hover:shadow-2xl hover:bg-white/95 transition-all duration-300">

<h3 class="text-xl font-bold mb-6 text-black drop-shadow-lg flex items-center gap-2">
                Tambah Pengumuman
            </h3>

            <form method="POST" class="space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-heading text-gray-500"></i>
                        Judul Pengumuman
                    </label>

                    <input type="text" 
                           name="title" 
                           required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                           placeholder="Masukkan judul pengumuman">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-align-left text-gray-500"></i>
                        Isi Pengumuman
                    </label>

                    <textarea name="content" 
                              rows="5" 
                              required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                              placeholder="Masukkan isi pengumuman"></textarea>
                </div>

                <button type="submit"
                        name="add_announcement"
                        class="px-6 py-2 rounded-lg font-semibold text-white bg-gradient-to-r from-green-400 to-emerald-600 hover:scale-105 transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    Tambah Pengumuman
                </button>

            </form>
        </div>

        <div class="bg-white/90 backdrop-blur-md rounded-2xl shadow-lg p-7 border border-white/20 hover:shadow-2xl hover:bg-white/95 transition-all duration-300">

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-black drop-shadow-lg flex items-center gap-2">
                    <i class="fas fa-bullhorn text-blue-600"></i>
                    Daftar Pengumuman
                </h3>

                <form method="GET" class="flex gap-2">
                    <input type="text"
                           name="search"
                           value="<?php echo htmlspecialchars($search); ?>"
                           placeholder="Cari pengumuman..."
                           class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <?php if (empty($announcements)) : ?>
                <div class="text-center py-8">
                    <i class="fas fa-bullhorn text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">Belum ada pengumuman</p>
                </div>
            <?php else : ?>
                <div class="space-y-4">
                    <?php foreach ($announcements as $announcement) : ?>
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 hover:shadow-lg transition-all duration-300">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800 text-lg mb-2">
                                        <?php echo htmlspecialchars($announcement['title']); ?>
                                    </h4>
                                    <p class="text-gray-600 text-sm mb-2">
                                        <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        <?php echo date('d/m/Y H:i', strtotime($announcement['created_at'])); ?>
                                    </p>
                                </div>
                                <form method="POST" class="ml-4" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                                    <input type="hidden" name="id" value="<?php echo $announcement['id']; ?>">
                                    <button type="submit"
                                            name="delete_announcement"
                                            class="px-3 py-2 bg-red-500 text-white text-xs rounded-md hover:bg-red-600 transition-colors flex items-center justify-center gap-1">
                                        <i class="fas fa-trash"></i>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>

    </div>
</div>
