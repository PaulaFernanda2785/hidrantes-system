window.HidrantesApp.onReady(() => {
    const confirmForms = document.querySelectorAll('[data-confirm-submit]');
    const detailTriggers = document.querySelectorAll('[data-hidrante-detail]');
    const modal = document.getElementById('hidrante-detail-modal');
    const closeButtons = modal ? modal.querySelectorAll('[data-hidrante-detail-close]') : [];
    const modalSubtitle = document.getElementById('hidrante-detail-subtitle');
    const badgeNumber = document.getElementById('hidrante-detail-badge-number');
    const badgeStatus = document.getElementById('hidrante-detail-badge-status');
    const badgeRegion = document.getElementById('hidrante-detail-badge-region');
    const detailGrid = document.getElementById('hidrante-detail-grid');
    const mapSection = document.getElementById('hidrante-detail-map-section');
    const mapCoordinates = document.getElementById('hidrante-detail-map-coordinates');
    const mapFrame = document.getElementById('hidrante-detail-map-frame');
    const photoCount = document.getElementById('hidrante-detail-photo-count');
    const photoEmpty = document.getElementById('hidrante-detail-photo-empty');
    const photoGrid = document.getElementById('hidrante-detail-photo-grid');
    const filterMunicipio = document.getElementById('filter-municipio-id');
    const filterBairro = document.getElementById('filter-bairro-id');
    const filterBairroHelp = document.getElementById('filter-bairro-help');
    const defaultBairroHelp = filterBairroHelp ? (filterBairroHelp.dataset.defaultMessage || 'Selecione um município para listar todos os bairros disponíveis.') : '';
    const initialSelectedBairroId = filterBairro ? (filterBairro.dataset.selectedBairroId || filterBairro.value || '') : '';
    let lastTrigger = null;

    const textValue = (value) => {
        const normalized = String(value ?? '').trim();
        return normalized === '' ? '-' : normalized;
    };

    const statusClass = (status) => {
        switch (String(status ?? '').trim().toLowerCase()) {
            case 'operante':
                return 'is-operante';
            case 'operante com restricao':
                return 'is-restricao';
            case 'inoperante':
                return 'is-inoperante';
            default:
                return 'is-neutral';
        }
    };

    const statusLabel = (status) => {
        switch (String(status ?? '').trim().toLowerCase()) {
            case 'operante com restricao':
                return 'operante com restrição';
            default:
                return textValue(status);
        }
    };

    const bindConfirmForms = () => {
        confirmForms.forEach((form) => {
            form.addEventListener('submit', (event) => {
                const message = form.dataset.confirmMessage || 'Deseja continuar com esta operação?';

                if (!window.confirm(message)) {
                    event.preventDefault();
                }
            });
        });
    };

    const setBairroHelp = (message, state = 'neutral') => {
        if (!filterBairroHelp) {
            return;
        }

        filterBairroHelp.textContent = message;
        filterBairroHelp.dataset.state = state;
    };

    const renderFilterBairros = (items, selectedId = '') => {
        if (!filterBairro) {
            return;
        }

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
        if (!filterBairro) {
            return;
        }

        if (!municipioId) {
            filterBairro.innerHTML = '<option value="">Todos</option>';
            filterBairro.value = '';
            filterBairro.disabled = true;
            setBairroHelp(defaultBairroHelp, 'neutral');
            return;
        }

        filterBairro.disabled = true;
        filterBairro.innerHTML = '<option value="">Carregando...</option>';
        setBairroHelp('Carregando bairros do município selecionado...', 'warning');

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
            const bairros = Array.isArray(items) ? items : [];

            renderFilterBairros(bairros, selectedId);

            if (bairros.length === 0) {
                setBairroHelp('Nenhum bairro ativo cadastrado para este município.', 'warning');
                return;
            }

            setBairroHelp('Escolha um bairro específico ou mantenha "Todos" para pesquisar no município inteiro.', 'neutral');
        } catch (error) {
            filterBairro.innerHTML = '<option value="">Não foi possível carregar</option>';
            filterBairro.value = '';
            filterBairro.disabled = true;
            setBairroHelp('Não foi possível carregar os bairros deste município agora.', 'error');
        }
    };

    const setupBairroFilter = () => {
        if (!filterMunicipio || !filterBairro) {
            return;
        }

        filterMunicipio.addEventListener('change', async () => {
            await loadFilterBairros(filterMunicipio.value, '');
        });

        filterBairro.addEventListener('change', () => {
            if (!filterMunicipio.value) {
                setBairroHelp(defaultBairroHelp, 'neutral');
                return;
            }

            if (!filterBairro.value) {
                setBairroHelp('Filtrando todos os bairros do município selecionado.', 'neutral');
                return;
            }

            setBairroHelp('Filtro aplicado para o bairro selecionado dentro do município atual.', 'success');
        });

        if (filterMunicipio.value) {
            if (filterBairro.options.length <= 1) {
                loadFilterBairros(filterMunicipio.value, initialSelectedBairroId);
                return;
            }

            filterBairro.disabled = false;
            if (initialSelectedBairroId) {
                setBairroHelp('Filtro aplicado para o bairro selecionado dentro do município atual.', 'success');
                return;
            }

            setBairroHelp('Escolha um bairro específico ou mantenha "Todos" para pesquisar no município inteiro.', 'neutral');
            return;
        }

        filterBairro.disabled = true;
        setBairroHelp(defaultBairroHelp, 'neutral');
    };

    const getCoordinatePair = (detail) => {
        const latitudeValue = String(detail.latitude ?? '').trim().replace(',', '.');
        const longitudeValue = String(detail.longitude ?? '').trim().replace(',', '.');
        const latitude = Number.parseFloat(latitudeValue);
        const longitude = Number.parseFloat(longitudeValue);

        if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
            return null;
        }

        if (latitude < -90 || latitude > 90 || longitude < -180 || longitude > 180) {
            return null;
        }

        return {
            latitude,
            longitude,
            latitudeValue,
            longitudeValue,
        };
    };

    const buildMapPreviewUrl = (coordinatePair) => {
        const deltaLat = 0.0045;
        const deltaLon = 0.0045 / Math.max(Math.cos((coordinatePair.latitude * Math.PI) / 180), 0.2);
        const bbox = [
            (coordinatePair.longitude - deltaLon).toFixed(6),
            (coordinatePair.latitude - deltaLat).toFixed(6),
            (coordinatePair.longitude + deltaLon).toFixed(6),
            (coordinatePair.latitude + deltaLat).toFixed(6),
        ].join(',');

        return `https://www.openstreetmap.org/export/embed.html?bbox=${encodeURIComponent(bbox)}&layer=mapnik&marker=${encodeURIComponent(`${coordinatePair.latitudeValue},${coordinatePair.longitudeValue}`)}`;
    };

    const createDetailItem = (label, value, full = false) => {
        const wrapper = document.createElement('div');
        wrapper.className = `modal-detail-item${full ? ' modal-detail-item-full' : ''}`;

        const term = document.createElement('dt');
        term.textContent = label;

        const description = document.createElement('dd');
        description.textContent = textValue(value);

        wrapper.appendChild(term);
        wrapper.appendChild(description);

        return wrapper;
    };

    const renderDetailGrid = (detail) => {
        const items = [
            ['Número do hidrante', detail.numero_hidrante],
            ['Equipe responsável', detail.equipe_responsavel],
            ['Área', detail.area],
            ['Existe no local', detail.existe_no_local],
            ['Tipo de hidrante', detail.tipo_hidrante],
            ['Status operacional', statusLabel(detail.status_operacional)],
            ['Acessibilidade', detail.acessibilidade],
            ['Tampo e conexões', detail.tampo_conexoes],
            ['Tampas ausentes', detail.tampas_ausentes],
            ['Caixa de proteção', detail.caixa_protecao],
            ['Condição da caixa', detail.condicao_caixa],
            ['Presença de água no interior', detail.presenca_agua_interior],
            ['Teste realizado', detail.teste_realizado],
            ['Resultado do teste', detail.resultado_teste],
            ['Município', detail.municipio_nome],
            ['Bairro', detail.bairro_nome],
            ['Endereço', detail.endereco, true],
            ['Latitude', detail.latitude],
            ['Longitude', detail.longitude],
            ['Criado em', detail.criado_em],
            ['Atualizado em', detail.atualizado_em],
        ];

        detailGrid.innerHTML = '';
        items.forEach(([label, value, full = false]) => {
            detailGrid.appendChild(createDetailItem(label, value, full));
        });
    };

    const renderMap = (detail) => {
        const coordinatePair = getCoordinatePair(detail);

        if (coordinatePair === null) {
            mapSection.hidden = true;
            mapCoordinates.textContent = '-';
            mapFrame.removeAttribute('src');
            return;
        }

        mapCoordinates.textContent = `${coordinatePair.latitudeValue}, ${coordinatePair.longitudeValue}`;
        mapFrame.src = buildMapPreviewUrl(coordinatePair);
        mapSection.hidden = false;
    };

    const renderPhotos = (detail) => {
        const photos = Array.isArray(detail.fotos) ? detail.fotos.filter((photo) => photo && photo.url) : [];

        photoGrid.innerHTML = '';
        photoCount.textContent = `${photos.length} foto(s)`;
        photoEmpty.hidden = photos.length > 0;
        photoGrid.hidden = photos.length === 0;

        photos.forEach((photo) => {
            const link = document.createElement('a');
            link.href = photo.url;
            link.target = '_blank';
            link.rel = 'noopener noreferrer';
            link.className = 'hidrante-detail-photo-card';

            const image = document.createElement('img');
            image.src = photo.url;
            image.alt = textValue(photo.label);
            image.className = 'hidrante-detail-photo-image';
            image.loading = 'lazy';

            const caption = document.createElement('span');
            caption.className = 'hidrante-detail-photo-label';
            caption.textContent = textValue(photo.label);

            link.appendChild(image);
            link.appendChild(caption);
            photoGrid.appendChild(link);
        });
    };

    const closeModal = () => {
        if (!modal) {
            return;
        }

        modal.hidden = true;
        document.body.classList.remove('modal-open');
        mapFrame.removeAttribute('src');

        if (lastTrigger) {
            lastTrigger.focus();
        }
    };

    const openModal = (trigger) => {
        if (!modal) {
            return;
        }

        let detail = {};

        try {
            detail = JSON.parse(trigger.dataset.hidrante || '{}');
        } catch (error) {
            return;
        }

        lastTrigger = trigger;
        badgeNumber.textContent = textValue(detail.numero_hidrante);
        badgeStatus.textContent = statusLabel(detail.status_operacional);
        badgeStatus.className = `hidrante-detail-badge ${statusClass(detail.status_operacional)}`;
        badgeRegion.textContent = `${textValue(detail.municipio_nome)} / ${textValue(detail.bairro_nome)}`;
        modalSubtitle.textContent = `${textValue(detail.endereco)} | atualizado em ${textValue(detail.atualizado_em)}`;

        renderDetailGrid(detail);
        renderMap(detail);
        renderPhotos(detail);

        modal.hidden = false;
        document.body.classList.add('modal-open');
    };

    bindConfirmForms();
    setupBairroFilter();

    if (
        !modal
        || !modalSubtitle
        || !badgeNumber
        || !badgeStatus
        || !badgeRegion
        || !detailGrid
        || !mapSection
        || !mapCoordinates
        || !mapFrame
        || !photoCount
        || !photoEmpty
        || !photoGrid
    ) {
        return;
    }

    detailTriggers.forEach((trigger) => {
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
