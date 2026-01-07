document.addEventListener('DOMContentLoaded', function() {
    const imageRecupere = document.getElementById('photo');
    const imageExemple = document.getElementById('imageExemple');
    const imageRogne = document.getElementById('croppedImage');
    let cropper;

    imageRecupere.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = () => {
            imageExemple.src = reader.result;
            imageExemple.style.display = 'block';

            if (cropper) cropper.destroy();

            cropper = new Cropper(imageExemple, {
                aspectRatio: 3 / 4,
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
                imageRogne.value = reader.result;
                form.submit();
            };
            reader.readAsDataURL(blob);
        });
    });
});