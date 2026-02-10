<div class="ml-64 p-8 bg-white min-h-screen">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Manajemen Data Warga</h1>
        <button onclick="openAddModal()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            <i class="fas fa-plus mr-2"></i>Tambah Warga
        </button>
    </div>

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
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">NIK</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Nama</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">JK</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">RT/RW</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100/50">
                    <?php while ($warga = mysqli_fetch_assoc($warga_result)): ?>
                    <tr class="hover:bg-gray-50/50 transition-colors duration-200">
                        <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($warga['nik']); ?></td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($warga['nama']); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?php echo $warga['jk'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($warga['nama_rt'] . '/' . $warga['nama_rw']); ?></td>
                        <td class="px-6 py-4 text-sm">
                            <button onclick="openEditModal(<?php echo $warga['id']; ?>, '<?php echo addslashes($warga['nik']); ?>', '<?php echo addslashes($warga['nama']); ?>', '<?php echo $warga['jk']; ?>', '<?php echo addslashes($warga['alamat']); ?>', <?php echo $warga['rt']; ?>, <?php echo $warga['rw']; ?>)" class="text-blue-600 hover:text-blue-800 mr-3 transition-colors">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            <form method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus warga ini?')">
                                <input type="hidden" name="id" value="<?php echo $warga['id']; ?>">
                                <button type="submit" name="delete_warga" class="text-red-600 hover:text-red-800 transition-colors">
                                    <i class="fas fa-trash mr-1"></i>Hapus
                                </button>
                            </form>
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
<div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" id="my-modal">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Tambah Warga Baru</h3>
                <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">NIK</label>
                        <input type="text" name="nik" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" name="nama" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                        <select name="jk" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Alamat</label>
                        <textarea name="alamat" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
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
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
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
                    <div>
                        <label class="block text-sm font-medium text-gray-700">NIK</label>
                        <input type="text" name="nik" id="edit_nik" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" name="nama" id="edit_nama" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                        <select name="jk" id="edit_jk" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Alamat</label>
                        <textarea name="alamat" id="edit_alamat" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
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

function openEditModal(id, nik, nama, jk, alamat, rt, rw) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nik').value = nik;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_jk').value = jk;
    document.getElementById('edit_alamat').value = alamat;
    document.getElementById('edit_rt').value = rt;
    document.getElementById('edit_rw').value = rw;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>


