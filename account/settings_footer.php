        <!-- Back to Home -->
        <div class="text-center mt-8">
            <a href="home" class="inline-flex items-center px-6 py-3 bg-white/80 backdrop-blur-sm text-gray-700 font-semibold rounded-xl hover:bg-white hover:shadow-lg transition-all duration-300 border border-gray-200 hover:border-gray-300">
                <i class="fas fa-arrow-left mr-3"></i>
                Kembali ke Beranda
            </a>
        </div>
    </div>

    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.5s ease-out;
        }
    </style>

    <script>
        // Tab switching functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons
                tabButtons.forEach(btn => {
                    btn.classList.remove('active', 'text-blue-600', 'border-b-2', 'border-blue-600');
                    btn.classList.add('text-gray-500');
                });

                // Add active class to clicked button
                button.classList.add('active', 'text-blue-600', 'border-b-2', 'border-blue-600');
                button.classList.remove('text-gray-500');

                // Hide all tab contents
                tabContents.forEach(content => content.classList.add('hidden'));

                // Show corresponding tab content
                const tabId = button.id.replace('-tab', '-content');
                document.getElementById(tabId).classList.remove('hidden');
            });
        });

        // Preview profile photo before upload
        document.getElementById('profile_photo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file harus kurang dari 5MB');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    // Update both profile images
                    document.querySelectorAll('img[alt="Profile Photo"]').forEach(img => {
                        img.src = e.target.result;
                    });
                };
                reader.readAsDataURL(file);
            }
        });

        // Add loading animation to form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
            button.disabled = true;

            // Re-enable after 3 seconds (in case of slow response)
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            }, 3000);
        });
    </script>
</body>
</html>
