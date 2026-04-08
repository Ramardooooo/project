// Gallery Modal Functions
function openModal(imagePath, title, description, date) {
    const modal = document.getElementById('gallery-modal');
    if (!modal) return;
    document.getElementById('modal-image').src = 'beranda/gallery/' + imagePath;
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-description').textContent = description;
    document.getElementById('modal-date').innerHTML = '<i class="fas fa-calendar-alt mr-2"></i>' + date;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const modal = document.getElementById('gallery-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
}

// Gallery State
const gallerySliderState = {
    currentSlide: 0
};

function initGallerySlider() {
    const scroller = document.getElementById('gallery-scroller');
    const wrapper = document.getElementById('gallery-wrapper');
    const cards = wrapper ? wrapper.querySelectorAll('.gallery-card') : [];
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const dots = document.querySelectorAll('.indicator-dot');

    if (!scroller || cards.length === 0) {
        console.warn('Gallery not ready');
        return;
    }

    const cardWidth = cards[0].offsetWidth + 24;

    function goToSlide(index) {
        const maxSlide = cards.length - 1;
        index = Math.max(0, Math.min(index, maxSlide));
        gallerySliderState.currentSlide = index;
        scroller.scrollTo({ left: index * cardWidth, behavior: 'smooth' });
        updateActive();
    }

    function updateActive() {
        dots.forEach((dot, i) => dot.classList.toggle('active', i === gallerySliderState.currentSlide));
        if (prevBtn) prevBtn.style.opacity = gallerySliderState.currentSlide === 0 ? '0.4' : '1';
        if (nextBtn) nextBtn.style.opacity = gallerySliderState.currentSlide === cards.length - 1 ? '0.4' : '1';
    }

    if (prevBtn) prevBtn.onclick = () => goToSlide(gallerySliderState.currentSlide - 1);
    if (nextBtn) nextBtn.onclick = () => goToSlide(gallerySliderState.currentSlide + 1);
    dots.forEach((dot, i) => dot.onclick = () => goToSlide(i));

scroller.onwheel = (e) => {
        if (e.target.tagName !== 'TEXTAREA' && e.target.tagName !== 'INPUT') {
            e.preventDefault();
            goToSlide(gallerySliderState.currentSlide + (e.deltaY > 0 ? 1 : -1));
        }
    };

    let startX;
    scroller.onmousedown = (e) => {
        startX = e.pageX - scroller.offsetLeft;
        scroller.style.cursor = 'grabbing';
    };
document.addEventListener('mousemove', (e) => {
        if (startX === undefined) return;
        const x = e.pageX - scroller.offsetLeft;
        scroller.scrollLeft = scroller.scrollLeft - (x - startX) * 2;
    });
    document.onmouseup = () => {
        startX = undefined;
        scroller.style.cursor = 'grab';
    };

    let touchStartX;
    scroller.ontouchstart = (e) => {
        touchStartX = e.touches[0].pageX - scroller.offsetLeft;
    };
    scroller.ontouchmove = (e) => {
        e.preventDefault();
        const x = e.touches[0].pageX - scroller.offsetLeft;
        scroller.scrollLeft = scroller.scrollLeft - (x - touchStartX) * 2;
    };

    scroller.onscroll = updateActive;
    updateActive();
    console.log('✅ Gallery fixed - geser + like ready!');
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initGallerySlider);
} else {
    initGallerySlider();
}

// Like - Simple & Fixed Logic
document.addEventListener('click', (e) => {
    const btn = e.target.closest('.like-btn');
    if (!btn) return;

    const galleryId = parseInt(btn.dataset.galleryId);
    const count = btn.querySelector('.like-count');
    const icon = btn.querySelector('i');

    fetch('api/toggle_like.php', {
        method: 'POST',
        body: `gallery_id=${galleryId}`
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            count.textContent = d.like_count;
            if (d.liked) {
                btn.classList.add('text-red-500');
                btn.classList.remove('text-gray-400');
                icon.classList.add('fa-solid');
            } else {
                btn.classList.add('text-gray-400');
                btn.classList.remove('text-red-500');
                icon.classList.add('fa-regular');
            }
        }
    }).catch(console.error);
});

// Modal close
document.addEventListener('click', e => {
    if (e.target.id === 'gallery-modal') closeModal();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

