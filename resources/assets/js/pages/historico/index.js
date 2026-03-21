window.HidrantesApp.onReady(() => {
    const modal = document.getElementById('historico-detail-modal');
    if (!modal) {
        return;
    }

    const triggers = document.querySelectorAll('[data-history-detail]');
    const closeButtons = modal.querySelectorAll('[data-modal-close]');
    const detailDate = document.getElementById('historico-detail-date');
    const detailUser = document.getElementById('historico-detail-user');
    const detailAction = document.getElementById('historico-detail-action');
    const detailEntity = document.getElementById('historico-detail-entity');
    const detailRecord = document.getElementById('historico-detail-record');
    const detailDescription = document.getElementById('historico-detail-description');

    const closeModal = () => {
        modal.hidden = true;
        document.body.classList.remove('modal-open');
    };

    const openModal = (trigger) => {
        detailDate.textContent = trigger.dataset.dataAcao || '-';
        detailUser.textContent = trigger.dataset.usuario || '-';
        detailAction.textContent = trigger.dataset.acao || '-';
        detailEntity.textContent = trigger.dataset.entidade || '-';
        detailRecord.textContent = trigger.dataset.registro || '-';
        detailDescription.textContent = trigger.dataset.detalhes || '-';

        modal.hidden = false;
        document.body.classList.add('modal-open');
    };

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', () => openModal(trigger));
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.hidden) {
            closeModal();
        }
    });
});
