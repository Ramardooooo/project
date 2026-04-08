// Announcements Hero Slider & Modal
document.addEventListener('DOMContentLoaded', function() {
  // Hero Slider
  const slides = document.querySelectorAll('.slider-slide');
  const dots = document.querySelectorAll('.hero-slider button[onclick]');
  let currentSlide = 0;
  let slideInterval;

function showSlide(index) {
    slides.forEach((slide, i) => {
      if (i === index) {
        slide.style.display = 'block';
        setTimeout(() => slide.style.opacity = '1', 50);
      } else {
        slide.style.opacity = '0';
        setTimeout(() => slide.style.display = 'none', 700);
      }
    });
    dots.forEach((dot, i) => dot.classList.toggle('bg-white', i === index));
    currentSlide = index;
  }

  function nextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
  }

  // Auto slide
  slideInterval = setInterval(nextSlide, 5000);

  // Pause on hover
  document.querySelector('.hero-slider').addEventListener('mouseenter', () => clearInterval(slideInterval));
  document.querySelector('.hero-slider').addEventListener('mouseleave', () => {
    slideInterval = setInterval(nextSlide, 5000);
  });

  dots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
      clearInterval(slideInterval);
      showSlide(index);
    });
  });

  // Modal functions
  window.openModal = function(id) {
    // Fetch full announcement
    fetch(`api/get_announcement.php?id=${id}`)
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          document.getElementById('modalTitle').textContent = data.title;
          document.getElementById('modalDate').textContent = new Date(data.created_at).toLocaleDateString('id-ID', { 
            day: 'numeric', month: 'short', year: 'numeric', hour: 'numeric', minute: 'numeric' 
          });
          document.getElementById('modalContent').innerHTML = data.content.replace(/\n/g, '<br>');
          document.getElementById('announcementModal').classList.remove('hidden');
        }
      })
      .catch(() => alert('Error loading announcement'));
  };

  window.closeModal = function() {
    document.getElementById('announcementModal').classList.add('hidden');
  };

  window.shareAnnouncement = function() {
    const title = document.getElementById('modalTitle').textContent;
    const url = window.location.origin + window.location.pathname + '#announcements';
    window.open(`https://wa.me/?text=${encodeURIComponent(title + ' ' + url)}`, '_blank');
  };

  window.printAnnouncement = function() {
    const content = document.getElementById('modalContent').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
      <html>
        <head><title>Pengumuman RT/RW</title>
        <style>body{font-family:sans-serif;line-height:1.6;margin:40px} .title{font-size:24px;font-weight:bold;margin-bottom:20px}</style>
        </head>
        <body>
          <div class="title">${document.getElementById('modalTitle').textContent}</div>
          <div>${content}</div>
        </body>
      </html>
    `);
    printWindow.document.close();
    printWindow.print();
  };

  // Close modal on ESC
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeModal();
  });
});
