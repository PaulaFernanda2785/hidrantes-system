window.HidrantesApp.onReady(() => {
    const filterMunicipio = document.getElementById('filter-municipio-id');
    const filterBairro = document.getElementById('filter-bairro-id');
    const printPreviewButton = document.getElementById('open-report-print-preview');
    const printModal = document.getElementById('report-print-modal');
    const printCloseButtons = printModal ? printModal.querySelectorAll('[data-report-print-close]') : [];
    const printTriggerButton = document.getElementById('trigger-report-print');

    const setupBairroFilter = () => {
        if (!filterMunicipio || !filterBairro) {
            return;
        }

        const initialSelectedBairroId = filterBairro.dataset.selectedBairroId || filterBairro.value || '';

        const renderFilterBairros = (items, selectedId = '') => {
            filterBairro.innerHTML = '<option value="">Todos</option>';

            items.forEach((item) => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.nome;

                if (String(item.id) === String(selectedId)) {
                    option.selected = true;
                }

                filterBairro.appendChild(option);
            });

            filterBairro.value = String(selectedId);
            filterBairro.disabled = false;
        };

        const loadFilterBairros = async (municipioId, selectedId = '') => {
            if (!municipioId) {
                filterBairro.innerHTML = '<option value="">Todos</option>';
                filterBairro.value = '';
                filterBairro.disabled = true;
                return;
            }

            filterBairro.disabled = true;
            filterBairro.innerHTML = '<option value="">Carregando...</option>';

            try {
                const response = await fetch(`/api/bairros/municipio/${municipioId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    throw new Error('Falha ao carregar bairros');
                }

                const items = await response.json();
                renderFilterBairros(Array.isArray(items) ? items : [], selectedId);
            } catch (error) {
                filterBairro.innerHTML = '<option value="">Nao foi possivel carregar</option>';
                filterBairro.value = '';
                filterBairro.disabled = true;
            }
        };

        filterMunicipio.addEventListener('change', async () => {
            await loadFilterBairros(filterMunicipio.value, '');
        });

        if (filterMunicipio.value) {
            if (filterBairro.options.length <= 1) {
                loadFilterBairros(filterMunicipio.value, initialSelectedBairroId);
                return;
            }

            filterBairro.disabled = false;
            return;
        }

        filterBairro.disabled = true;
    };

    const closePrintModal = () => {
        if (!printModal) {
            return;
        }

        printModal.hidden = true;
        document.body.classList.remove('modal-open');
    };

    const openPrintModal = () => {
        if (!printModal) {
            return;
        }

        printModal.hidden = false;
        document.body.classList.add('modal-open');
    };

    const triggerPrint = () => {
        document.body.classList.add('report-print-mode');
        window.print();
    };

    window.addEventListener('afterprint', () => {
        document.body.classList.remove('report-print-mode');
    });

    setupBairroFilter();

    if (!printModal || !printPreviewButton || !printTriggerButton) {
        return;
    }

    printPreviewButton.addEventListener('click', openPrintModal);

    printCloseButtons.forEach((button) => {
        button.addEventListener('click', closePrintModal);
    });

    printModal.addEventListener('click', (event) => {
        if (event.target === printModal) {
            closePrintModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !printModal.hidden) {
            closePrintModal();
        }
    });

    printTriggerButton.addEventListener('click', triggerPrint);
});
