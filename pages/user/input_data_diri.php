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
        $update_fields = "nik='$nik', tanggal_lahir='$tanggal_lahir', alamat='$alamat', jk='$jk', pekerjaan='$pekerjaan', rt='$rt_id_post', rw='$rw_id_post', kk_id='$kk_id_post', status_approval='menunggu'";
        
        if ($has_tempat_lahir) $update_fields .= ", tempat_lahir='$tempat_lahir'";
        if ($has_goldar) $update_fields .= ", goldar='$goldar'";
        if ($has_agama) $update_fields .= ", agama='$agama'";
        if ($has_status_kawin) $update_fields .= ", status_kawin='$status_kawin'";
        
        $update_warga = "UPDATE warga SET $update_fields WHERE nama='$nama'";
        if (mysqli_query($conn, $update_warga)) {
            $message = 'Data berhasil diperbarui!';
            header("Location: data_diri?Berhasil Memperbarui");
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
            // Log activity - user registration
            $logged_user_id = $_SESSION['user_id'] ?? null;
            mysqli_query($conn, "INSERT INTO activities (action, entity, description, user_id) VALUES ('register', 'warga', 'Warga baru mendaftar: $nama', $logged_user_id)");
            
            $message = 'Data berhasil disimpan! Menunggu persetujuan dari Ketua RT.';
            header("Location: dashboard_user?success=1");
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
        <div class="max-w-4xl mx-auto">
            
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Data Diri</h1>
                <p class="text-gray-600 mt-2">Lihat dan kelola data pribadi Anda</p>
            </div>

<?php if ($message): ?>
            <div class="bg-green-50 border-l-4 border-green-400 text-green-800 px-6 py-4 rounded mb-6">
                <?php echo $message; ?>
            </div>
<?php endif; ?>

<?php if ($error): ?>
            <div class="bg-red-50 border-l-4 border-red-400 text-red-800 px-6 py-4 rounded mb-6">
                <?php echo $error; ?>
            </div>
<?php endif; ?>

<?php if ($existing_data && $has_status_approval && isset($existing_data['status_approval']) && $existing_data['status_approval'] !== 'diterima'): 
    $status = $existing_data['status_approval'];
    $status_class = '';
    $status_text = '';
    
    if ($status === 'diterima') {
        $status_class = 'border-green-400 bg-green-50 text-green-800';
        $status_text = 'Data Diterima';
    } elseif ($status === 'ditolak') {
        $status_class = 'border-red-400 bg-red-50 text-red-800';
        $status_text = 'Data Ditolak';
    } else {
        $status_class = 'border-yellow-400 bg-yellow-50 text-yellow-800';
        $status_text = 'Menunggu Persetujuan';
    }
?>
    <div class="border-l-4 <?php echo $status_class; ?> px-6 py-4 rounded-lg mb-8 shadow-sm">
        <div class="flex items-center justify-between">
            <span class="font-bold text-lg"><?php echo $status_text; ?></span>
            <button onclick="this.parentElement.parentElement.style.display='none'" class="text-gray-500 hover:text-gray-700">
                &times;
            </button>
        </div>
    </div>
<?php endif; ?>

            <div class="bg-white rounded-2xl shadow-xl border p-8">
                <?php if (!$existing_data): ?>
                <!-- New Data Form -->
                <form method="POST" class="space-y-6">
                    <h2 class="text-2xl font-bold text-gray-800 border-b pb-4">Input Data Diri</h2>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">NIK <span class="text-red-500">*</span></label>
                        <input type="text" name="nik" required maxlength="16" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Masukkan NIK 16 digit">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_lahir" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <?php if ($has_tempat_lahir): ?>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <?php endif; ?>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select name="jk" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <?php if ($has_goldar): ?>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Golongan Darah</label>
                            <select name="goldar" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                                <option value="O">O</option>
                            </select>
                        </div>
                        <?php endif; ?>
                        <?php if ($has_agama): ?>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Agama</label>
                            <select name="agama" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih</option>
                                <option value="Islam">Islam</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Budha">Budha</option>
                            </select>
                        </div>
                        <?php endif; ?>
                        <?php if ($has_status_kawin): ?>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Status Kawin</label>
                            <select name="status_kawin" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih</option>
                                <option value="Belum Kawin">Belum Kawin</option>
                                <option value="Kawin">Kawin</option>
                                <option value="Cerai Hidup">Cerai Hidup</option>
                                <option value="Cerai Mati">Cerai Mati</option>
                            </select>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pekerjaan</label>
                            <input type="text" name="pekerjaan" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Karyawan Swasta">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat <span class="text-red-500">*</span></label>
                            <textarea name="alamat" required rows="3" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Jl. Contoh No. 123"></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">RT <span class="text-red-500">*</span></label>
                            <select name="rt_id" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih RT</option>
                                <?php foreach ($rt_list as $rt): ?>
                                    <option value="<?php echo $rt['id']; ?>"><?php echo htmlspecialchars($rt['nama_rt']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">RW <span class="text-red-500">*</span></label>
                            <select name="rw_id" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih RW</option>
                                <?php foreach ($rw_list as $rw): ?>
                                    <option value="<?php echo $rw['id']; ?>"><?php echo htmlspecialchars($rw['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kartu Keluarga</label>
                            <select name="kk_id" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Tidak ada</option>
                                <?php foreach ($kk_list as $kk): ?>
                                    <option value="<?php echo $kk['id']; ?>"><?php echo htmlspecialchars($kk['kepala_keluaraga']); ?> - <?php echo $kk['no_kk']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <p class="text-sm text-yellow-800 font-medium">Data akan diperiksa oleh Ketua RT sebelum aktif.</p>
                    </div>

                    <button type="submit" name="submit_data_diri" class="w-full py-4 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-xl shadow-lg">
                        Simpan Data Diri
                    </button>
                </form>
                <?php else: ?>
                <!-- READ ONLY - Enhanced Detail -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-8 border-b pb-4">Informasi Pribadi</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        <div class="group bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl border shadow-sm hover:shadow-md transition-all">
                            <div class="font-semibold text-blue-800 mb-2 text-sm uppercase tracking-wide">NIK</div>
                            <div class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($existing_data['nik']); ?></div>
                        </div>

                        <div class="group bg-gradient-to-br from-indigo-50 to-indigo-100 p-6 rounded-xl border shadow-sm hover:shadow-md transition-all">
                            <div class="font-semibold text-indigo-800 mb-2 text-sm uppercase tracking-wide">Tanggal Lahir</div>
                            <div class="text-xl font-bold text-gray-900"><?php echo date('d F Y', strtotime($existing_data['tanggal_lahir'])); ?></div>
                        </div>

                        <div class="group bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-xl border shadow-sm hover:shadow-md transition-all">
                            <div class="font-semibold text-purple-800 mb-2 text-sm uppercase tracking-wide">Jenis Kelamin</div>
                            <div class="text-xl font-bold text-gray-900"><?php echo $existing_data['jk'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></div>
                        </div>

                        <?php if ($has_tempat_lahir && $existing_data['tempat_lahir']): ?>
                        <div class="group bg-gradient-to-br from-emerald-50 to-emerald-100 p-6 rounded-xl border shadow-sm hover:shadow-md transition-all">
                            <div class="font-semibold text-emerald-800 mb-2 text-sm uppercase tracking-wide">Tempat Lahir</div>
                            <div class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($existing_data['tempat_lahir']); ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if ($has_goldar && $existing_data['goldar']): ?>
                        <div class="group bg-gradient-to-br from-red-50 to-red-100 p-6 rounded-xl border shadow-sm hover:shadow-md transition-all">
                            <div class="font-semibold text-red-800 mb-2 text-sm uppercase tracking-wide">Golongan Darah</div>
                            <div class="text-3xl font-bold text-red-600"><?php echo $existing_data['goldar']; ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if ($has_agama && $existing_data['agama']): ?>
                        <div class="group bg-gradient-to-br from-amber-50 to-amber-100 p-6 rounded-xl border shadow-sm hover:shadow-md transition-all">
                            <div class="font-semibold text-amber-800 mb-2 text-sm uppercase tracking-wide">Agama</div>
                            <div class="text-xl font-bold text-gray-900"><?php echo $existing_data['agama']; ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if ($has_status_kawin && $existing_data['status_kawin']): ?>
                        <div class="group bg-gradient-to-br from-teal-50 to-teal-100 p-6 rounded-xl border shadow-sm hover:shadow-md transition-all">
                            <div class="font-semibold text-teal-800 mb-2 text-sm uppercase tracking-wide">Status Kawin</div>
                            <div class="text-xl font-bold text-gray-900"><?php echo $existing_data['status_kawin']; ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if ($existing_data['pekerjaan']): ?>
                        <div class="group bg-gradient-to-br from-orange-50 to-orange-100 p-6 rounded-xl border shadow-sm hover:shadow-md transition-all">
                            <div class="font-semibold text-orange-800 mb-2 text-sm uppercase tracking-wide">Pekerjaan</div>
                            <div class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($existing_data['pekerjaan']); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Alamat Section -->
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-8 rounded-2xl border shadow-lg">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Alamat Lengkap</h3>
                        <div class="bg-white p-6 rounded-xl border-2 shadow-inner">
                            <p class="text-lg leading-relaxed mb-3"><?php echo nl2br(htmlspecialchars($existing_data['alamat'])); ?></p>
                            <div class="text-sm text-gray-600 font-medium">
                                RT <?php 
                                $rt_name = '';
                                foreach ($rt_list as $rt) {
                                    if (isset($existing_data['rt']) && $rt['id'] == $existing_data['rt']) $rt_name = $rt['nama_rt'];
                                }
                                echo $rt_name; ?> RW <?php 
                                $rw_name = '';
                                foreach ($rw_list as $rw) {
                                    if (isset($existing_data['rw']) && $rw['id'] == $existing_data['rw']) $rw_name = $rw['name'];
                                }
                                echo $rw_name; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="text-center pt-12">
                        <a href="edit_data_diri" class="inline-block px-12 py-4 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold text-lg rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300">
                            Edit Data Diri
                        </a>
                        <p class="mt-3 text-sm text-gray-500">Ubah informasi pribadi jika diperlukan</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
