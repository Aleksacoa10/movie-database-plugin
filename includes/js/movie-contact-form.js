document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('movie-contact-form');
    const messageElement = document.getElementById('form-message');

    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            messageElement.textContent = ''; 

            const formData = new FormData(form);
            formData.append('action', 'movie_contact_form');
            formData.append('security', movieContactForm.nonce);

            fetch(movieContactForm.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageElement.textContent = data.data;
                    messageElement.style.color = 'green';
                    form.reset();
                } else {
                    messageElement.textContent = data.data;
                    messageElement.style.color = 'red';
                }
            })
            .catch(error => {
                messageElement.textContent = 'Došlo je do greške. Pokušajte ponovo.';
                messageElement.style.color = 'red';
            });
        });
    }
});
