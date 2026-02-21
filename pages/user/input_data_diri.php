<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: home");
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

// Check if status_approval column exists
$has_status_approval = false;
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM warga LIKE 'status_approval'");
if ($check_col && mysqli_num_rows($check_col) > 0) {
    $has_status_approval = true;
}

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

// Get existing data if any
$existing_data = null;
$select_fields = "nik, tanggal_lahir, alamat, jk, pekerjaan, rt, rw, kk_id";
if ($has_tempat_lahir) $select_fields .= ", tempat_lahir";
if ($has_goldar) $select_fields .= ", goldar";
if ($has_agama) $select_fields .= ", agama";
if ($has_status_kawin) $select_fields .= ", status_kawin";
if ($has_status_approval) $select_fields .= ", status_approval";

$check_existing = "SELECT $select_fields FROM warga WHERE nama = '$nama'";
$check_result = mysqli_query($conn, $check_existing);
if ($check_result && mysqli_num_rows($check_result) > 0) {
    $existing_data = mysqli_fetch_assoc($check_result);
}

if (isset($_POST['submit_data_diri'])) {
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
    
    // Check if warga already exists for this user
    $check_warga = "SELECT id FROM warga WHERE nama = '$nama'";
    $check_result = mysqli_query($conn, $check_warga);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Update existing record - build dynamic query
        $update_fields = "nik='$nik', tanggal_lahir='$tanggal_lahir', alamat='$alamat', jk='$jk', pekerjaan='$pekerjaan', rt='$rt_id_post', rw='$rw_id_post', kk_id='$kk_id_post'";
        
        if ($has_tempat_lahir) $update_fields .= ", tempat_lahir='$tempat_lahir'";
        if ($has_goldar) $update_fields .= ", goldar='$goldar'";
        if ($has_agama) $update_fields .= ", agama='$agama'";
        if ($has_status_kawin) $update_fields .= ", status_kawin='$status_kawin'";
        
        $update_warga = "UPDATE warga SET $update_fields WHERE nama='$nama'";
        if (mysqli_query($conn, $update_warga)) {
            $message = 'Data berhasil diperbarui!';
            header("Location: ../../pages/user/dashboard_user.php?success=1");
            exit();
        } else {
            $error = 'Gagal memperbarui data!';
        }
    } else {
        // Check if status_approval column exists
        $check_col = mysqli_query($conn, "SHOW COLUMNS FROM warga LIKE 'status_approval'");
        if (mysqli_num_rows($check_col) > 0) {
            // Insert new record with status_approval = 'menunggu'
            $insert_fields = "nama, nik, jk, tanggal_lahir, alamat, pekerjaan, rt, rw, kk_id, status, status_approval";
            $insert_values = "'$nama', '$nik', '$jk', '$tanggal_lahir', '$alamat', '$pekerjaan', '$rt_id_post', '$rw_id_post', '$kk_id_post', 'aktif', 'menunggu'";
            
            if ($has_tempat_lahir) {
                $insert_fields .= ", tempat_lahir";
                $insert_values .= ", '$tempat_lahir'";
            }
            if ($has_goldar) {
                $insert_fields .= ", goldar";
                $insert_values .= ", '$goldar'";
            }
            if ($has_agama) {
                $insert_fields .= ", agama";
                $insert_values .= ", '$agama'";
            }
            if ($has_status_kawin) {
                $insert_fields .= ", status_kawin";
                $insert_values .= ", '$status_kawin'";
            }
            
            $insert_warga = "INSERT INTO warga ($insert_fields) VALUES ($insert_values)";
        } else {
            // Insert new record without status_approval
            $insert_warga = "INSERT INTO warga (nama, nik, jk, tanggal_lahir, alamat, pekerjaan, rt, rw, kk_id, status) VALUES ('$nama', '$nik', '$jk', '$tanggal_lahir', '$alamat', '$pekerjaan', '$rt_id_post', '$rw_id_post', '$kk_id_post', 'aktif')";
        }
        
        if (mysqli_query($conn, $insert_warga)) {
            $message = 'Data berhasil disimpan! Menunggu persetujuan dari Ketua RT.';
            header("Location: ../../pages/user/dashboard_user.php?success=1");
            exit();
        } else {
            $error = 'Gagal menyimpan data!';
        }
    }
    
    // Refresh existing data
    $check_existing = "SELECT $select_fields FROM warga WHERE nama = '$nama'";
    $check_result = mysqli_query($conn, $check_existing);
    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $existing_data = mysqli_fetch_assoc($check_result);
    }
}
?>

