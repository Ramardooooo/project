<div id="mainContent" class="ml-64 p-8 bg-white min-h-screen transition-all duration-300">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Manajemen Kartu Keluarga</h1>
        <button onclick="openAddModal()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
            <i class="fas fa-plus mr-2"></i>Tambah KK
        </button>
    </div>

    <!-- Pending Approvals Alert -->
    <?php if ($has_status_approval && $pending_count > 0): ?>
    <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    Ada <span class="font-bold"><?php echo $pending_count; ?></span> data KK yang menunggu persetujuan.
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Search -->
    <div class="mb-6">
        <form method="GET" class="flex gap-4">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari nama kepala keluarga atau nomor KK..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            <button type="submit" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
        </form>
    </div>

    <!-- KK Table -->
    <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100/50">
                    <tr>
                        <?php if ($has_status_approval): ?>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <?php endif; ?>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">No. KK</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Kepala Keluarga</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Jumlah Anggota</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100/50">
                    <?php while ($kk = mysqli_fetch_assoc($kk_result)): ?>
                    <?php 
                        $status_approval = $has_status_approval ? ($kk['status_approval'] ?? null) : null;
                        
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
                        <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($kk['no_kk'] ?? ''); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($kk['kepala_keluaraga'] ?? ''); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-500"><?php echo $kk['anggota_count'] ?? 0; ?> orang</td>
                        <td class="px-6 py-4 text-sm">
                            <?php if ($has_status_approval && $status_approval == 'menunggu'): ?>
                                <div class="flex space-x-2">
                                    <form method="POST" class="inline" onsubmit="return confirm('Terima KK ini?')">
                                        <input type="hidden" name="kk_id" value="<?php echo $kk['id']; ?>">
                                        <button type="submit" name="approve_kk" class="text-green-600 hover:text-green-800 transition-colors bg-green-50 px-2 py-1 rounded">
                                            <i class="fas fa-check mr-1"></i>Accept
                                        </button>
                                    </form>
                                    <form method="POST" class="inline" onsubmit="return confirm('Tolak KK ini?')">
                                        <input type="hidden" name="kk_id" value="<?php echo $kk['id']; ?>">
                                        <button type="submit" name="reject_kk" class="text-red-600 hover:text-red-800 transition-colors bg-red-50 px-2 py-1 rounded">
                                            <i class="fas fa-times mr-1"></i>Deny
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <button onclick="openEditModal(<?php echo $kk['id']; ?>, '<?php echo addslashes($kk['kepala_keluaraga'] ?? ''); ?>', '<?php echo addslashes($kk['no_kk'] ?? ''); ?>')" class="text-blue-600 hover:text-blue-800 mr-3 transition-colors">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </button>
                                <form method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus KK ini?')">
                                    <input type="hidden" name="id" value="<?php echo $kk['id']; ?>">
                                    <button type="submit" name="delete_kk" class="text-red-600 hover:text-red-800 transition-colors">
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
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?php echo $i == $page ? 'text-green-600 bg-green-50' : 'text-gray-700 hover:bg-gray-50'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </nav>
    </div>
    <?php endif; ?>
</div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Tambah Kartu Keluarga Baru</h3>
                <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nomor KK</label>
                    <input type="text" name="no_kk" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kepala Keluarga</label>
                    <input type="text" name="kepala_keluaraga" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Batal</button>
                    <button type="submit" name="add_kk" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit Kartu Keluarga</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="id" id="edit_id">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nomor KK</label>
                    <input type="text" name="no_kk" id="edit_no_kk" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kepala Keluarga</label>
                    <input type="text" name="kepala_keluaraga" id="edit_kepala_keluaraga" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Batal</button>
                    <button type="submit" name="edit_kk" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Members Modal -->
<div id="membersModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-10 mx-auto p-4 border w-full max-w-lg shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Anggota Keluarga</h3>
                <button onclick="closeMembersModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="membersContent">
                <!-- Content will be loaded here -->
            </div>
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

function openEditModal(id, kepala_keluaraga, no_kk) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_kepala_keluaraga').value = kepala_keluaraga || '';
    document.getElementById('edit_no_kk').value = no_kk || '';
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

function viewMembers(kkId) {
    fetch(`get_kk_members.php?kk_id=${kkId}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('membersContent').innerHTML = data;
            document.getElementById('membersModal').classList.remove('hidden');
        });
}

function closeMembersModal() {
    document.getElementById('membersModal').classList.add('hidden');
}
</script>
