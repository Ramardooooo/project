<!-- Account Tab -->
                <div id="account-content" class="tab-content hidden">
                    <div class="space-y-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Keamanan Akun</h2>

                        <!-- Password Change -->
                        <div class="bg-gray-50/50 rounded-2xl p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Ubah Kata Sandi</h3>
                            <form class="space-y-4">
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi Saat Ini</label>
                                        <input type="password" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi Baru</label>
                                        <input type="password" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Kata Sandi Baru</label>
                                    <input type="password" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-xl hover:from-green-600 hover:to-green-700 transition-all duration-300">
                                    <i class="fas fa-key mr-2"></i>Perbarui Kata Sandi
                                </button>
                            </form>
                        </div>

                        <!-- Account Actions -->
                        <div class="bg-red-50/50 rounded-2xl p-6 border border-red-200">
                            <h3 class="text-lg font-semibold text-red-800 mb-4">Zona Bahaya</h3>
                            <p class="text-red-700 mb-4">Tindakan ini tidak dapat dibatalkan. Pastikan Anda yakin.</p>
                            <div class="space-y-3">
                                <button class="px-6 py-3 bg-red-500 text-white font-semibold rounded-xl hover:bg-red-600 transition-all duration-300">
                                    <i class="fas fa-trash mr-2"></i>Hapus Akun
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
