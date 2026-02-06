<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

include '../config/database.php';

$user_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Handle profile photo upload
    $profile_photo = null;
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $ext;
            $upload_path = 'uploads/profiles/' . $new_filename;

            if (!is_dir('uploads/profiles')) {
                mkdir('uploads/profiles', 0777, true);
            }

            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $upload_path)) {
                $profile_photo = $upload_path;
            } else {
                $message = "Error uploading file.";
            }
        } else {
            $message = "Invalid file type. Only JPG, PNG, GIF allowed.";
        }
    }

    // Update user data
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
        } else {
            $message = "Error updating profile.";
        }
    }
}

// Get current user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Get user statistics
$stats = [
    'account_age' => isset($user['created_at']) ? floor((time() - strtotime($user['created_at'])) / (60*60*24)) : 0,
    'role_display' => ucfirst($user['role']),
    'last_login' => isset($user['last_login']) ? date('d M Y, H:i', strtotime($user['last_login'])) : 'Never'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun - Lurahgo.id</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-purple-50 min-h-screen" style="font-family: 'Poppins', sans-serif;">
    <!-- Background Animation -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-blue-400/20 to-purple-400/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-br from-purple-400/20 to-pink-400/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gradient-to-br from-indigo-400/10 to-cyan-400/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 4s;"></div>
    </div>

    <div class="relative max-w-6xl mx-auto py-12 px-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl shadow-lg mb-4">
                <i class="fas fa-cog text-white text-2xl"></i>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 bg-clip-text text-transparent mb-4">
                Pengaturan Akun
            </h1>
            <p class="text-gray-600 text-lg">Kelola preferensi akun dan informasi profil Anda</p>
        </div>

        <!-- Success/Error Messages -->
        <?php if ($message): ?>
            <div class="mb-8 p-4 rounded-2xl shadow-lg backdrop-blur-sm border <?php echo strpos($message, 'successfully') !== false ? 'bg-green-50/80 border-green-200 text-green-800' : 'bg-red-50/80 border-red-200 text-red-800'; ?> animate-fade-in">
                <div class="flex items-center">
                    <i class="fas <?php echo strpos($message, 'successfully') !== false ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> mr-3 text-xl"></i>
                    <span class="font-medium"><?php echo $message; ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Settings Tabs -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 overflow-hidden">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200">
                <nav class="flex">
                    <button id="profile-tab" class="tab-button active flex-1 py-4 px-6 text-center font-semibold text-blue-600 border-b-2 border-blue-600 transition-all duration-300">
                        <i class="fas fa-user mr-2"></i>Profil
                    </button>
                    <button id="account-tab" class="tab-button flex-1 py-4 px-6 text-center font-semibold text-gray-500 hover:text-gray-700 transition-all duration-300">
                        <i class="fas fa-shield-alt mr-2"></i>Akun
                    </button>
                    <button id="preferences-tab" class="tab-button flex-1 py-4 px-6 text-center font-semibold text-gray-500 hover:text-gray-700 transition-all duration-300">
                        <i class="fas fa-sliders-h mr-2"></i>Preferensi
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-8 md:p-12">
