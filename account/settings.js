// Profile photo preview
document.addEventListener('DOMContentLoaded', function() {
  const fileInput = document.getElementById('profile_photo');
  const preview = document.getElementById('profile-preview');
  
  if (fileInput && preview) {
    fileInput.addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  }
});

// Sidebar toggle (if needed)
function toggleSidebar() {
  // ... 
}

