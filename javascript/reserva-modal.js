document.addEventListener('DOMContentLoaded', () => {
    const reservaModal = document.getElementById('reservaModal');
    const openReservaModal = document.getElementById('openReservaModal');
    const closeReservaModal = document.getElementById('closeReservaModal');
    const cancelReservaModal = document.getElementById('cancelReservaModal');
    const closeVerifyModal = document.getElementById('closeVerifyModal');
    const tokenInput = document.getElementById('csrf_token');
    const csrfUrl = reservaModal?.dataset.csrfUrl;

    async function loadCsrfToken() {
        if (!csrfUrl || !tokenInput) return;
        try {
            const response = await fetch(csrfUrl, { credentials: 'same-origin' });
            if (!response.ok) throw new Error('Não foi possível obter token CSRF');
            const data = await response.json();
            tokenInput.value = data.csrf_token;
        } catch (error) {
            console.error(error);
        }
    }

    function openModal(event) {
        if (event) event.preventDefault();
        if (!reservaModal) return;
        reservaModal.classList.add('visible');
        reservaModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        loadCsrfToken();
    }

    function closeModal(event) {
        if (event && event.preventDefault) event.preventDefault();
        if (!reservaModal) return;
        reservaModal.classList.remove('visible');
        reservaModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    if (openReservaModal) {
        openReservaModal.addEventListener('click', openModal);
    }
    if (closeReservaModal) {
        closeReservaModal.addEventListener('click', closeModal);
    }
    if (cancelReservaModal) {
        cancelReservaModal.addEventListener('click', closeModal);
    }
    if (closeVerifyModal) {
        closeVerifyModal.addEventListener('click', closeModal);
    }
    if (reservaModal) {
        reservaModal.addEventListener('click', event => {
            if (event.target === reservaModal) closeModal();
        });
    }
    window.addEventListener('keydown', event => {
        if (event.key === 'Escape' && reservaModal?.classList.contains('visible')) {
            closeModal();
        }
    });
});