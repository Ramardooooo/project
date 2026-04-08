// Enhanced Footer JS - Newsletter + Scroll Top
document.addEventListener('DOMContentLoaded', function() {
  // Newsletter Form
  const form = document.getElementById('newsletterForm');
  const emailInput = document.getElementById('newsletterEmail');
  const status = document.getElementById('newsletterStatus');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = emailInput.value.trim();

    try {
      const response = await fetch('../api/newsletter.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email })
      });
      const data = await response.json();

      status.classList.remove('opacity-0', 'text-green-500', 'text-red-500');
      
      if (data.success) {
        status.textContent = data.message;
        status.classList.add('text-green-500', 'opacity-100');
        form.reset();
        setTimeout(() => status.classList.add('opacity-0'), 4000);
      } else {
        status.textContent = data.error || 'Subscription failed';
        status.classList.add('text-red-500', 'opacity-100');
      }
    } catch (error) {
      status.textContent = 'Network error. Try again.';
      status.classList.add('text-red-500', 'opacity-100');
    }
  });

  // Email validation visual feedback
  emailInput.addEventListener('input', () => {
    const valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value);
    emailInput.classList.toggle('ring-2', emailInput.value);
    emailInput.classList.toggle('ring-green-400/50', valid && emailInput.value);
    emailInput.classList.toggle('ring-red-400/50', emailInput.value && !valid);
  });

  // Enhanced Back to Top
  const backToTop = document.getElementById('back-to-top');
  let ticking = false;

  function updateBackToTop() {
    if (!ticking) {
      requestAnimationFrame(() => {
        const scrolled = window.pageYOffset;
        backToTop.classList.toggle('opacity-100', scrolled > 300);
        backToTop.classList.toggle('visible', scrolled > 300);
        ticking = false;
      });
      ticking = true;
    }
  }

  window.addEventListener('scroll', updateBackToTop);
  backToTop.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  // Pulse animation for back-to-top
  const pulseInterval = setInterval(() => {
    if (backToTop.classList.contains('opacity-100')) {
      backToTop.style.animation = 'none';
      backToTop.offsetHeight; // Trigger reflow
      backToTop.style.animation = 'pulse 2s infinite';
    }
  }, 100);

  // Newsletter input focus effect
  emailInput.addEventListener('focus', () => emailInput.parentElement.classList.add('ring-2', 'ring-blue-400/50'));
  emailInput.addEventListener('blur', () => {
    if (!emailInput.value) emailInput.parentElement.classList.remove('ring-2', 'ring-blue-400/50');
  });
});

