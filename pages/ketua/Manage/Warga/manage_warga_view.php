<div id="mainContent" class="ml-64 p-8 bg-white min-h-screen transition-all duration-300">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Manajemen Data Warga</h1>
        <button onclick="openAddModal()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            <i class="fas fa-plus mr-2"></i>Tambah Warga
        </button>
    </div>

    <!-- Pending Approvals Alert -->
    <?php
    if ($has_status_approval) {
        $pending_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM warga WHERE status_approval = 'menunggu'"));
        if ($pending_count && $pending_count['total'] > 0):
    ?>
    <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    Ada <span class="font-bold"><?php echo $pending_count['total']; ?></span> data warga yang menunggu persetujuan.
                </p>
            </div>
        </div>
    </div>
    <?php 
        endif;
    }
    ?>

    <!-- Search -->
    <div class="mb-6">
        <form method="GET" class="flex gap-4">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari nama, NIK, atau alamat..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button type="submit" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
        </form>
    </div>

    <!-- Warga Table -->
    <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100/50">
                    <tr>
                        <?php if ($has_status_approval): ?>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <?php endif; ?>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NIK</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">JK</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tgl Lahir</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tempat Lahir</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Gol. Darah</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Agama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status Kawin</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pekerjaan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">RT/RW</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">KK</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100/50">
                    <?php while ($warga = mysqli_fetch_assoc($warga_result)): ?>
                    <?php 
                        $status_approval = $has_status_approval ? ($warga['status_approval'] ?? null) : null;
                        
                        $status_class = '';
                        $status_text = '';
                        $status_icon = '';
                        
                        if ($status_approval == 'menunggu') {
                            $status_class = 'bg-yellow-100 text-yellow-800';
                            $status_text = 'Menunggu';
                            $status_icon = 'fa-clock';
                        } elseif ($status_approval == 'diterima') {
                            $status_class = 'bg-green-100 text-green-800';
                            $status_text = 'Diterima';
                            $status_icon = 'fa-check-circle';
                        } elseif ($status_approval == 'ditolak') {
                            $status_class = 'bg-red-100 text-red-800';
                            $status_text = 'Ditolak';
                            $status_icon = 'fa-times-circle';
                        } else {
                            $status_class = 'bg-blue-100 text-blue-800';
                            $status_text = 'Aktif';
                            $status_icon = 'fa-check';
                        }
                    ?>
                    <tr class="hover:bg-gray-50/50 transition-colors duration-200">
                        <?php if ($has_status_approval): ?>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $status_class; ?>">
                                <i class="fas <?php echo $status_icon; ?> mr-1"></i>
                                <?php echo $status_text; ?>
                            </span>
                        </td>
                        <?php endif; ?>
                        <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($warga['nik'] ?? ''); ?></td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($warga['nama'] ?? ''); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo ($warga['jk'] ?? '') == 'L' ? 'L' : 'P'; ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo $warga['tanggal_lahir'] ? date('d-m-Y', strtotime($warga['tanggal_lahir'])) : '-'; ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars($warga['tempat_lahir'] ?? '-'); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars($warga['goldar'] ?? '-'); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars($warga['agama'] ?? '-'); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars($warga['status_kawin'] ?? '-'); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars($warga['pekerjaan'] ?? '-'); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?php echo htmlspecialchars(($warga['nama_rt'] ?? '-') . '/' . ($warga['nama_rw'] ?? '-')); ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <?php if (isset($warga['kk_id']) && $warga['kk_id'] && isset($warga['no_kk'])): ?>
                                <span class="text-blue-600"><?php echo htmlspecialchars($warga['no_kk']); ?></span>
                            <?php else: ?>
                                <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <?php if ($has_status_approval && $status_approval == 'menunggu'): ?>
                                <div class="flex space-x-2">
                                    <form method="POST" class="inline" onsubmit="return confirm('Terima warga ini?')">
                                        <input type="hidden" name="warga_id" value="<?php echo $warga['id']; ?>">
                                        <button type="submit" name="approve_warga" class="text-green-600 hover:text-green-800 transition-colors bg-green-50 px-2 py-1 rounded">
                                            <i class="fas fa-check mr-1"></i>Accept
                                        </button>
                                    </form>
                                    <form method="POST" class="inline" onsubmit="return confirm('Tolak warga ini?')">
                                        <input type="hidden" name="warga_id" value="<?php echo $warga['id']; ?>">
                                        <button type="submit" name="reject_warga" class="text-red-600 hover:text-red-800 transition-colors bg-red-50 px-2 py-1 rounded">
                                            <i class="fas fa-times mr-1"></i>Deny
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <button onclick="openEditModal(<?php echo $warga['id']; ?>, '<?php echo addslashes($warga['nik'] ?? ''); ?>', '<?php echo addslashes($warga['nama'] ?? ''); ?>', '<?php echo $warga['jk'] ?? 'L'; ?>', '<?php echo addslashes($warga['alamat'] ?? ''); ?>', '<?php echo addslashes($warga['tempat_lahir'] ?? ''); ?>', '<?php echo addslashes($warga['goldar'] ?? ''); ?>', '<?php echo addslashes($warga['agama'] ?? ''); ?>', '<?php echo addslashes($warga['status_kawin'] ?? ''); ?>', '<?php echo addslashes($warga['pekerjaan'] ?? ''); ?>', <?php echo $warga['rt'] ?? 'null'; ?>, <?php echo $warga['rw'] ?? 'null'; ?>)" class="text-blue-600 hover:text-blue-800 mr-3 transition-colors">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </button>
                                <form method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus warga ini?')">
                                    <input type="hidden" name="id" value="<?php echo $warga['id']; ?>">
                                    <button type="submit" name="delete_warga" class="text-red-600 hover:text-red-800 transition-colors">
                                        <i class="fas fa-trash mr-1"></i>Hapus
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="mt-6 flex justify-center">
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?php echo $i == $page ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-50'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </nav>
    </div>
    <?php endif; ?>
