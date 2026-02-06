<!-- Profile Tab -->
                <div id="profile-content" class="tab-content">
                    <div class="grid md:grid-cols-3 gap-8">
                        <!-- Profile Overview -->
                        <div class="md:col-span-1">
                            <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl p-6 text-center">
                                <div class="relative inline-block mb-4">
                                    <img src="<?php echo $user['profile_photo'] ? '../' . $user['profile_photo'] : 'https://via.placeholder.com/120/3B82F6/FFFFFF?text=' . strtoupper(substr($user['username'], 0, 1)); ?>"
                                         alt="Profile Photo"
                                         class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg">
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($user['username']); ?></h3>
                                <p class="text-gray-600 mb-4"><?php echo ucfirst($user['role']); ?></p>
                                <div class="space-y-2 text-sm text-gray-600">
                                    <div class="flex items-center justify-center">
                                        <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                                        Anggota selama <?php echo $stats['account_age']; ?> hari
                                    </div>
                                    <div class="flex items-center justify-center">
                                        <i class="fas fa-clock mr-2 text-purple-500"></i>
                                        Login terakhir: <?php echo $stats['last_login']; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Form -->
                        <div class="md:col-span-2">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Informasi Profil</h2>
                            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                                <!-- Profile Photo Section -->
                                <div class="flex items-center space-x-6">
                                    <div class="relative group">
                                        <img src="<?php echo $user['profile_photo'] ? '../' . $user['profile_photo'] : 'https://via.placeholder.com/80/3B82F6/FFFFFF?text=' . strtoupper(substr($user['username'], 0, 1)); ?>"
                                             alt="Foto Profil"
                                             class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-lg group-hover:scale-105 transition-transform duration-300">
                                        <label for="profile_photo" class="absolute inset-0 bg-black/50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 cursor-pointer">
                                            <i class="fas fa-camera text-white text-lg"></i>
                                        </label>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Foto Profil</h4>
                                        <p class="text-sm text-gray-600">Klik gambar untuk mengubah foto profil Anda</p>
                                        <input type="file" id="profile_photo" name="profile_photo" accept="image/*" class="hidden">
                                    </div>
                                </div>

                                <!-- Form Fields -->
                                <div class="grid md:grid-cols-2 gap-6">
                                    <!-- Username -->
                                    <div class="space-y-2">
                                        <label for="username" class="block text-sm font-semibold text-gray-700">
                                            <i class="fas fa-user mr-2 text-blue-500"></i>Username
                                        </label>
                                        <input type="text" id="username" name="username" required
                                               value="<?php echo htmlspecialchars($user['username']); ?>"
                                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50/50 backdrop-blur-sm transition-all duration-300 hover:bg-white hover:shadow-md">
                                    </div>

                                    <!-- Email -->
                                    <div class="space-y-2">
                                        <label for="email" class="block text-sm font-semibold text-gray-700">
                                            <i class="fas fa-envelope mr-2 text-purple-500"></i>Alamat Email
                                        </label>
                                        <input type="email" id="email" name="email" required
                                               value="<?php echo htmlspecialchars($user['email']); ?>"
                                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50/50 backdrop-blur-sm transition-all duration-300 hover:bg-white hover:shadow-md">
                                    </div>
                                </div>

                                <!-- Role (read-only) -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">
                                        <i class="fas fa-shield-alt mr-2 text-indigo-500"></i>Peran Akun
                                    </label>
                                    <input type="text" readonly
                                           value="<?php echo ucfirst($user['role']); ?>"
                                           class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gradient-to-r from-gray-100 to-gray-50 text-gray-600 cursor-not-allowed">
                                    <p class="text-xs text-gray-500 mt-1">Peran akun Anda tidak dapat diubah</p>
                                </div>

                                <!-- Submit Button -->
                                <div class="flex justify-end pt-4">
                                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-500 via-purple-600 to-indigo-600 text-white font-bold rounded-xl hover:from-blue-600 hover:via-purple-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                        <i class="fas fa-save mr-2"></i>
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
