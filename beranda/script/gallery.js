function openModal(imagePath, title, description, date) {
    const modal = document.getElementById('gallery-modal');
    const modalImage = document.getElementById('modal-image');
    const modalTitle = document.getElementById('modal-title');
    const modalDescription = document.getElementById('modal-description');
    const modalDate = document.getElementById('modal-date');

    modalImage.src = 'beranda/gallery/' + imagePath;
    modalTitle.textContent = title;
    modalDescription.textContent = description;
    modalDate.innerHTML = '<i class="fas fa-calendar-alt mr-2"></i>' + date;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const modal = document.getElementById('gallery-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
}

document.getElementById('gallery-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.like-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const galleryId = this.getAttribute('data-gallery-id');
            const likeCount = this.querySelector('.like-count');

            fetch('api/toggle_like.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'gallery_id=' + galleryId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    likeCount.textContent = data.like_count;
                    this.classList.toggle('text-red-500', data.user_liked);
                    this.classList.toggle('text-gray-400', !data.user_liked);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
