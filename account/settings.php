<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

include '../config/database.php';

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'user';
$message = '';

// Get user data from database
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (isset($_POST['update_profile'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $profile_photo = null;
    $old_photo = $user['profile_photo'] ?? '';
    
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            // Delete old photo first
            if (!empty($old_photo)) {
                $old_photo_path = $old_photo;
                if (strpos($old_photo_path, 'account/') === 0) {
                    $old_photo_path = $old_photo_path;
                } else {
                    $old_photo_path = 'account/' . $old_photo;
                }
                if (file_exists($old_photo_path) && is_file($old_photo_path)) {
                    unlink($old_photo_path);
                }
            }
            
            $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $ext;
            $upload_path = 'account/uploads/profiles/' . $new_filename;

            if (!is_dir('account/uploads/profiles')) {
                mkdir('account/uploads/profiles', 0777, true);
            }

            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $upload_path)) {
                $profile_photo = 'uploads/profiles/' . $new_filename;
            } else {
                $message = "Error uploading file.";
            }
        } else {
            $message = "Invalid file type. Only JPG, PNG, GIF allowed.";
        }
    }

    if (empty($message)) {
        $sql = "UPDATE users SET username=?, email=?";
        $params = [$username, $email];
        $types = "ss";

        if ($profile_photo) {
            $sql .= ", profile_photo=?";
            $params[] = $profile_photo;
            $types .= "s";
        }

        $sql .= " WHERE id=?";
        $params[] = $user_id;
        $types .= "i";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$params);

        if (mysqli_stmt_execute($stmt)) {
            $message = "Profil berhasil diperbarui!";
            $_SESSION['username'] = $username;
            
            // Refresh user data
            $sql = "SELECT * FROM users WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
        } else {
            $message = "Error updating profile.";
        }
    }
}

// Handle Password Change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_data = mysqli_fetch_assoc($result);
    
    if (!password_verify($current_password, $user_data['password'])) {
        $message = "Kata sandi saat ini salah!";
    } elseif (strlen($new_password) < 6) {
        $message = "Kata sandi baru minimal 6 karakter!";
    } elseif ($new_password !== $confirm_password) {
        $message = "Konfirmasi kata sandi tidak cocok!";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = "Kata sandi berhasil diperbarui!";
        } else {
            $message = "Error memperbarui kata sandi.";
        }
    }
}

$stats = [
    'account_age' => isset($user['created_at']) ? floor((time() - strtotime($user['created_at'])) / (60*60*24)) : 0,
    'role_display' => ucfirst($user['role']),
    'last_login' => isset($user['last_login']) ? date('d M Y, H:i', strtotime($user['last_login'])) : 'Never'
];

// Determine dashboard link based on role
$dashboard_link = 'dashboard_user';
$dashboard_name = 'Edit Profile Kamu!';
if ($role === 'admin') {
    $dashboard_link = 'dashboard_admin';
    $dashboard_name = 'Edit Profile Kamu!';
} elseif ($role === 'ketua') {
    $dashboard_link = 'dashboard_ketua';
    $dashboard_name = 'Edit Profile Kamu!';
}

// Simple profile photo path - just use it directly
$profile_photo_db = $user['profile_photo'] ?? '';

