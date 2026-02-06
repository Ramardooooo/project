window.addEventListener('scroll', function() {
    const backToTop = document.getElementById('back-to-top');
    if (window.pageYOffset > 300) {
        backToTop.classList.remove('opacity-0', 'invisible');
        backToTop.classList.add('opacity-100', 'visible');
    } else {
        backToTop.classList.remove('opacity-100', 'visible');
        backToTop.classList.add('opacity-0', 'invisible');
    }
});

document.getElementById('back-to-top').addEventListener('click', function() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

document.querySelector('button').addEventListener('click', function(e) {
    const button = e.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    button.disabled = true;

    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check mr-2"></i>Subscribed!';
        button.classList.remove('from-blue-500', 'to-purple-600');
        button.classList.add('from-green-500', 'to-green-600');

        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
            button.classList.remove('from-green-500', 'to-green-600');
            button.classList.add('from-blue-500', 'to-blue-600');
        }, 3000);
    }, 2000);
});

document.querySelectorAll('.fa-facebook-f, .fa-twitter, .fa-instagram, .fa-youtube, .fa-whatsapp').forEach(icon => {
    icon.parentElement.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.1) rotate(5deg)';
    });
    icon.parentElement.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1) rotate(0deg)';
    });
});
