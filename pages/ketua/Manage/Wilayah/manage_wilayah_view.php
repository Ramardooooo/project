<div class="ml-64 p-8 bg-white min-h-screen">
    <h1 class="text-3xl font-bold mb-8 text-gray-800">Manajemen Wilayah</h1>

    <div class="mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Data RT</h2>
            <button onclick="openAddRTModal()" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
                <i class="fas fa-plus mr-2"></i>Tambah RT
            </button>
        </div>

        <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Nama RT</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Ketua RT</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">RW</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Jumlah Warga</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100/50">
                        <?php while ($rt = mysqli_fetch_assoc($rt_result)): ?>
                        <tr class="hover:bg-gray-50/50 transition-colors duration-200">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($rt['nama_rt']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($rt['ketua_rt']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($rt['nama_rw'] ?? ''); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo $rt['jumlah_warga']; ?> orang</td>
                            <td class="px-6 py-4 text-sm">
                                <button onclick="openEditRTModal(<?php echo $rt['id']; ?>, '<?php echo addslashes($rt['nama_rt']); ?>', '<?php echo addslashes($rt['ketua_rt']); ?>', <?php echo $rt['id_rw']; ?>)" class="text-blue-600 hover:text-blue-800 mr-3 transition-colors">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </button>
                                <form method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus RT ini?')">
                                    <input type="hidden" name="id" value="<?php echo $rt['id']; ?>">
                                    <button type="submit" name="delete_rt" class="text-red-600 hover:text-red-800 transition-colors">
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
    </div>

    <div class="mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Data RW</h2>
            <button onclick="openAddRWModal()" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">
                <i class="fas fa-plus mr-2"></i>Tambah RW
            </button>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama RW</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah RT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Warga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php mysqli_data_seek($rw_result, 0); while ($rw = mysqli_fetch_assoc($rw_result)): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($rw['name']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $rw['jumlah_rt']; ?> RT</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $rw['total_warga'] ?? 0; ?> orang</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="openEditRWModal(<?php echo $rw['id']; ?>, '<?php echo addslashes($rw['name']); ?>')" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <form method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus RW ini?')">
                                <input type="hidden" name="id" value="<?php echo $rw['id']; ?>">
                                <button type="submit" name="delete_rw" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="addRTModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Tambah RT Baru</h3>
                <button onclick="closeAddRTModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama RT</label>
                    <input type="text" name="nama_rt" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Ketua RT</label>
                    <input type="text" name="ketua_rt" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">RW</label>
                    <select name="id_rw" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        <?php mysqli_data_seek($rw_result, 0); while ($rw = mysqli_fetch_assoc($rw_result)): ?>
                            <option value="<?php echo $rw['id']; ?>"><?php echo $rw['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeAddRTModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Batal</button>
                    <button type="submit" name="add_rt" class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="editRTModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit RT</h3>
                <button onclick="closeEditRTModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="id" id="edit_rt_id">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama RT</label>
                    <input type="text" name="nama_rt" id="edit_rt_nama" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Ketua RT</label>
                    <input type="text" name="ketua_rt" id="edit_rt_ketua" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">RW</label>
                    <select name="id_rw" id="edit_rt_rw" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        <?php mysqli_data_seek($rw_result, 0); while ($rw = mysqli_fetch_assoc($rw_result)): ?>
                            <option value="<?php echo $rw['id']; ?>"><?php echo $rw['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeEditRTModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Batal</button>
                    <button type="submit" name="edit_rt" class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add RW Modal -->
<div id="addRWModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Tambah RW Baru</h3>
                <button onclick="closeAddRWModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama RW</label>
                    <input type="text" name="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeAddRWModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Batal</button>
                    <button type="submit" name="add_rw" class="px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit RW Modal -->
<div id="editRWModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit RW</h3>
                <button onclick="closeEditRWModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="id" id="edit_rw_id">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama RW</label>
                    <input type="text" name="name" id="edit_rw_name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeEditRWModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Batal</button>
                    <button type="submit" name="edit_rw" class="px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddRTModal() {
    document.getElementById('addRTModal').classList.remove('hidden');
}

function closeAddRTModal() {
    document.getElementById('addRTModal').classList.add('hidden');
}

function openEditRTModal(id, nama_rt, ketua_rt, id_rw) {
    document.getElementById('edit_rt_id').value = id;
    document.getElementById('edit_rt_nama').value = nama_rt;
    document.getElementById('edit_rt_ketua').value = ketua_rt;
    document.getElementById('edit_rt_rw').value = id_rw;
    document.getElementById('editRTModal').classList.remove('hidden');
}

function closeEditRTModal() {
    document.getElementById('editRTModal').classList.add('hidden');
}

function openAddRWModal() {
    document.getElementById('addRWModal').classList.remove('hidden');
}

function closeAddRWModal() {
    document.getElementById('addRWModal').classList.add('hidden');
}

function openEditRWModal(id, name) {
    document.getElementById('edit_rw_id').value = id;
    document.getElementById('edit_rw_name').value = name;
    document.getElementById('editRWModal').classList.remove('hidden');
}

function closeEditRWModal() {
    document.getElementById('editRWModal').classList.add('hidden');
}
</script>
