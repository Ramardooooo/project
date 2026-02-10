document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.like-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const galleryId = this.getAttribute('data-gallery-id');
            const likeCount = this.querySelector('.like-count');

            fetch('api/toggle_like.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ gallery_id: parseInt(galleryId) })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    likeCount.textContent = data.like_count;
                    this.classList.toggle('text-red-500', data.liked);
                    this.classList.toggle('text-gray-400', !data.liked);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});

