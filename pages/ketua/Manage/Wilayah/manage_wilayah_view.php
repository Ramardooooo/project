<div id="mainContent" class="ml-64 p-8 bg-white min-h-screen transition-all duration-300">
    <h1 class="text-3xl font-bold mb-8 text-gray-800">Manajemen Wilayah</h1>

    <!-- Tabs -->
    <div class="mb-8">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button type="button" id="rt-tab" onclick="showTab('rt')" class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm border-purple-500 text-purple-600">
                    Data RT
                </button>
                <button type="button" id="rw-tab" onclick="showTab('rw')" class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Data RW
                </button>
            </nav>
        </div>
    </div>

    <!-- RT Tab Content -->
    <div id="rt-content" class="tab-content">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Data RT</h2>
            <button onclick="openAddRTModal()" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
                <i class="fas fa-plus mr-2"></i>Tambah RT
            </button>
        </div>

        <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden">
            <table class="w-full table-auto">
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
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
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

            <!-- RT Pagination -->
            <?php if ($rt_total_pages > 1): ?>
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    <?php if ($rt_page > 1): ?>
                        <a href="?<?php echo isset($_GET['rw_page']) ? 'rw_page=' . $_GET['rw_page'] . '&' : ''; ?>rt_page=<?php echo $rt_page - 1; ?>" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Previous
                        </a>
                    <?php endif; ?>
                    <?php if ($rt_page < $rt_total_pages): ?>
                        <a href="?<?php echo isset($_GET['rw_page']) ? 'rw_page=' . $_GET['rw_page'] . '&' : ''; ?>rt_page=<?php echo $rt_page + 1; ?>" class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Next
                        </a>
                    <?php endif; ?>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium"><?php echo ($rt_page - 1) * $items_per_page + 1; ?></span> to <span class="font-medium"><?php echo min($rt_page * $items_per_page, $rt_total); ?></span> of <span class="font-medium"><?php echo $rt_total; ?></span> results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                            <?php if ($rt_page > 1): ?>
                                <a href="?<?php echo isset($_GET['rw_page']) ? 'rw_page=1&' : ''; ?>rt_page=1" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                                <a href="?<?php echo isset($_GET['rw_page']) ? 'rw_page=' . $_GET['rw_page'] . '&' : ''; ?>rt_page=<?php echo $rt_page - 1; ?>" class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <i class="fas fa-angle-left"></i>
                                </a>
                            <?php endif; ?>

                            <?php for ($i = max(1, $rt_page - 2); $i <= min($rt_total_pages, $rt_page + 2); $i++): ?>
                                <a href="?<?php echo isset($_GET['rw_page']) ? 'rw_page=' . $_GET['rw_page'] . '&' : ''; ?>rt_page=<?php echo $i; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?php echo $i == $rt_page ? 'text-purple-600 bg-purple-50' : 'text-gray-700 hover:bg-gray-50'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($rt_page < $rt_total_pages): ?>
                                <a href="?<?php echo isset($_GET['rw_page']) ? 'rw_page=' . $_GET['rw_page'] . '&' : ''; ?>rt_page=<?php echo $rt_page + 1; ?>" class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                                <a href="?<?php echo isset($_GET['rw_page']) ? 'rw_page=' . $_GET['rw_page'] . '&' : ''; ?>rt_page=<?php echo $rt_total_pages; ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <i class="fas fa-angle-double-right"></i>
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- RW Tab Content -->
    <div id="rw-content" class="tab-content hidden">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Data RW</h2>
            <button onclick="openAddRWModal()" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">
                <i class="fas fa-plus mr-2"></i>Tambah RW
            </button>
        </div>

        <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Nama RW</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Jumlah RT</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Total Warga</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100/50">
                    <?php while ($rw = mysqli_fetch_assoc($rw_result)): ?>
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($rw['name']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $rw['jumlah_rt']; ?> RT</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $rw['total_warga'] ?? 0; ?> orang</td>
                        <td class="px-6 py-4 text-sm">
                            <button onclick="openEditRWModal(<?php echo $rw['id']; ?>, '<?php echo addslashes($rw['name']); ?>')" class="text-blue-600 hover:text-blue-800 mr-3 transition-colors">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            <form method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus RW ini?')">
                                <input type="hidden" name="id" value="<?php echo $rw['id']; ?>">
                                <button type="submit" name="delete_rw" class="text-red-600 hover:text-red-800 transition-colors">
                                    <i class="fas fa-trash mr-1"></i>Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- RW Pagination -->
            <?php if ($rw_total_pages > 1): ?>
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    <?php if ($rw_page > 1): ?>
                        <a href="?<?php echo isset($_GET['rt_page']) ? 'rt_page=' . $_GET['rt_page'] . '&' : ''; ?>rw_page=<?php echo $rw_page - 1; ?>" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Previous
                        </a>
                    <?php endif; ?>
                    <?php if ($rw_page < $rw_total_pages): ?>
                        <a href="?<?php echo isset($_GET['rt_page']) ? 'rt_page=' . $_GET['rt_page'] . '&' : ''; ?>rw_page=<?php echo $rw_page + 1; ?>" class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Next
                        </a>
                    <?php endif; ?>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium"><?php echo ($rw_page - 1) * $items_per_page + 1; ?></span> to <span class="font-medium"><?php echo min($rw_page * $items_per_page, $rw_total); ?></span> of <span class="font-medium"><?php echo $rw_total; ?></span> results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                            <?php if ($rw_page > 1): ?>
                                <a href="?<?php echo isset($_GET['rt_page']) ? 'rt_page=1&' : ''; ?>rw_page=1" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                                <a href="?<?php echo isset($_GET['rt_page']) ? 'rt_page=' . $_GET['rt_page'] . '&' : ''; ?>rw_page=<?php echo $rw_page - 1; ?>" class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <i class="fas fa-angle-left"></i>
                                </a>
                            <?php endif; ?>

                            <?php for ($i = max(1, $rw_page - 2); $i <= min($rw_total_pages, $rw_page + 2); $i++): ?>
                                <a href="?<?php echo isset($_GET['rt_page']) ? 'rt_page=' . $_GET['rt_page'] . '&' : ''; ?>rw_page=<?php echo $i; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?php echo $i == $rw_page ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($rw_page < $rw_total_pages): ?>
                                <a href="?<?php echo isset($_GET['rt_page']) ? 'rt_page=' . $_GET['rt_page'] . '&' : ''; ?>rw_page=<?php echo $rw_page + 1; ?>" class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                                <a href="?<?php echo isset($_GET['rt_page']) ? 'rt_page=' . $_GET['rt_page'] . '&' : ''; ?>rw_page=<?php echo $rw_total_pages; ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <i class="fas fa-angle-double-right"></i>
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>
            </div>
            <?php endif; ?>
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
function showTab(tab) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-purple-500', 'text-purple-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab content
    document.getElementById(tab + '-content').classList.remove('hidden');

    // Add active class to selected tab
    document.getElementById(tab + '-tab').classList.remove('border-transparent', 'text-gray-500');
    document.getElementById(tab + '-tab').classList.add('border-purple-500', 'text-purple-600');
}

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

// Initialize default tab
document.addEventListener('DOMContentLoaded', function() {
    showTab('rt');
});
</script>
