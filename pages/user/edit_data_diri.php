<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../home");
    exit();
}

ob_start();

include '../../config/database.php';
include '../../layouts/user/header.php';
include '../../layouts/user/sidebar.php';

$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

$nama = $user['username'] ?? '';

// Get RT/RW list for dropdown
$rt_list = [];
$rw_list = [];
$kk_list = [];
$rt_result = mysqli_query($conn, "SELECT * FROM rt ORDER BY nama_rt");
if ($rt_result) $rt_list = mysqli_fetch_all($rt_result, MYSQLI_ASSOC);
$rw_result = mysqli_query($conn, "SELECT * FROM rw ORDER BY name");
if ($rw_result) $rw_list = mysqli_fetch_all($rw_result, MYSQLI_ASSOC);
$kk_result = mysqli_query($conn, "SELECT id, kepala_keluaraga, no_kk FROM kk ORDER BY no_kk");
if ($kk_result) $kk_list = mysqli_fetch_all($kk_result, MYSQLI_ASSOC);

$message = '';
$error = '';

// Check if additional columns exist
$has_tempat_lahir = false;
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM warga LIKE 'tempat_lahir'");
if ($check_col && mysqli_num_rows($check_col) > 0) {
    $has_tempat_lahir = true;
}

$has_goldar = false;
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM warga LIKE 'goldar'");
if ($check_col && mysqli_num_rows($check_col) > 0) {
    $has_goldar = true;
}

$has_agama = false;
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM warga LIKE 'agama'");
if ($check_col && mysqli_num_rows($check_col) > 0) {
    $has_agama = true;
}

$has_status_kawin = false;
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM warga LIKE 'status_kawin'");
if ($check_col && mysqli_num_rows($check_col) > 0) {
    $has_status_kawin = true;
}

// Get existing data (required for edit)
$existing_data = null;
$select_fields = "nik, tanggal_lahir, alamat, jk, pekerjaan, rt, rw, kk_id";
if ($has_tempat_lahir) $select_fields .= ", tempat_lahir";
if ($has_goldar) $select_fields .= ", goldar";
if ($has_agama) $select_fields .= ", agama";
if ($has_status_kawin) $select_fields .= ", status_kawin";

$check_existing = "SELECT $select_fields FROM warga WHERE nama = '$nama'";
$check_result = mysqli_query($conn, $check_existing);
if ($check_result && mysqli_num_rows($check_result) > 0) {
    $existing_data = mysqli_fetch_assoc($check_result);
} else {
    // Redirect if no data to edit
    header("Location: input_data_diri.php");
    exit();
}