<div id="mainContent" class="ml-64 min-h-screen bg-gray-50">
    <div class="p-8">
        <div class="max-w-7xl mx-auto">
            
            <!-- Header -->
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mr-10">
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Data Diri</h1>
                    <p class="text-gray-500 text-sm">Lengkapi data diri Anda dengan benar</p>
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

            <!-- Status Info -->
            <?php if ($existing_data && $has_status_approval && isset($existing_data['status_approval'])): ?>
                <?php 
                $status = $existing_data['status_approval'];
                $status_class = '';
                $status_icon = '';
                $status_text = '';
                
                if ($status === 'diterima') {
                    $status_class = 'bg-green-100 border-green-400 text-green-700';
                    $status_icon = 'fa-check-circle';
                    $status_text = 'Data Anda telah DITERIMA';
                } elseif ($status === 'ditolak') {
                    $status_class = 'bg-red-100 border-red-400 text-red-700';
                    $status_icon = 'fa-times-circle';
                    $status_text = 'Data Anda DITOLAK - Silakan perbaiki data Anda';
                } else {
                    $status_class = 'bg-yellow-100 border-yellow-400 text-yellow-700';
                    $status_icon = 'fa-clock';
                    $status_text = 'Data Anda MENUNGGU persetujuan';
                }
                ?>
                <div id="status-banner" class="<?php echo $status_class; ?> border-l-4 px-4 py-3 rounded mb-4">
                    <div class="flex items-center">
                        <i class="fas <?php echo $status_icon; ?> mr-3"></i>
                        <span class="font-medium"><?php echo $status_text; ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form Card - 3 Kolom -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                <form method="POST" class="space-y-4">
                    
                    <!-- Baris 1: NIK -->
                    <div>
                        <label for="nik" class="block text-sm font-semibold text-gray-700 mb-1">NIK</label>
                        <input type="text" id="nik" name="nik" required 
                               value="<?php echo $existing_data ? htmlspecialchars($existing_data['nik']) : ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Masukkan 16 digit NIK">
                    </div>

                    <!-- Baris 2: 3 Kolom -->
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="tempat_lahir" class="block text-sm font-semibold text-gray-700 mb-1">Tempat Lahir</label>
                            <input type="text" id="tempat_lahir" name="tempat_lahir" 
                                   value="<?php echo $existing_data && isset($existing_data['tempat_lahir']) ? htmlspecialchars($existing_data['tempat_lahir']) : ''; ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Jakarta">
                        </div>
                        <div>
                            <label for="tanggal_lahir" class="block text-sm font-semibold text-gray-700 mb-1">Tgl Lahir</label>
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir" required 
                                   value="<?php echo $existing_data && $existing_data['tanggal_lahir'] ? htmlspecialchars($existing_data['tanggal_lahir']) : ''; ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="jk" class="block text-sm font-semibold text-gray-700 mb-1">JK</label>
                            <select id="jk" name="jk" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih</option>
                                <option value="L" <?php echo $existing_data && $existing_data['jk'] === 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="P" <?php echo $existing_data && $existing_data['jk'] === 'P' ? 'selected' : ''; ?>>Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <!-- Baris 3: 3 Kolom -->
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="goldar" class="block text-sm font-semibold text-gray-700 mb-1">Gol. Darah</label>
                            <select id="goldar" name="goldar" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih</option>
                                <option value="A" <?php echo $existing_data && isset($existing_data['goldar']) && $existing_data['goldar'] === 'A' ? 'selected' : ''; ?>>A</option>
                                <option value="B" <?php echo $existing_data && isset($existing_data['goldar']) && $existing_data['goldar'] === 'B' ? 'selected' : ''; ?>>B</option>
                                <option value="AB" <?php echo $existing_data && isset($existing_data['goldar']) && $existing_data['goldar'] === 'AB' ? 'selected' : ''; ?>>AB</option>
                                <option value="O" <?php echo $existing_data && isset($existing_data['goldar']) && $existing_data['goldar'] === 'O' ? 'selected' : ''; ?>>O</option>
                            </select>
                        </div>
                        <div>
                            <label for="agama" class="block text-sm font-semibold text-gray-700 mb-1">Agama</label>
                            <select id="agama" name="agama" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih</option>
                                <option value="Islam" <?php echo $existing_data && isset($existing_data['agama']) && $existing_data['agama'] === 'Islam' ? 'selected' : ''; ?>>Islam</option>
                                <option value="Kristen" <?php echo $existing_data && isset($existing_data['agama']) && $existing_data['agama'] === 'Kristen' ? 'selected' : ''; ?>>Kristen</option>
                                <option value="Katolik" <?php echo $existing_data && isset($existing_data['agama']) && $existing_data['agama'] === 'Katolik' ? 'selected' : ''; ?>>Katolik</option>
                                <option value="Hindu" <?php echo $existing_data && isset($existing_data['agama']) && $existing_data['agama'] === 'Hindu' ? 'selected' : ''; ?>>Hindu</option>
                                <option value="Budha" <?php echo $existing_data && isset($existing_data['agama']) && $existing_data['agama'] === 'Budha' ? 'selected' : ''; ?>>Budha</option>
                            </select>
                        </div>
                        <div>
                            <label for="status_kawin" class="block text-sm font-semibold text-gray-700 mb-1">Status Kawin</label>
                            <select id="status_kawin" name="status_kawin" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih</option>
                                <option value="Belum Kawin" <?php echo $existing_data && isset($existing_data['status_kawin']) && $existing_data['status_kawin'] === 'Belum Kawin' ? 'selected' : ''; ?>>Belum Kawin</option>
                                <option value="Kawin" <?php echo $existing_data && isset($existing_data['status_kawin']) && $existing_data['status_kawin'] === 'Kawin' ? 'selected' : ''; ?>>Kawin</option>
                                <option value="Cerai Hidup" <?php echo $existing_data && isset($existing_data['status_kawin']) && $existing_data['status_kawin'] === 'Cerai Hidup' ? 'selected' : ''; ?>>Cerai Hidup</option>
                                <option value="Cerai Mati" <?php echo $existing_data && isset($existing_data['status_kawin']) && $existing_data['status_kawin'] === 'Cerai Mati' ? 'selected' : ''; ?>>Cerai Mati</option>
                            </select>
                        </div>
                    </div>

                    <!-- Baris 4: Pekerjaan -->
                    <div>
                        <label for="pekerjaan" class="block text-sm font-semibold text-gray-700 mb-1">Pekerjaan</label>
                        <input type="text" id="pekerjaan" name="pekerjaan" 
                               value="<?php echo $existing_data && isset($existing_data['pekerjaan']) ? htmlspecialchars($existing_data['pekerjaan']) : ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Contoh: Karyawan, Wiraswasta, Pelajar">
                    </div>

                    <!-- Baris 5: Alamat -->
                    <div>
                        <label for="alamat" class="block text-sm font-semibold text-gray-700 mb-1">Alamat</label>
                        <textarea id="alamat" name="alamat" required rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Jl. Raya RT 05 RW 02 No. 15"><?php echo $existing_data ? htmlspecialchars($existing_data['alamat']) : ''; ?></textarea>
                    </div>

                    <!-- Baris 6: RT RW KK -->
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="rt_id" class="block text-sm font-semibold text-gray-700 mb-1">RT</label>
                            <select id="rt_id" name="rt_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih RT</option>
                                <?php foreach ($rt_list as $rt): ?>
                                    <option value="<?php echo $rt['id']; ?>" <?php echo $existing_data && $existing_data['rt'] == $rt['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($rt['nama_rt']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="rw_id" class="block text-sm font-semibold text-gray-700 mb-1">RW</label>
                            <select id="rw_id" name="rw_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih RW</option>
                                <?php foreach ($rw_list as $rw): ?>
                                    <option value="<?php echo $rw['id']; ?>" <?php echo $existing_data && $existing_data['rw'] == $rw['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($rw['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="kk_id" class="block text-sm font-semibold text-gray-700 mb-1">KK (Opsional)</label>
                            <select id="kk_id" name="kk_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih KK</option>
                                <?php foreach ($kk_list as $kk): ?>
                                    <option value="<?php echo $kk['id']; ?>" <?php echo $existing_data && isset($existing_data['kk_id']) && $existing_data['kk_id'] == $kk['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($kk['kepala_keluaraga'] . ' - ' . $kk['no_kk']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-sm text-yellow-800">Data akan ditinjau oleh Ketua RT untuk persetujuan.</p>
                    </div>

                    <!-- Submit -->
                    <div>
                        <button type="submit" name="submit_data_diri" 
                                class="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                            Simpan Data Diri
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
