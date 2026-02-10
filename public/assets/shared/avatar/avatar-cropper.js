let cropper;
const input = document.getElementById('avatarInput');
const preview = document.getElementById('avatarPreview');
const hidden = document.getElementById('croppedImage');

if (input) {
  input.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = () => {
      preview.src = reader.result;

      if (cropper) cropper.destroy();

      cropper = new Cropper(preview, {
        aspectRatio: 1,           // 👈 AVATAR 1:1
        viewMode: 1,
        dragMode: 'move',
        autoCropArea: 1,
        responsive: true,
      });
    };
    reader.readAsDataURL(file);
  });
}

document.querySelector('.avatar-form')?.addEventListener('submit', () => {
  if (!cropper) return;

  const canvas = cropper.getCroppedCanvas({
    width: 400,
    height: 400,   // taille finale avatar
    imageSmoothingQuality: 'high'
  });

  hidden.value = canvas.toDataURL('image/webp', 0.9);
});