if (isset($_POST['update_data_diri'])) {
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $tanggal_lahir = mysqli_real_escape_string($conn, $_POST['tanggal_lahir']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $jk = mysqli_real_escape_string($conn, $_POST['jk']);
    $pekerjaan = mysqli_real_escape_string($conn, $_POST['pekerjaan'] ?? '');
    $tempat_lahir = mysqli_real_escape_string($conn, $_POST['tempat_lahir'] ?? '');
    $goldar = mysqli_real_escape_string($conn, $_POST['goldar'] ?? '');
    $agama = mysqli_real_escape_string($conn, $_POST['agama'] ?? '');
    $status_kawin = mysqli_real_escape_string($conn, $_POST['status_kawin'] ?? '');
    $rt_id_post = $_POST['rt_id'] ?? null;
    $rw_id_post = $_POST['rw_id'] ?? null;
    $kk_id_post = $_POST['kk_id'] ?? null;
    
    // Update existing record - build dynamic query
    $update_fields = "nik='$nik', tanggal_lahir='$tanggal_lahir', alamat='$alamat', jk='$jk', pekerjaan='$pekerjaan', rt='$rt_id_post', rw='$rw_id_post', kk_id='$kk_id_post', status_approval='menunggu'";
    
    if ($has_tempat_lahir) $update_fields .= ", tempat_lahir='$tempat_lahir'";
    if ($has_goldar) $update_fields .= ", goldar='$goldar'";
    if ($has_agama) $update_fields .= ", agama='$agama'";
    if ($has_status_kawin) $update_fields .= ", status_kawin='$status_kawin'";
    
    $update_warga = "UPDATE warga SET $update_fields WHERE nama='$nama'";
    if (mysqli_query($conn, $update_warga)) {
        $message = 'Data berhasil diperbarui!';
        // Refresh data
        $check_result = mysqli_query($conn, $check_existing);
        $existing_data = mysqli_fetch_assoc($check_result);
    } else {
        $error = 'Gagal memperbarui data: ' . mysqli_error($conn);
    }
}
?>

<div id="mainContent" class="ml-64 min-h-screen bg-gray-50">
    <div class="p-8">
        <div class="max-w-7xl mx-auto">
            
            <!-- Header -->
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-orange-500 rounded-2xl flex items-center justify-center mr-4 text-white">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Edit Data Diri</h1>
                    <p class="text-gray-500 text-sm">Perbarui data diri Anda</p>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8">
                <form method="POST" class="space-y-6">
                    
                    <!-- Row 1: NIK -->
                    <div>
                        <label for="nik" class="block text-sm font-semibold text-gray-700 mb-2">NIK <span class="text-orange-500">*</span></label>
                        <input type="text" id="nik" name="nik" required maxlength="16"
                               value="<?php echo htmlspecialchars($existing_data['nik'] ?? ''); ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>

                    <!-- Row 2: 3 Columns -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="tempat_lahir" class="block text-sm font-semibold text-gray-700 mb-2">Tempat Lahir</label>
                            <input type="text" id="tempat_lahir" name="tempat_lahir" 
                                   value="<?php echo htmlspecialchars($existing_data['tempat_lahir'] ?? ''); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label for="tanggal_lahir" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Lahir <span class="text-orange-500">*</span></label>
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir" required 
                                   value="<?php echo htmlspecialchars($existing_data['tanggal_lahir'] ?? ''); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label for="jk" class="block text-sm font-semibold text-gray-700 mb-2">Jenis Kelamin <span class="text-orange-500">*</span></label>
                            <select id="jk" name="jk" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                                <option value="">Pilih</option>
                                <option value="L" <?php echo ($existing_data['jk'] ?? '') === 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="P" <?php echo ($existing_data['jk'] ?? '') === 'P' ? 'selected' : ''; ?>>Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 3: 3 Columns -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="goldar" class="block text-sm font-semibold text-gray-700 mb-2">Golongan Darah</label>
                            <select id="goldar" name="goldar" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                                <option value="">Pilih</option>
                                <option value="A" <?php echo ($existing_data['goldar'] ?? '') === 'A' ? 'selected' : ''; ?>>A</option>
                                <option value="B" <?php echo ($existing_data['goldar'] ?? '') === 'B' ? 'selected' : ''; ?>>B</option>
                                <option value="AB" <?php echo ($existing_data['goldar'] ?? '') === 'AB' ? 'selected' : ''; ?>>AB</option>
                                <option value="O" <?php echo ($existing_data['goldar'] ?? '') === 'O' ? 'selected' : ''; ?>>O</option>
                            </select>
                        </div>
                        <div>
                            <label for="agama" class="block text-sm font-semibold text-gray-700 mb-2">Agama</label>
                            <select id="agama" name="agama" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                                <option value="">Pilih</option>
                                <option value="Islam" <?php echo ($existing_data['agama'] ?? '') === 'Islam' ? 'selected' : ''; ?>>Islam</option>
                                <option value="Kristen" <?php echo ($existing_data['agama'] ?? '') === 'Kristen' ? 'selected' : ''; ?>>Kristen</option>
                                <option value="Katolik" <?php echo ($existing_data['agama'] ?? '') === 'Katolik' ? 'selected' : ''; ?>>Katolik</option>
                                <option value="Hindu" <?php echo ($existing_data['agama'] ?? '') === 'Hindu' ? 'selected' : ''; ?>>Hindu</option>
                                <option value="Budha" <?php echo ($existing_data['agama'] ?? '') === 'Budha' ? 'selected' : ''; ?>>Budha</option>
                            </select>
                        </div>
                        <div>
                            <label for="status_kawin" class="block text-sm font-semibold text-gray-700 mb-2">Status Kawin</label>
                            <select id="status_kawin" name="status_kawin" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                                <option value="">Pilih</option>
                                <option value="Belum Kawin" <?php echo ($existing_data['status_kawin'] ?? '') === 'Belum Kawin' ? 'selected' : ''; ?>>Belum Kawin</option>
                                <option value="Kawin" <?php echo ($existing_data['status_kawin'] ?? '') === 'Kawin' ? 'selected' : ''; ?>>Kawin</option>
                                <option value="Cerai Hidup" <?php echo ($existing_data['status_kawin'] ?? '') === 'Cerai Hidup' ? 'selected' : ''; ?>>Cerai Hidup</option>
                                <option value="Cerai Mati" <?php echo ($existing_data['status_kawin'] ?? '') === 'Cerai Mati' ? 'selected' : ''; ?>>Cerai Mati</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 4: Pekerjaan -->
                    <div>
                        <label for="pekerjaan" class="block text-sm font-semibold text-gray-700 mb-2">Pekerjaan</label>
                        <input type="text" id="pekerjaan" name="pekerjaan" 
                               value="<?php echo htmlspecialchars($existing_data['pekerjaan'] ?? ''); ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
                               placeholder="Contoh: Karyawan, Wiraswasta, Pelajar">
                    </div>

                    <!-- Row 5: Alamat -->
                    <div>
                        <label for="alamat" class="block text-sm font-semibold text-gray-700 mb-2">Alamat <span class="text-orange-500">*</span></label>
                        <textarea id="alamat" name="alamat" required rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"><?php echo htmlspecialchars($existing_data['alamat'] ?? ''); ?></textarea>
                    </div>

                    <!-- Row 6: RT RW KK -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="rt_id" class="block text-sm font-semibold text-gray-700 mb-2">RT <span class="text-orange-500">*</span></label>
                            <select id="rt_id" name="rt_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                                <option value="">Pilih RT</option>
                                <?php foreach ($rt_list as $rt): ?>
                                    <option value="<?php echo $rt['id']; ?>" <?php echo ($existing_data['rt'] ?? '') == $rt['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($rt['nama_rt']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="rw_id" class="block text-sm font-semibold text-gray-700 mb-2">RW <span class="text-orange-500">*</span></label>
                            <select id="rw_id" name="rw_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                                <option value="">Pilih RW</option>
                                <?php foreach ($rw_list as $rw): ?>
                                    <option value="<?php echo $rw['id']; ?>" <?php echo ($existing_data['rw'] ?? '') == $rw['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($rw['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="kk_id" class="block text-sm font-semibold text-gray-700 mb-2">Kartu Keluarga (Opsional)</label>
                            <select id="kk_id" name="kk_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                                <option value="">Pilih KK</option>
                                <?php foreach ($kk_list as $kk): ?>
                                    <option value="<?php echo $kk['id']; ?>" <?php echo isset($existing_data['kk_id']) && $existing_data['kk_id'] == $kk['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($kk['kepala_keluaraga'] . ' - ' . $kk['no_kk']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <button type="submit" name="update_data_diri" 
                                class="flex-1 py-4 bg-orange-500 text-white font-bold rounded-xl hover:bg-orange-600 focus:outline-none focus:ring-4 focus:ring-orange-300 transition-all shadow-lg">
                            <i class="fas fa-save mr-2"></i>Update Data Diri
                        </button>
                        <a href="data_diri" class="flex-1 py-4 bg-gray-500 text-white font-bold rounded-xl hover:bg-gray-600 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-all text-center shadow-lg flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
