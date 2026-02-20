const tabButtons = document.querySelectorAll('.tab-button');
const tabContents = document.querySelectorAll('.tab-content');

// Tab switching (if tabs are used)
if (tabButtons.length > 0) {
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'text-blue-600', 'border-b-2', 'border-blue-600');
                btn.classList.add('text-gray-500');
            });

            button.classList.add('active', 'text-blue-600', 'border-b-2', 'border-blue-600');
            button.classList.remove('text-gray-500');

            tabContents.forEach(content => content.classList.add('hidden'));

            const tabId = button.id.replace('-tab', '-content');
            const targetContent = document.getElementById(tabId);
            if (targetContent) {
                targetContent.classList.remove('hidden');
            }
        });
    });
}

// Profile photo preview
const profilePhotoInput = document.getElementById('profile_photo');
if (profilePhotoInput) {
    profilePhotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file harus kurang dari 5MB');
                this.value = '';
                return;
            }

            const allowed = ['jpg', 'jpeg', 'png', 'gif'];
            const ext = file.name.split('.').pop().toLowerCase();
            if (!allowed.includes(ext)) {
                alert('Format file harus JPG, PNG, atau GIF');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('profile-preview');
                if (previewImg) {
                    previewImg.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        }
    });
}

// Form submission loading state
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const button = this.querySelector('button[type="submit"]');
        if (button && !button.disabled) {
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
            button.disabled = true;

            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            }, 3000);
        }
    });
});
