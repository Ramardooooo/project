let cropper = null;

document.addEventListener('DOMContentLoaded', function() {
  const fileInput = document.getElementById('profile_photo');
  const cropImage = document.getElementById('cropper-image');
  const croppedPreview = document.getElementById('profile-preview-cropped');
  const cropControls = document.getElementById('crop-controls');
  const profileForm = document.querySelector('form');

  if (!fileInput || !cropImage || !croppedPreview) return;

  // File input change - init cropper
  fileInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
      const modal = document.getElementById('cropper-modal') || createCropModal();
      const reader = new FileReader();
      reader.onload = function(e) {
        const modalImg = document.getElementById('cropper-modal-image');
        modalImg.src = e.target.result;
        initModalCropper();
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
      };
      reader.readAsDataURL(file);
    }
  });

  // Aspect ratio buttons
  document.querySelectorAll('.crop-btn[data-aspect]').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.crop-btn[data-aspect]').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      const aspect = parseFloat(this.dataset.aspect);
      cropper.setAspectRatio(aspect);
    });
  });

  // Form submit - replace with cropped image
  if (profileForm) {
    profileForm.addEventListener('submit', function(e) {
      if (cropper && fileInput.files.length > 0) {
        e.preventDefault();
        
        cropper.getCroppedCanvas({
          width: 400,
          height: 400,
          imageSmoothingQuality: 'high'
        }).toBlob(function(blob) {
          const formData = new FormData(profileForm);
          
          // Replace original file with cropped blob
          formData.delete('profile_photo');
          formData.append('profile_photo', blob, 'cropped_profile.jpg');
          
          fetch(window.location.href, {
            method: 'POST',
            body: formData
          }).then(response => response.text())
            .then(html => {
              // Simple reload to show success/error message
              window.location.reload();
            })
            .catch(err => {
              console.error('Upload error:', err);
              alert('Error uploading cropped image');
            });
        }, 'image/jpeg', 0.9);
        return false;
      }
    });
  }
});

let cropperModal = null;

function initModalCropper() {
  const modalImg = document.getElementById('cropper-modal-image');
  if (cropperModal) {
    cropperModal.destroy();
  }
  
  cropperModal = new Cropper(modalImg, {
    viewMode: 1,
    dragMode: 'crop',
    aspectRatio: 1,
    autoCropArea: 0.8,
    restore: true,
    cropBoxMovable: true,
    cropBoxResizable: true,
    background: false,
    modal: true,
    highlight: true,
    rotatable: true
  });
}

function createCropModal() {
  // Modal already created in HTML
  return document.getElementById('cropper-modal');
}

function closeCropModal() {
  const modal = document.getElementById('cropper-modal');
  modal.classList.add('hidden');
  document.body.classList.remove('overflow-hidden');
  if (cropperModal) cropperModal.destroy();
}

function resetCropModal() {
  if (cropperModal) cropperModal.reset();
}

function applyCrop() {
  if (cropperModal) {
    const canvas = cropperModal.getCroppedCanvas({width: 400, height: 400});
    const croppedPreview = document.getElementById('profile-preview-cropped');
    croppedPreview.innerHTML = `<img src="${canvas.toDataURL('image/jpeg')}" class="w-full h-full object-cover rounded-full">`;
    closeCropModal();
    const fileInput = document.getElementById('profile_photo');
    const dt = new DataTransfer();
    canvas.toBlob(blob => {
      dt.items.add(new File([blob], 'cropped.jpg', {type: 'image/jpeg'}));
      fileInput.files = dt.files;
    });
  }
}

function resetCrop() {
  if (cropper) {
    cropper.reset();
  }
}

// Global cropper for onclick handlers
window.cropper = null;


