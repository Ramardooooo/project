<div id="mainContent" class="ml-64 p-8 bg-white min-h-screen transition-all duration-300">
    <h1 class="text-3xl font-bold mb-8 text-gray-800">Mutasi Warga</h1>

    <!-- Mutasi Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <button onclick="openMutasiModal('datang')" class="bg-gradient-to-r from-green-400 to-green-600 text-white p-6 rounded-xl hover:from-green-500 hover:to-green-700 hover:scale-105 hover:shadow-xl transition-all duration-300 transform">
            <i class="fas fa-plus-circle text-3xl mb-3"></i>
            <h3 class="text-lg font-semibold">Warga Datang</h3>
            <p class="text-sm opacity-90">Pencatatan warga yang datang</p>
        </button>

        <button onclick="openMutasiModal('pindah')" class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white p-6 rounded-xl hover:from-yellow-500 hover:to-orange-600 hover:scale-105 hover:shadow-xl transition-all duration-300 transform">
            <i class="fas fa-arrow-right text-3xl mb-3"></i>
            <h3 class="text-lg font-semibold">Warga Pindah</h3>
            <p class="text-sm opacity-90">Pencatatan warga yang pindah</p>
        </button>

        <button onclick="openMutasiModal('meninggal')" class="bg-gradient-to-r from-red-400 to-red-600 text-white p-6 rounded-xl hover:from-red-500 hover:to-red-700 hover:scale-105 hover:shadow-xl transition-all duration-300 transform">
            <i class="fas fa-cross text-3xl mb-3"></i>
            <h3 class="text-lg font-semibold">Warga Meninggal</h3>
            <p class="text-sm opacity-90">Pencatatan warga yang meninggal</p>
        </button>
    </div>

    <!-- Mutasi History -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Riwayat Mutasi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">NIK</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">RT/RW</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php while ($mutasi = mysqli_fetch_assoc($mutasi_result)): ?>
                    <tr class="hover:bg-gradient-to-r hover:from-gray-50 hover:to-blue-50 transition-colors duration-200">
                        <td class="px-6 py-4 text-sm text-gray-900"><?php echo date('d/m/Y', strtotime($mutasi['tanggal_mutasi'])); ?></td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo htmlspecialchars((string) ($mutasi['nama'] ?? '')); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars((string) $mutasi['nik']); ?></td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-medium rounded-full <?php switch($mutasi['jenis_mutasi']) { case 'datang': echo 'bg-green-100 text-green-800 border border-green-200'; break; case 'pindah': echo 'bg-yellow-100 text-yellow-800 border border-yellow-200'; break; case 'meninggal': echo 'bg-red-100 text-red-800 border border-red-200'; break; } ?>">
                                <?php echo ucfirst($mutasi['jenis_mutasi']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars((string) ($mutasi['nama_rt'] . '/' . $mutasi['nama_rw'])); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <?php $k = $mutasi['keterangan']; if ($mutasi['jenis_mutasi'] == 'pindah' && $mutasi['alamat_tujuan']) $k .= ' - ' . $mutasi['alamat_tujuan']; echo htmlspecialchars((string) $k); ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Mutasi Modal -->
<div id="mutasiModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
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
                            <option value="<?php echo $warga['id']; ?>"><?php echo htmlspecialchars((string) ($warga['nama'] . ' - ' . $warga['nik'] . ' (' . $warga['nama_rt'] . '/' . $warga['nama_rw'] . ')')); ?></option>
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
    const modal = document.getElementById('mutasiModal');

    switch(jenis) {
        case 'datang':
            modalTitle.textContent = 'Pencatatan Warga Datang';
            submitBtn.textContent = 'Catat Datang';
            submitBtn.className = 'px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200';
            alamatTujuanField.style.display = 'none';
            break;
        case 'pindah':
            modalTitle.textContent = 'Pencatatan Warga Pindah';
            submitBtn.textContent = 'Catat Pindah';
            submitBtn.className = 'px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-lg hover:from-yellow-600 hover:to-orange-600 transition-all duration-200';
            alamatTujuanField.style.display = 'block';
            break;
        case 'meninggal':
            modalTitle.textContent = 'Pencatatan Warga Meninggal';
            submitBtn.textContent = 'Catat Meninggal';
            submitBtn.className = 'px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200';
            alamatTujuanField.style.display = 'none';
            break;
    }

    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.add('opacity-100');
        modal.querySelector('div').classList.add('scale-100');
        modal.querySelector('div').classList.remove('scale-95');
    }, 10);
}

function closeMutasiModal() {
    const modal = document.getElementById('mutasiModal');
    const modalContent = modal.querySelector('div');

    modal.classList.remove('opacity-100');
    modalContent.classList.remove('scale-100');
    modalContent.classList.add('scale-95');

    setTimeout(() => {
        modal.classList.add('hidden');
        document.getElementById('mutasiForm').reset();
    }, 300);
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
