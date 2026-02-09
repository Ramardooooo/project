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
    <div class="bg-white border">
        <div class="px-4 py-2 bg-gray-50">
            <h3 class="text-lg font-medium">Riwayat Mutasi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm">Tanggal</th>
                        <th class="px-4 py-2 text-left text-sm">Nama</th>
                        <th class="px-4 py-2 text-left text-sm">NIK</th>
                        <th class="px-4 py-2 text-left text-sm">Jenis</th>
                        <th class="px-4 py-2 text-left text-sm">RT/RW</th>
                        <th class="px-4 py-2 text-left text-sm">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($mutasi = mysqli_fetch_assoc($mutasi_result)): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm"><?php echo date('d/m/Y', strtotime($mutasi['tanggal_mutasi'])); ?></td>
                        <td class="px-4 py-2 text-sm font-medium"><?php echo htmlspecialchars($mutasi['nama']); ?></td>
                        <td class="px-4 py-2 text-sm"><?php echo htmlspecialchars($mutasi['nik']); ?></td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded <?php switch($mutasi['jenis_mutasi']) { case 'datang': echo 'bg-green-100 text-green-800'; break; case 'pindah': echo 'bg-yellow-100 text-yellow-800'; break; case 'meninggal': echo 'bg-red-100 text-red-800'; break; } ?>">
                                <?php echo ucfirst($mutasi['jenis_mutasi']); ?>
                            </span>
                        </td>
                        <td class="px-4 py-2 text-sm"><?php echo htmlspecialchars($mutasi['nama_rt'] . '/' . $mutasi['nama_rw']); ?></td>
                        <td class="px-4 py-2 text-sm">
                            <?php $k = $mutasi['keterangan']; if ($mutasi['jenis_mutasi'] == 'pindah' && $mutasi['alamat_tujuan']) $k .= ' - ' . $mutasi['alamat_tujuan']; echo htmlspecialchars($k); ?>
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
