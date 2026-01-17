document.addEventListener('DOMContentLoaded', function() {
    const inputImage = document.getElementById('photo');
    const imagePreview = document.getElementById('imagePreview');
    const croppedInput = document.getElementById('croppedImage');
    let cropper;

    inputImage.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = () => {
            imagePreview.src = reader.result;
            imagePreview.style.display = 'block';

            if (cropper) cropper.destroy();

            cropper = new Cropper(imagePreview, {
                aspectRatio: 4 / 3,
                viewMode: 1,
                autoCropArea: 1
            });
        };
        reader.readAsDataURL(file);
    });

    const form = document.querySelector('.form-realisation');
    form.addEventListener('submit', function(e) {
        if (!cropper) return;

        e.preventDefault();
        cropper.getCroppedCanvas().toBlob((blob) => {
            const reader = new FileReader();
            reader.onloadend = () => {
                croppedInput.value = reader.result;
                form.submit();
            };
            reader.readAsDataURL(blob);
        });
    });
});
