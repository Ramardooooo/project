// Optimized Gallery Image Preview Modal with Like functionality
document.addEventListener('DOMContentLoaded', function() {
    const galleryImages = document.querySelectorAll('.gallery-image');
    const modal = document.getElementById('gallery-modal');
    const modalImage = document.getElementById('modal-image');
    const modalTitle = document.getElementById('modal-title');
    const modalDescription = document.getElementById('modal-description');
    const modalDate = document.getElementById('modal-date');
    const closeModal = document.querySelector('.close-modal');

    // Gallery data
    const galleryData = Array.from(galleryImages).map((img, index) => ({
        src: img.src,
        title: img.dataset.title,
        description: img.dataset.description,
        date: img.dataset.date,
        index: index
    }));

    // Open modal
    galleryImages.forEach((image, index) => {
        image.addEventListener('click', function() {
            openModal(index);
        });
    });

    // Close modal
    closeModal.addEventListener('click', function() {
        closeModalFunction();
    });

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModalFunction();
        }
    });

    // Keyboard navigation (Escape only)
    document.addEventListener('keydown', function(e) {
        if (modal.classList.contains('hidden')) return;

        if (e.key === 'Escape') {
            closeModalFunction();
        }
    });

    // Like functionality - optimized
    document.addEventListener('click', function(e) {
        if (e.target.closest('.like-btn')) {
            e.preventDefault();
            const button = e.target.closest('.like-btn');
            const galleryId = button.dataset.galleryId;
            toggleLike(galleryId, button);
        }
    });

    function openModal(index) {
        const data = galleryData[index];
        modalImage.src = data.src;
        modalTitle.textContent = data.title;
        modalDescription.textContent = data.description;
        modalDate.textContent = data.date;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeModalFunction() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    // Optimized Like functionality with better error handling
    async function toggleLike(galleryId, button) {
        // Disable button to prevent double clicks
        button.disabled = true;
        button.style.opacity = '0.6';

        try {
            const response = await fetch('api/toggle_like.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ gallery_id: parseInt(galleryId) })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            if (result.success) {
                const icon = button.querySelector('i');
                const countSpan = button.querySelector('.like-count');

                if (result.liked) {
                    button.classList.remove('text-gray-400');
                    button.classList.add('text-red-500');
                    icon.classList.add('fas');
                    icon.classList.remove('far');
                } else {
                    button.classList.remove('text-red-500');
                    button.classList.add('text-gray-400');
                    icon.classList.add('far');
                    icon.classList.remove('fas');
                }

                // Animate count change
                countSpan.style.transform = 'scale(1.2)';
                countSpan.textContent = result.like_count;
                setTimeout(() => {
                    countSpan.style.transform = 'scale(1)';
                }, 200);

                console.log('Like toggled successfully:', result.liked ? 'liked' : 'unliked', 'Count:', result.like_count);
            } else {
                console.error('Like toggle failed:', result.message);
                alert('Gagal mengubah like: ' + result.message);
            }
        } catch (error) {
            console.error('Error toggling like:', error);
            alert('Terjadi kesalahan saat mengubah like. Silakan coba lagi.');
        } finally {
            // Re-enable button
            button.disabled = false;
            button.style.opacity = '1';
        }
    }
});