// Try multiple possible paths
$profile_photo_url = '';
if (!empty($profile_photo_db)) {
    // Try direct path first
    if (file_exists($profile_photo_db)) {
        $profile_photo_url = $profile_photo_db;
    } 
    // Try with account/ prefix
    elseif (file_exists('../../account/' . $profile_photo_db)) {
        $profile_photo_url = 'account/' . $profile_photo_db;
    }
    // Try with account// prefix (double)
    elseif (file_exists('../../account/account/' . $profile_photo_db)) {
        $profile_photo_url = '../../account/account/' . $profile_photo_db;
    }
    // If file doesn't exist but db has value, just use it as-is
    else {
        $profile_photo_url = $profile_photo_db;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun - Lurahgo.id</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex">
    <!-- Sidebar -->
    <div id="sidebar" class="fixed top-0 left-0 h-full bg-white shadow-md border-r border-gray-200 transition-all duration-300" style="width: 260px; z-index: 50;">
        <div class="p-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-bold text-gray-800">Lurahgo.id</h1>
                    <p class="text-xs text-gray-500"><?php echo $dashboard_name; ?></p>
                </div>
            </div>
        </div>
        <ul class="mt-4 space-y-1 px-3">
            <li>
                <a href="home" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                    <i class="fas fa-home w-5"></i>
                    <span>Beranda</span>
                </a>
            </li>
            <li>
                <a href="<?php echo $dashboard_link; ?>" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="settings" class="flex items-center gap-3 px-4 py-2.5 rounded-lg bg-gray-100 text-gray-800 font-medium">
                    <i class="fas fa-cog w-5"></i>
                    <span>Pengaturan</span>
                </a>
            </li>
        </ul>
        <div class="absolute bottom-4 left-4 right-4">
            <div class="text-center text-xs text-gray-400">
                <div>&copy; 2025 Lurahgo.id</div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 ml-[260px]">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex justify-between items-center px-8 py-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Pengaturan Akun</h2>
                    <p class="text-sm text-gray-500">Kelola informasi profil dan keamanan</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3 bg-gray-50 rounded-full px-4 py-2">
                        <?php if (!empty($profile_photo_url)): ?>
                            <img src="<?php echo htmlspecialchars($profile_photo_url); ?>" alt="Profile" class="w-8 h-8 rounded-full object-cover">
                        <?php else: ?>
                            <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold">
                                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                        <span class="text-sm font-medium text-gray-700"><?php echo htmlspecialchars($user['username']); ?></span>
                    </div>
                    <a href="logout" class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="p-8">
            <?php if ($message): ?>
                <div class="mb-6 p-4 rounded-lg <?php echo strpos($message, 'berhasil') !== false ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-red-50 border border-red-200 text-red-700'; ?>">
                    <span class="font-medium"><?php echo $message; ?></span>
                </div>
            <?php endif; ?>

            <!-- Section 1: Profil -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 mb-6">
                <div class="flex items-center mb-5">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-user text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-800">Informasi Profil</h3>
                        <p class="text-sm text-gray-500">Perbarui data diri Anda</p>
                    </div>
                </div>

                <form method="POST" enctype="multipart/form-data" class="space-y-5">
                    <div class="flex items-start gap-6">
                        <div class="flex-shrink-0">
                            <div class="relative">
                                <?php if (!empty($profile_photo_url)): ?>
                                    <img id="profile-preview" 
                                         src="<?php echo htmlspecialchars($profile_photo_url); ?>"
                                         alt="Profile Photo"
                                         class="w-24 h-24 rounded-full object-cover border-4 border-gray-100 shadow-sm">
                                <?php else: ?>
                                    <img id="profile-preview" 
                                         src="https://via.placeholder.com/120/3B82F6/FFFFFF?text=<?php echo strtoupper(substr($user['username'], 0, 1)); ?>"
                                         alt="Profile Photo"
                                         class="w-24 h-24 rounded-full object-cover border-4 border-gray-100 shadow-sm">
                                <?php endif; ?>
                                <label for="profile_photo" class="absolute bottom-0 right-0 bg-gray-800 p-2 rounded-full cursor-pointer hover:bg-gray-700 transition-colors shadow-lg">
                                    <i class="fas fa-camera text-white text-sm"></i>
                                </label>
                                <input type="file" id="profile_photo" name="profile_photo" accept="image/*" class="hidden">
                            </div>
                            <p class="text-xs text-gray-500 text-center mt-2">Klik icon kamera untuk ganti foto</p>
                        </div>
                        <div class="flex-1 space-y-4">
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                    <input type="text" name="username" required
                                           value="<?php echo htmlspecialchars($user['username']); ?>"
                                           class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" required
                                           value="<?php echo htmlspecialchars($user['email']); ?>"
                                           class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Peran</label>
                                <input type="text" readonly
                                       value="<?php echo ucfirst($user['role']); ?>"
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-lg bg-gray-50 text-gray-500">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" name="update_profile" 
                                class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center shadow-md hover:shadow-lg">
                            <i class="fas fa-save mr-2"></i>Simpan Profil
                        </button>
                    </div>
                </form>
            </div>

            <!-- Section 2: Keamanan -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 mb-6">
                <div class="flex items-center mb-5">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-shield-alt text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-800">Keamanan Akun</h3>
                        <p class="text-sm text-gray-500">Kelola kata sandi Anda</p>
                    </div>
                </div>

                <form method="POST" class="space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi Saat Ini</label>
                            <input type="password" name="current_password" required
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="Masukkan kata sandi lama">
                        </div>
                        <div></div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi Baru</label>
                            <input type="password" name="new_password" required minlength="6"
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="Minimal 6 karakter">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Kata Sandi</label>
                            <input type="password" name="confirm_password" required
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="Masukkan ulang kata sandi">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" name="change_password" 
                                class="px-5 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors flex items-center shadow-md hover:shadow-lg">
                            <i class="fas fa-key mr-2"></i>Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>

            <!-- Section 3: Info Akun -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 mb-6">
                <div class="flex items-center mb-5">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-info-circle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-800">Informasi Akun</h3>
                        <p class="text-sm text-gray-500">Detail akun Anda</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center text-gray-500 mb-2">
                            <i class="fas fa-calendar-day mr-2"></i>
                            <span class="text-sm">Anggota Sejak</span>
                        </div>
                        <p class="font-semibold text-gray-800"><?php echo date('d M Y', strtotime($user['created_at'])); ?></p>
                        <p class="text-xs text-gray-500"><?php echo $stats['account_age']; ?> hari</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center text-gray-500 mb-2">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            <span class="text-sm">Login Terakhir</span>
                        </div>
                        <p class="font-semibold text-gray-800"><?php echo $stats['last_login']; ?></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center text-gray-500 mb-2">
                            <i class="fas fa-id-card mr-2"></i>
                            <span class="text-sm">ID Akun</span>
                        </div>
                        <p class="font-semibold text-gray-800">#<?php echo $user['id']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Section 4: Zona Bahaya -->
            <div class="bg-white rounded-2xl shadow-lg border border-red-100 p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-red-600">Zona Bahaya</h3>
                        <p class="text-sm text-gray-500">Tindakan yang tidak dapat dibatalkan</p>
                    </div>
                </div>
                <p class="text-gray-600 text-sm mb-4">Apakah Anda ingin menghapus akun? Tindakan ini akan menghapus semua data secara permanen.</p>
                <button onclick="confirm('Apakah Anda yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan!')" 
                        class="px-5 py-2.5 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 transition-colors">
                    <i class="fas fa-trash mr-2"></i>Hapus Akun
                </button>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center text-sm text-gray-400">
                <p>&copy; 2025 Lurahgo.id. All rights reserved.</p>
            </div>
        </main>
    </div>

    <script src="settings.js"></script>
</body>
</html>
