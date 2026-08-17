// Validação client-side dos formulários (feedback visual do Bootstrap)
(function () {
    'use strict';

    const forms = document.querySelectorAll('.needs-validation');

    Array.from(forms).forEach((form) => {
        form.addEventListener('submit', (event) => {
            let valido = form.checkValidity();

            const senha = form.querySelector('input[name="senha"]');
            const confirmar = form.querySelector('input[name="confirmar_senha"]');
            if (senha && confirmar && senha.value !== confirmar.value) {
                confirmar.setCustomValidity('As senhas não coincidem.');
                valido = false;
            } else if (confirmar) {
                confirmar.setCustomValidity('');
            }

            if (!valido) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        }, false);
    });

    // Preview de imagem antes do envio (capa de jogo / avatar)
    document.querySelectorAll('input[type="file"][data-preview]').forEach((input) => {
        input.addEventListener('change', () => {
            const previewEl = document.getElementById(input.dataset.preview);
            if (!previewEl || !input.files || !input.files[0]) return;
            const leitor = new FileReader();
            leitor.onload = (e) => { previewEl.src = e.target.result; };
            leitor.readAsDataURL(input.files[0]);
        });
    });
})();
