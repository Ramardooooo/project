// Simple Gallery Modal
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('.gallery-image');
    const modal = document.getElementById('gallery-modal');
    const modalImg = document.getElementById('modal-image');
    const modalTitle = document.getElementById('modal-title');
    const modalDesc = document.getElementById('modal-description');
    const modalDate = document.getElementById('modal-date');
    const closeBtn = document.querySelector('.close-modal');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');

    let currentIndex = 0;
    let galleryItems = [];

    // Collect gallery data
    images.forEach((img, index) => {
        galleryItems.push({
            src: img.src,
            title: img.dataset.title,
            description: img.dataset.description,
            date: img.dataset.date
        });

        img.addEventListener('click', () => {
            currentIndex = index;
            showImage(currentIndex);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        });
    });

    function showImage(index) {
        const item = galleryItems[index];
        modalImg.src = item.src;
        modalTitle.textContent = item.title;
        modalDesc.textContent = item.description;
        modalDate.textContent = item.date;

        // Update counter
        document.getElementById('current-image').textContent = index + 1;
        document.getElementById('total-images').textContent = galleryItems.length;

        // Update buttons
        prevBtn.style.opacity = index > 0 ? '1' : '0.3';
        nextBtn.style.opacity = index < galleryItems.length - 1 ? '1' : '0.3';
    }

    // Close modal
    closeBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    // Navigation
    prevBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (currentIndex > 0) {
            currentIndex--;
            showImage(currentIndex);
        }
    });

    nextBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (currentIndex < galleryItems.length - 1) {
            currentIndex++;
            showImage(currentIndex);
        }
    });

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (!modal.classList.contains('hidden')) {
            if (e.key === 'Escape') closeModal();
            else if (e.key === 'ArrowLeft' && currentIndex > 0) {
                currentIndex--;
                showImage(currentIndex);
            }
            else if (e.key === 'ArrowRight' && currentIndex < galleryItems.length - 1) {
                currentIndex++;
                showImage(currentIndex);
            }
        }
    });
});
