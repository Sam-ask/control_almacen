document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const alertMessage = document.getElementById('alertMessage');
    const btnSubmit = document.getElementById('btnSubmit');

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        alertMessage.classList.add('d-none');
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = 'Validando...';

        const formData = new FormData(loginForm);

        try {
            const response = await fetch('../controllers/AuthController.php?action=login', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.status === 'success') {
                alertMessage.classList.remove('d-none', 'alert-danger');
                alertMessage.classList.add('alert-success');
                alertMessage.textContent = 'Acceso concedido. Redirigiendo...';
                
                setTimeout(() => {
                    window.location.href = data.redirect; 
                }, 1000);
            } else {
                mostrarError(data.message);
            }

        } catch (error) {
            console.error('Error en la petición:', error);
            mostrarError('Ocurrió un error de conexión con el servidor.');
        } finally {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = 'Iniciar Sesión';
        }
    });

    function mostrarError(mensaje) {
        alertMessage.classList.remove('d-none', 'alert-success');
        alertMessage.classList.add('alert-danger');
        alertMessage.textContent = mensaje;
    }
});