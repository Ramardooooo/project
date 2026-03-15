<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">

        <h2 class="text-4xl font-serif text-center mb-16 text-gray-800">
            Rating & Testimoni Pengguna
        </h2>

<?php
include 'config/database.php';
$has_testimoni = false;
$user_id = $_SESSION['user_id'] ?? null;
if ($user_id) {
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM testimonials WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $has_testimoni = $row['count'] > 0;
    mysqli_stmt_close($stmt);
}
?>

<div class="grid md:grid-cols-3 gap-8" id="testimonials-grid">
<?php
$stmt = mysqli_prepare($conn, "SELECT * FROM testimonials ORDER BY created_at DESC LIMIT 6");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $profile_photo = $row['profile_photo'] ?? '';
    $avatar_url = '';
    if (!empty($profile_photo)) {
        $possible_paths = [
            '../account/' . $profile_photo,
            '../../account/' . $profile_photo,
            $profile_photo,
            '../' . $profile_photo
        ];
        foreach ($possible_paths as $path) {
            if (file_exists($path)) {
                $avatar_url = $path;
                break;
            }
        }
    }
    // Get user profile photo from account/uploads/profiles/
    $user_result = mysqli_query($conn, "SELECT profile_photo FROM users WHERE username = '" . mysqli_real_escape_string($conn, $row['name']) . "' LIMIT 1");
    $user_photo = '';
    if ($user_row = mysqli_fetch_assoc($user_result)) {
        $user_photo = $user_row['profile_photo'];
    }
    $avatar_url = '';
    if (!empty($user_photo)) {
        $possible_paths = [
            '../account/account/uploads/profiles/' . $user_photo,
            '../../account/account/uploads/profiles/' . $user_photo,
            '../account/uploads/profiles/' . $user_photo,
            '../../account/uploads/profiles/' . $user_photo,
            $user_photo
        ];
        foreach ($possible_paths as $path) {
            if (file_exists($path)) {
                $avatar_url = $path;
                break;
            }
        }
    }
    $avatar = $avatar_url ?: "https://randomuser.me/api/portraits/" . ($row['rating'] % 2 ? 'men' : 'women') . '/' . rand(1,99) . ".jpg";
    $rating_stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $rating_stars .= ($i <= $row['rating']) ? '<i class="fas fa-star text-yellow-400"></i>' : '<i class="fas fa-star text-gray-300"></i>';
    }
    echo '<div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center mb-4">
            <img src="' . htmlspecialchars($avatar) . '" alt="' . htmlspecialchars($row['name']) . '" class="w-12 h-12 rounded-full mr-4 object-cover">
            <div>
                <h4 class="font-semibold text-gray-800">' . htmlspecialchars($row['name']) . '</h4>
                <p class="text-sm text-gray-500">' . date('d M Y', strtotime($row['created_at'])) . '</p>
            </div>
        </div>
        <p class="text-gray-600 italic mb-4">" ' . htmlspecialchars($row['description']) . '"</p>
        <div class="flex mt-4">' . $rating_stars . '</div>
    </div>';
}
mysqli_stmt_close($stmt);
?>
</div>

    <!-- Testimoni Form Section -->
    <div class="mt-16 pt-12 border-t border-gray-200">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl p-8 lg:p-12">
                <?php if (!$has_testimoni): ?>
                <h3 class="text-2xl font-serif font-bold text-gray-800 text-center mb-6">Kirim Testimoni Anda</h3>
                <p class="text-center text-gray-600 mb-8">Bantu kami improve layanan dengan rating dan feedback Anda!</p>

                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['testimonial_submit'])) {
                    $name = trim($_POST['name'] ?? $_SESSION['username'] ?? '');
                    $rating = intval($_POST['rating'] ?? 0);
                    $description = trim($_POST['description'] ?? '');

                    $errors = [];
                    if (empty($name)) $errors[] = 'Nama wajib diisi.';
                    if ($rating < 1 || $rating > 5) $errors[] = 'Rating harus 1-5.';
                    if (empty($description)) $errors[] = 'Deskripsi wajib diisi.';
                    if (strlen($description) < 10) $errors[] = 'Deskripsi minimal 10 karakter.';

                    if (empty($errors)) {
                        $stmt = mysqli_prepare($conn, "INSERT INTO testimonials (name, rating, description, user_id) VALUES (?, ?, ?, ?)");
                        mysqli_stmt_bind_param($stmt, "sisi", $name, $rating, $description, $user_id);

                        if (mysqli_stmt_execute($stmt)) {
                            $has_testimoni = true; // Update immediately
                            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-xl mb-6">
                                <i class="fas fa-check-circle mr-3"></i> Terima kasih atas testimoni Anda!
                            </div>';
                        } else {
                            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl mb-6">
                                <i class="fas fa-exclamation-triangle mr-3"></i> Gagal: ' . mysqli_error($conn) . '
                            </div>';
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl mb-6">
                            <i class="fas fa-exclamation-triangle mr-3"></i> ' . implode('<br>', $errors) . '
                        </div>';
                    }
                } ?>

                <form method="POST" class="space-y-6">
                    <input type="hidden" name="testimonial_submit" value="1">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>" maxlength="255"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                        <div class="flex items-center space-x-1 text-2xl mb-2">
                            <i class="far fa-star cursor-pointer hover:text-yellow-400 text-gray-400 star" onclick="setRating(1)" title="1"></i>
                            <i class="far fa-star cursor-pointer hover:text-yellow-400 text-gray-400 star" onclick="setRating(2)" title="2"></i>
                            <i class="far fa-star cursor-pointer hover:text-yellow-400 text-gray-400 star" onclick="setRating(3)" title="3"></i>
                            <i class="far fa-star cursor-pointer hover:text-yellow-400 text-gray-400 star" onclick="setRating(4)" title="4"></i>
                            <i class="far fa-star cursor-pointer hover:text-yellow-400 text-gray-400 star" onclick="setRating(5)" title="5"></i>
                        </div>
                        <input type="hidden" id="rating" name="rating" value="0" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Testimoni Anda</label>
                        <textarea name="description" rows="4" required minlength="10"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-vertical"
                                  placeholder="Ceritakan pengalaman Anda..."></textarea>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold py-4 px-6 rounded-xl text-lg shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200">
                        <i class="fas fa-paper-plane mr-2"></i> Kirim Testimoni
                    </button>
                </form>
                <?php else: ?>
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-green-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-star text-3xl text-green-500"></i>
                    </div>
                    <h3 class="text-2xl font-serif font-bold text-green-600 mb-4">Terima kasih telah memberikan rating!</h3>
                    <p class="text-gray-600 text-lg mb-8 max-w-md mx-auto">Kontribusi Anda sangat berharga untuk kami. Testimoni Anda telah tercatat dan akan ditampilkan setelah diverifikasi.</p>
                   
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
    </div>
</section>

<script>
function setRating(stars) {
    document.getElementById('rating').value = stars;
    const starIcons = document.querySelectorAll('.star');
    starIcons.forEach((icon, index) => {
        if (index < stars) {
            icon.classList.remove('text-gray-400');
            icon.classList.add('text-yellow-400', 'fas');
            icon.classList.remove('far');
        } else {
            icon.classList.remove('text-yellow-400', 'fas');
            icon.classList.add('text-gray-400', 'far');
        }
    });
}
</script>