</div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-10 mx-auto p-4 border w-full max-w-2xl shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Tambah Warga Baru</h3>
                <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- NIK & Nama -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">NIK</label>
                        <input type="text" name="nik" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" name="nama" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Jenis Kelamin & Tanggal Lahir -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                        <select name="jk" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Tempat Lahir -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Jakarta">
                    </div>

                    <!-- Golongan Darah -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Golongan Darah</label>
                        <select name="goldar" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="AB">AB</option>
                            <option value="O">O</option>
                        </select>
                    </div>

                    <!-- Agama -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Agama</label>
                        <select name="agama" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih</option>
                            <option value="Islam">Islam</option>
                            <option value="Kristen">Kristen</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Budha">Budha</option>
                            <option value="Konghucu">Konghucu</option>
                        </select>
                    </div>

                    <!-- Status Kawin -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status Perkawinan</label>
                        <select name="status_kawin" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih</option>
                            <option value="Belum Kawin">Belum Kawin</option>
                            <option value="Kawin">Kawin</option>
                            <option value="Cerai Hidup">Cerai Hidup</option>
                            <option value="Cerai Mati">Cerai Mati</option>
                        </select>
                    </div>

                    <!-- Pekerjaan -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Pekerjaan</label>
                        <input type="text" name="pekerjaan" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Pedagang, PNS, Karyawan, Pelajar, dll">
                    </div>

                    <!-- Alamat -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Alamat</label>
                        <textarea name="alamat" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    <!-- RT & RW -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">RT</label>
                        <select name="rt" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <?php mysqli_data_seek($rt_result, 0); while ($rt = mysqli_fetch_assoc($rt_result)): ?>
                                <option value="<?php echo $rt['id']; ?>"><?php echo $rt['nama_rt']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">RW</label>
                        <select name="rw" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <?php mysqli_data_seek($rw_result, 0); while ($rw = mysqli_fetch_assoc($rw_result)): ?>
                                <option value="<?php echo $rw['id']; ?>"><?php echo $rw['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- KK -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Kartu Keluarga (Opsional)</label>
                        <select name="kk_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih KK</option>
                            <?php mysqli_data_seek($kk_result, 0); while ($kk = mysqli_fetch_assoc($kk_result)): ?>
                                <option value="<?php echo $kk['id']; ?>"><?php echo $kk['kepala_keluaraga']; ?> (<?php echo $kk['no_kk']; ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Batal</button>
                    <button type="submit" name="add_warga" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-10 mx-auto p-4 border w-full max-w-2xl shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit Warga</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="id" id="edit_id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- NIK & Nama -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">NIK</label>
                        <input type="text" name="nik" id="edit_nik" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" name="nama" id="edit_nama" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Jenis Kelamin & Tanggal Lahir -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                        <select name="jk" id="edit_jk" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Tempat Lahir -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" id="edit_tempat_lahir" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Golongan Darah -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Golongan Darah</label>
                        <select name="goldar" id="edit_goldar" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="AB">AB</option>
                            <option value="O">O</option>
                        </select>
                    </div>

                    <!-- Agama -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Agama</label>
                        <select name="agama" id="edit_agama" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih</option>
                            <option value="Islam">Islam</option>
                            <option value="Kristen">Kristen</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Budha">Budha</option>
                            <option value="Konghucu">Konghucu</option>
                        </select>
                    </div>

                    <!-- Status Kawin -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status Perkawinan</label>
                        <select name="status_kawin" id="edit_status_kawin" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih</option>
                            <option value="Belum Kawin">Belum Kawin</option>
                            <option value="Kawin">Kawin</option>
                            <option value="Cerai Hidup">Cerai Hidup</option>
                            <option value="Cerai Mati">Cerai Mati</option>
                        </select>
                    </div>

                    <!-- Pekerjaan -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Pekerjaan</label>
                        <input type="text" name="pekerjaan" id="edit_pekerjaan" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Alamat -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Alamat</label>
                        <textarea name="alamat" id="edit_alamat" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    <!-- RT & RW -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">RT</label>
                        <select name="rt" id="edit_rt" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <?php mysqli_data_seek($rt_result, 0); while ($rt = mysqli_fetch_assoc($rt_result)): ?>
                                <option value="<?php echo $rt['id']; ?>"><?php echo $rt['nama_rt']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">RW</label>
                        <select name="rw" id="edit_rw" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <?php mysqli_data_seek($rw_result, 0); while ($rw = mysqli_fetch_assoc($rw_result)): ?>
                                <option value="<?php echo $rw['id']; ?>"><?php echo $rw['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Batal</button>
                    <button type="submit" name="edit_warga" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('addModal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
}

function openEditModal(id, nik, nama, jk, alamat, tempat_lahir, goldar, agama, status_kawin, pekerjaan, rt, rw) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nik').value = nik || '';
    document.getElementById('edit_nama').value = nama || '';
    document.getElementById('edit_jk').value = jk || 'L';
    document.getElementById('edit_alamat').value = alamat || '';
    document.getElementById('edit_tempat_lahir').value = tempat_lahir || '';
    document.getElementById('edit_goldar').value = goldar || '';
    document.getElementById('edit_agama').value = agama || '';
    document.getElementById('edit_status_kawin').value = status_kawin || '';
    document.getElementById('edit_pekerjaan').value = pekerjaan || '';
    if (rt) document.getElementById('edit_rt').value = rt;
    if (rw) document.getElementById('edit_rw').value = rw;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>
