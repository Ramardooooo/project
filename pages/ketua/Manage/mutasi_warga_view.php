<div class="ml-64 p-8 bg-white min-h-screen">
    <h1 class="text-3xl font-bold mb-8 text-gray-800">Mutasi Warga</h1>

    <!-- Mutasi Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <button onclick="openMutasiModal('datang')" class="bg-green-500 text-white p-6 rounded-lg hover:bg-green-600 transition-colors">
            <i class="fas fa-plus-circle text-3xl mb-3"></i>
            <h3 class="text-lg font-semibold">Warga Datang</h3>
            <p class="text-sm opacity-90">Pencatatan warga yang datang</p>
        </button>

        <button onclick="openMutasiModal('pindah')" class="bg-yellow-500 text-white p-6 rounded-lg hover:bg-yellow-600 transition-colors">
            <i class="fas fa-arrow-right text-3xl mb-3"></i>
            <h3 class="text-lg font-semibold">Warga Pindah</h3>
            <p class="text-sm opacity-90">Pencatatan warga yang pindah</p>
        </button>

        <button onclick="openMutasiModal('meninggal')" class="bg-red-500 text-white p-6 rounded-lg hover:bg-red-600 transition-colors">
            <i class="fas fa-cross text-3xl mb-3"></i>
            <h3 class="text-lg font-semibold">Warga Meninggal</h3>
            <p class="text-sm opacity-90">Pencatatan warga yang meninggal</p>
        </button>
    </div>

    <!-- Mutasi History -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
<div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h3 class="text-lg font-medium text-gray-900">Riwayat Mutasi (<?php echo number_format($total_mutasi ?? 0); ?> total)</h3>
            <!-- Search Form -->
            <form method="GET" class="flex gap-3 flex-1 max-w-md">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search ?? ''); ?>" placeholder="Cari nama, NIK, jenis mutasi, keterangan..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center whitespace-nowrap">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
                <?php if (!empty($search ?? '')): ?>
                <a href="?page=1" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 flex items-center whitespace-nowrap text-sm">
                    <i class="fas fa-times mr-1"></i>Clear
                </a>
                <?php endif; ?>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Tanggal</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Nama</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">NIK</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Jenis Mutasi</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">RT/RW</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php while ($mutasi = mysqli_fetch_assoc($mutasi_result)): ?>
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 text-sm text-gray-900"><?php echo date('d/m/Y', strtotime($mutasi['tanggal_mutasi'])); ?></td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($mutasi['nama']); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($mutasi['nik']); ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                <?php
                                switch($mutasi['jenis_mutasi']) {
                                    case 'datang': echo 'bg-green-100 text-green-800'; break;
                                    case 'pindah': echo 'bg-yellow-100 text-yellow-800'; break;
                                    case 'meninggal': echo 'bg-red-100 text-red-800'; break;
                                }
                                ?>">
                                <?php echo ucfirst($mutasi['jenis_mutasi']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($mutasi['nama_rt'] . '/' . $mutasi['nama_rw']); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <?php
                            $keterangan = $mutasi['keterangan'];
                            if ($mutasi['jenis_mutasi'] == 'pindah' && $mutasi['alamat_tujuan']) {
                                $keterangan .= ' - Tujuan: ' . $mutasi['alamat_tujuan'];
                            }
                            echo htmlspecialchars($keterangan);
                            ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if (($total_pages ?? 1) > 1): ?>
        <div class="px-6 py-6 bg-gray-50 border-t border-gray-200 flex items-center justify-center space-x-2 flex-wrap gap-1">
            <?php if (($page ?? 1) > 1): ?>
                <a href="?page=1<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="px-3 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center text-sm shadow-sm whitespace-nowrap">
                    <i class="fas fa-angle-double-left mr-1"></i>1
                </a>
                <a href="?page=<?php echo ($page - 1); ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="px-3 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center text-sm shadow-sm">
                    <i class="fas fa-chevron-left"></i>
                </a>
            <?php endif; ?>

            <?php 
            $start = max(1, ($page ?? 1) - 2);
            $end = min(($total_pages ?? 1), ($page ?? 1) + 2);
            for ($i = $start; $i <= $end; $i++): ?>
                <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="px-4 py-2 <?php echo $i == ($page ?? 1) ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'; ?> rounded-lg text-sm font-medium shadow-sm">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if (($page ?? 1) < ($total_pages ?? 1)): ?>
                <a href="?page=<?php echo ($page + 1); ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="px-3 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center text-sm shadow-sm">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a href="?page=<?php echo $total_pages ?? 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="px-3 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center text-sm shadow-sm whitespace-nowrap">
                    <?php echo $total_pages ?? 1; ?> <i class="fas fa-angle-double-right ml-1"></i>
                </a>
            <?php endif; ?>
            
            <span class="text-sm text-gray-500 px-3 py-2">Hal. <?php echo $page ?? 1; ?> dari <?php echo $total_pages ?? 1; ?></span>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Mutasi Modal -->
<div id="mutasiModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
<div class="flex items-center justify-center min-h-screen px-4 py-8"><div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto border border-gray-200">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Mutasi Warga</h3>
                <button onclick="closeMutasiModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4" id="mutasiForm">
                <input type="hidden" name="jenis_mutasi" id="jenis_mutasi">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Pilih Warga</label>
                    <select name="warga_id" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih warga</option>
                        <?php mysqli_data_seek($warga_result, 0); while ($warga = mysqli_fetch_assoc($warga_result)): ?>
                            <option value="<?php echo $warga['id']; ?>"><?php echo $warga['nama'] . ' - ' . $warga['nik'] . ' (' . $warga['nama_rt'] . '/' . $warga['nama_rw'] . ')'; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Mutasi</label>
                    <input type="date" name="tanggal_mutasi" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div id="alamatTujuanField" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700">Alamat Tujuan</label>
                    <input type="text" name="alamat_tujuan" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                    <textarea name="keterangan" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeMutasiModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Batal</button>
                    <button type="submit" id="submitBtn" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openMutasiModal(jenis) {
    document.getElementById('jenis_mutasi').value = jenis;
    document.getElementById('mutasiForm').action = '?action=' + jenis;

    const modalTitle = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('submitBtn');
    const alamatTujuanField = document.getElementById('alamatTujuanField');

    switch(jenis) {
        case 'datang':
            modalTitle.textContent = 'Pencatatan Warga Datang';
            submitBtn.textContent = 'Catat Datang';
            submitBtn.className = 'px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600';
            alamatTujuanField.style.display = 'none';
            break;
        case 'pindah':
            modalTitle.textContent = 'Pencatatan Warga Pindah';
            submitBtn.textContent = 'Catat Pindah';
            submitBtn.className = 'px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600';
            alamatTujuanField.style.display = 'block';
            break;
        case 'meninggal':
            modalTitle.textContent = 'Pencatatan Warga Meninggal';
            submitBtn.textContent = 'Catat Meninggal';
            submitBtn.className = 'px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600';
            alamatTujuanField.style.display = 'none';
            break;
    }

    document.getElementById('mutasiModal').classList.remove('hidden');
}

function closeMutasiModal() {
    document.getElementById('mutasiModal').classList.add('hidden');
    document.getElementById('mutasiForm').reset();
}

document.getElementById('mutasiForm').addEventListener('submit', function(e) {
    const jenis = document.getElementById('jenis_mutasi').value;

    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'mutasi_' + jenis;
    hiddenInput.value = '1';
    this.appendChild(hiddenInput);
});
</script>
