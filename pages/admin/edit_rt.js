document.getElementById('editRtForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;

    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
    submitBtn.disabled = true;

    fetch('edit_rt_process.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Data RT berhasil diperbarui!', 'success');
            setTimeout(() => {
                window.location.href = 'manage_rt_rw.php';
            }, 2000);
        } else {
            showNotification(data.message || 'Terjadi kesalahan saat menyimpan data.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat menyimpan data.', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

function showNotification(message, type = 'success') {
    const notification = document.getElementById('floating-notification');
    const icon = notification.querySelector('i');
    const title = notification.querySelector('h4');
    const text = notification.querySelector('p');

    if (type === 'success') {
        notification.className = notification.className.replace(/bg-\w+-500/, 'bg-green-500');
        icon.className = 'fas fa-check-circle mr-3 text-xl';
        title.textContent = 'Berhasil!';
    } else if (type === 'error') {
        notification.className = notification.className.replace(/bg-\w+-500/, 'bg-red-500');
        icon.className = 'fas fa-exclamation-circle mr-3 text-xl';
        title.textContent = 'Error!';
    }

    text.textContent = message;

    notification.classList.remove('translate-x-full');
    notification.classList.add('translate-x-0');

    setTimeout(() => {
        closeNotification();
    }, 5000);
}

function closeNotification() {
    const notification = document.getElementById('floating-notification');
    notification.classList.remove('translate-x-0');
    notification.classList.add('translate-x-full');
}
