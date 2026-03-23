window.HidrantesApp.onReady(() => {
    const municipio = document.getElementById('municipio_id');
    const bairro = document.getElementById('bairro_id');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const useCurrentLocationButton = document.getElementById('use-current-location-button');
    const openLocationMapButton = document.getElementById('open-location-map-button');
    const clearCurrentLocationButton = document.getElementById('clear-current-location-button');
    const geolocationFeedback = document.getElementById('geolocation-feedback');
    const locationMapPreview = document.getElementById('location-map-preview');
    const locationMapCoordinates = document.getElementById('location-map-preview-coordinates');
    const locationMapFrame = document.getElementById('location-map-frame');
    const fileInput = document.getElementById('upload-fotos-input');
    const cameraInput = document.getElementById('upload-camera-input');
    const dropzone = document.getElementById('upload-dropzone');
    const selectButton = document.getElementById('upload-select-button');
    const cameraButton = document.getElementById('upload-camera-button');
    const previewGrid = document.getElementById('upload-preview-grid');
    const emptyState = document.getElementById('upload-empty-state');
    const limitFeedback = document.getElementById('upload-limit-feedback');
    const selectionCount = document.getElementById('upload-selection-count');
    const bairroFeedback = document.getElementById('bairro-feedback');
    const bairroModal = document.getElementById('bairro-modal');
    const openBairroModalButton = document.getElementById('open-bairro-modal');
    const editBairroButton = document.getElementById('edit-bairro-button');
    const bairroModalCloseButtons = bairroModal ? bairroModal.querySelectorAll('[data-bairro-modal-close]') : [];
    const bairroCreateForm = document.getElementById('bairro-create-form');
    const bairroModalTitle = document.getElementById('bairro-modal-title');
    const bairroModalSubtitle = document.getElementById('bairro-modal-subtitle');
    const bairroModalMunicipio = document.getElementById('bairro-modal-municipio');
    const bairroModalNome = document.getElementById('bairro-modal-nome');
    const bairroModalFeedback = document.getElementById('bairro-modal-feedback');
    const bairroCreateSubmit = document.getElementById('bairro-create-submit');
    const hidranteForm = document.querySelector('.hidrante-form');
    const csrfInput = hidranteForm ? hidranteForm.querySelector('input[name="_token"]') : null;
    const csrfToken = csrfInput ? csrfInput.value : '';
    const maxFiles = Number.parseInt(hidranteForm ? (hidranteForm.dataset.uploadMaxFiles || '3') : '3', 10) || 3;
    const maxFileSizeBytes = Number.parseInt(hidranteForm ? (hidranteForm.dataset.uploadMaxSizeBytes || '5242880') : '5242880', 10) || 5242880;
    const defaultBairroHelp = 'Se não encontrar o bairro, cadastre um novo ou edite o bairro atualmente selecionado.';
    const defaultLimitFeedback = `Limite de ${maxFiles} fotos atingido. Remova uma imagem para anexar outra.`;
    const supportsManagedFileTransfer = (() => {
        try {
            if (typeof DataTransfer === 'undefined') {
                return false;
            }

            const transfer = new DataTransfer();
            return typeof transfer.items !== 'undefined';
        } catch (error) {
            return false;
        }
    })();
    let selectedBairroId = bairro ? bairro.value : '';
    let bairroModalMode = 'create';
    let editingBairroId = '';
    let selectedFiles = [];
    let uploadFeedbackMessage = '';

    if (
        !municipio
        || !bairro
        || !latitudeInput
        || !longitudeInput
        || !fileInput
        || !cameraInput
        || !dropzone
        || !selectButton
        || !cameraButton
        || !previewGrid
        || !emptyState
        || !limitFeedback
        || !selectionCount
        || !bairroFeedback
        || !bairroModal
        || !openBairroModalButton
        || !editBairroButton
        || !bairroCreateForm
        || !bairroModalTitle
        || !bairroModalSubtitle
        || !bairroModalMunicipio
        || !bairroModalNome
        || !bairroModalFeedback
        || !bairroCreateSubmit
        || !hidranteForm
        || !csrfInput
        || !useCurrentLocationButton
        || !openLocationMapButton
        || !clearCurrentLocationButton
        || !geolocationFeedback
        || !locationMapPreview
        || !locationMapCoordinates
        || !locationMapFrame
    ) {
        return;
    }

    const setGeolocationFeedback = (message, state = 'neutral') => {
        geolocationFeedback.textContent = message;
        geolocationFeedback.dataset.state = state;
    };

    const formatFileSize = (bytes) => {
        if (!Number.isFinite(bytes) || bytes <= 0) {
            return '0 B';
        }

        const units = ['B', 'KB', 'MB', 'GB'];
        let size = bytes;
        let unitIndex = 0;

        while (size >= 1024 && unitIndex < units.length - 1) {
            size /= 1024;
            unitIndex += 1;
        }

        const precision = size >= 10 || unitIndex === 0 ? 0 : 1;

        return `${size.toFixed(precision)} ${units[unitIndex]}`;
    };

    const refreshUploadFeedback = () => {
        if (uploadFeedbackMessage) {
            limitFeedback.textContent = uploadFeedbackMessage;
            limitFeedback.hidden = false;
            return;
        }

        const limitReached = selectedFiles.length >= maxFiles;
        limitFeedback.textContent = defaultLimitFeedback;
        limitFeedback.hidden = !limitReached;
    };

    const getCoordinatePair = () => {
        const latitudeValue = latitudeInput.value.trim().replace(',', '.');
        const longitudeValue = longitudeInput.value.trim().replace(',', '.');
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

    const updateGeolocationActionState = () => {
        const hasCoordinates = latitudeInput.value.trim() !== '' || longitudeInput.value.trim() !== '';
        const coordinatePair = getCoordinatePair();

        clearCurrentLocationButton.disabled = !hasCoordinates;
        openLocationMapButton.disabled = coordinatePair === null;
    };

    const hideMapPreview = () => {
        locationMapPreview.hidden = true;
        locationMapCoordinates.textContent = '-';
        locationMapFrame.removeAttribute('src');
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

    const renderMapPreview = ({ reveal = true, scroll = false } = {}) => {
        const coordinatePair = getCoordinatePair();

        if (coordinatePair === null) {
            hideMapPreview();
            return false;
        }

        latitudeInput.value = coordinatePair.latitudeValue;
        longitudeInput.value = coordinatePair.longitudeValue;
        locationMapCoordinates.textContent = `${coordinatePair.latitudeValue}, ${coordinatePair.longitudeValue}`;
        locationMapFrame.src = buildMapPreviewUrl(coordinatePair);

        if (reveal) {
            locationMapPreview.hidden = false;
        }

        if (scroll) {
            locationMapPreview.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest',
            });
        }

        return true;
    };

    const syncMapPreviewWithInputs = () => {
        if (locationMapPreview.hidden) {
            return;
        }

        renderMapPreview({ reveal: true, scroll: false });
    };

    const syncInputFiles = () => {
        if (!supportsManagedFileTransfer) {
            return false;
        }

        const transfer = new DataTransfer();
        selectedFiles.forEach((file) => transfer.items.add(file));

        try {
            fileInput.files = transfer.files;
            return true;
        } catch (error) {
            return false;
        }
    };

    const updateSelectionCount = () => {
        const limitReached = selectedFiles.length >= maxFiles;

        dropzone.classList.toggle('is-limit-reached', limitReached);
        selectButton.disabled = limitReached;
        cameraButton.disabled = limitReached;

        if (selectedFiles.length === 0) {
            selectionCount.textContent = 'Nenhuma imagem selecionada.';
            emptyState.hidden = false;
            refreshUploadFeedback();
            return;
        }

        selectionCount.textContent = limitReached
            ? `${selectedFiles.length} imagem(ns) pronta(s). Limite atingido.`
            : `${selectedFiles.length} imagem(ns) pronta(s) para envio.`;
        emptyState.hidden = true;
        refreshUploadFeedback();
    };

    const renderPreviews = () => {
        previewGrid.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const card = document.createElement('div');
            card.className = 'upload-preview-card';

            const image = document.createElement('img');
            image.className = 'upload-preview-media';
            image.alt = file.name;
            image.src = URL.createObjectURL(file);
            image.addEventListener('load', () => URL.revokeObjectURL(image.src), { once: true });

            const name = document.createElement('div');
            name.className = 'upload-preview-name';
            name.textContent = file.name;

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'btn-secondary upload-preview-remove';
            removeButton.textContent = 'Remover';

            if (!supportsManagedFileTransfer) {
                removeButton.hidden = true;
            } else {
                removeButton.addEventListener('click', () => {
                    selectedFiles = selectedFiles.filter((_, fileIndex) => fileIndex !== index);
                    uploadFeedbackMessage = '';
                    syncInputFiles();
                    renderPreviews();
                    updateSelectionCount();
                });
            }

            card.appendChild(image);
            card.appendChild(name);
            card.appendChild(removeButton);
            previewGrid.appendChild(card);
        });
    };

    const mergeFiles = (incomingFiles) => {
        const allFiles = Array.from(incomingFiles);
        const validFiles = allFiles.filter((file) => file.type.startsWith('image/') && file.size <= maxFileSizeBytes);
        const oversizedFiles = allFiles.filter((file) => file.type.startsWith('image/') && file.size > maxFileSizeBytes);
        const invalidFiles = allFiles.filter((file) => !file.type.startsWith('image/'));

        if (oversizedFiles.length > 0) {
            uploadFeedbackMessage = oversizedFiles.length === 1
                ? `A imagem "${oversizedFiles[0].name}" excede o limite de ${formatFileSize(maxFileSizeBytes)}.`
                : `${oversizedFiles.length} imagens excedem o limite de ${formatFileSize(maxFileSizeBytes)} por arquivo.`;
        } else if (invalidFiles.length > 0) {
            uploadFeedbackMessage = 'Envie apenas imagens JPG, PNG ou WEBP.';
        } else {
            uploadFeedbackMessage = '';
        }

        if (validFiles.length === 0) {
            refreshUploadFeedback();
            return false;
        }

        if (!supportsManagedFileTransfer) {
            selectedFiles = [
                ...Array.from(fileInput.files || []),
                ...Array.from(cameraInput.files || []),
            ]
                .filter((file) => file.type.startsWith('image/') && file.size <= maxFileSizeBytes)
                .slice(0, maxFiles);

            renderPreviews();
            updateSelectionCount();
            return false;
        }

        const remainingSlots = Math.max(0, maxFiles - selectedFiles.length);
        const filesToAdd = validFiles.slice(0, remainingSlots);

        if (filesToAdd.length === 0) {
            updateSelectionCount();
            return false;
        }

        selectedFiles = [...selectedFiles, ...filesToAdd];
        syncInputFiles();
        renderPreviews();
        updateSelectionCount();
        return true;
    };

    const setBairroFeedback = (message = defaultBairroHelp, state = 'neutral') => {
        bairroFeedback.textContent = message;
        bairroFeedback.dataset.state = state;
    };

    const setBairroModalFeedback = (message = '', state = 'neutral') => {
        bairroModalFeedback.textContent = message;
        bairroModalFeedback.dataset.state = state;
    };

    const selectedMunicipioNome = () => {
        if (!municipio.value) {
            return '';
        }

        const selectedOption = municipio.options[municipio.selectedIndex];
        return selectedOption ? selectedOption.textContent.trim() : '';
    };

    const selectedBairroNome = () => {
        if (!bairro.value) {
            return '';
        }

        const selectedOption = bairro.options[bairro.selectedIndex];
        return selectedOption ? selectedOption.textContent.trim() : '';
    };

    const setBairroModalMode = (mode) => {
        bairroModalMode = mode;

        if (mode === 'edit') {
            bairroModalTitle.textContent = 'Editar bairro';
            bairroModalSubtitle.textContent = 'Atualize o nome do bairro vinculado ao município selecionado.';
            bairroCreateSubmit.textContent = 'Atualizar bairro';
            return;
        }

        bairroModalTitle.textContent = 'Cadastrar bairro';
        bairroModalSubtitle.textContent = 'O bairro será vinculado ao município atualmente selecionado.';
        bairroCreateSubmit.textContent = 'Salvar bairro';
    };

    const updateBairroActionState = () => {
        const hasMunicipio = Boolean(municipio.value);
        openBairroModalButton.disabled = !hasMunicipio;
        editBairroButton.disabled = !hasMunicipio || !bairro.value;

        if (!hasMunicipio) {
            setBairroFeedback('Selecione um município para listar ou cadastrar bairros.', 'warning');
            return;
        }

        if (bairroFeedback.dataset.state !== 'success') {
            setBairroFeedback();
        }
    };

    const renderBairros = (items, selectedId = '') => {
        bairro.innerHTML = '<option value="">Selecione</option>';

        items.forEach((item) => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.nome;

            if (String(item.id) === String(selectedId)) {
                option.selected = true;
            }

            bairro.appendChild(option);
        });

        bairro.value = String(selectedId);
        selectedBairroId = bairro.value;
    };

    const loadBairros = async (municipioId, selectedId = '') => {
        if (!municipioId) {
            bairro.innerHTML = '<option value="">Selecione</option>';
            selectedBairroId = '';
            updateBairroActionState();
            return;
        }

        bairro.innerHTML = '<option value="">Carregando...</option>';

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
            renderBairros(Array.isArray(items) ? items : [], selectedId);
        } catch (error) {
            bairro.innerHTML = '<option value="">Não foi possível carregar</option>';
            selectedBairroId = '';
            setBairroFeedback('Não foi possível carregar os bairros deste municipio agora.', 'error');
        }

        updateBairroActionState();
    };

    const closeBairroModal = () => {
        bairroModal.hidden = true;
        document.body.classList.remove('modal-open');
        bairroCreateForm.reset();
        bairroModalMunicipio.value = '';
        editingBairroId = '';
        setBairroModalMode('create');
        setBairroModalFeedback();
    };

    const openBairroCreateModal = () => {
        if (!municipio.value) {
            setBairroFeedback('Selecione um município antes de cadastrar um novo bairro.', 'warning');
            return;
        }

        editingBairroId = '';
        setBairroModalMode('create');
        bairroModalMunicipio.value = selectedMunicipioNome();
        bairroModalNome.value = '';
        setBairroModalFeedback();
        bairroModal.hidden = false;
        document.body.classList.add('modal-open');
        window.setTimeout(() => bairroModalNome.focus(), 0);
    };

    const openBairroEditModal = () => {
        if (!municipio.value) {
            setBairroFeedback('Selecione um município antes de editar um bairro.', 'warning');
            return;
        }

        if (!bairro.value) {
            setBairroFeedback('Selecione um bairro da lista para editar.', 'warning');
            return;
        }

        editingBairroId = bairro.value;
        setBairroModalMode('edit');
        bairroModalMunicipio.value = selectedMunicipioNome();
        bairroModalNome.value = selectedBairroNome();
        setBairroModalFeedback();
        bairroModal.hidden = false;
        document.body.classList.add('modal-open');
        window.setTimeout(() => bairroModalNome.focus(), 0);
    };

    const geolocationErrorMessage = (error) => {
        switch (error && error.code) {
            case 1:
                return 'Permita o acesso à localização do dispositivo para preencher as coordenadas.';
            case 2:
                return 'Não foi possível obter a localização atual do dispositivo.';
            case 3:
                return 'A captura da localização demorou demais. Tente novamente em um local com melhor sinal.';
            default:
                return 'Não foi possível obter a localização atual.';
        }
    };

    selectButton.addEventListener('click', () => fileInput.click());
    cameraButton.addEventListener('click', () => cameraInput.click());

    useCurrentLocationButton.addEventListener('click', () => {
        if (!('geolocation' in navigator)) {
            setGeolocationFeedback('Este dispositivo ou navegador não oferece suporte à geolocalização.', 'error');
            return;
        }

        useCurrentLocationButton.disabled = true;
        useCurrentLocationButton.textContent = 'Obtendo localização...';
        setGeolocationFeedback('Solicitando a localização atual do dispositivo...', 'warning');

        navigator.geolocation.getCurrentPosition(
            (position) => {
                latitudeInput.value = String(position.coords.latitude.toFixed(8));
                longitudeInput.value = String(position.coords.longitude.toFixed(8));
                updateGeolocationActionState();
                renderMapPreview({ reveal: true, scroll: false });
                setGeolocationFeedback(
                    `Coordenadas preenchidas com sucesso. Precisão aproximada de ${Math.round(position.coords.accuracy)} metros. A previa do mapa foi atualizada abaixo.`,
                    'success'
                );
                useCurrentLocationButton.disabled = false;
                useCurrentLocationButton.textContent = 'Atualizar localização atual';
            },
            (error) => {
                setGeolocationFeedback(geolocationErrorMessage(error), 'error');
                useCurrentLocationButton.disabled = false;
                useCurrentLocationButton.textContent = 'Usar localização atual';
            },
            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0,
            }
        );
    });

    openLocationMapButton.addEventListener('click', () => {
        if (!renderMapPreview({ reveal: true, scroll: true })) {
            setGeolocationFeedback('Informe latitude e longitude válidas para conferir o ponto no mapa.', 'error');
            updateGeolocationActionState();
            return;
        }

        setGeolocationFeedback('Prévia do ponto atualizada abaixo para conferência antes de salvar.', 'success');
        updateGeolocationActionState();
    });

    clearCurrentLocationButton.addEventListener('click', () => {
        latitudeInput.value = '';
        longitudeInput.value = '';
        hideMapPreview();
        updateGeolocationActionState();
        setGeolocationFeedback('Latitude e longitude foram limpas manualmente.', 'neutral');
    });

    latitudeInput.addEventListener('input', () => {
        updateGeolocationActionState();
        syncMapPreviewWithInputs();
    });

    longitudeInput.addEventListener('input', () => {
        updateGeolocationActionState();
        syncMapPreviewWithInputs();
    });

    fileInput.addEventListener('change', () => {
        mergeFiles(fileInput.files);
    });

    cameraInput.addEventListener('change', () => {
        const synchronized = mergeFiles(cameraInput.files);
        if (synchronized) {
            cameraInput.value = '';
        }
    });

    ['dragenter', 'dragover'].forEach((eventName) => {
        dropzone.addEventListener(eventName, (event) => {
            event.preventDefault();
            dropzone.classList.add('is-dragover');
        });
    });

    ['dragleave', 'drop'].forEach((eventName) => {
        dropzone.addEventListener(eventName, (event) => {
            event.preventDefault();
            dropzone.classList.remove('is-dragover');
        });
    });

    dropzone.addEventListener('drop', (event) => {
        const droppedFiles = event.dataTransfer ? event.dataTransfer.files : null;
        if (!droppedFiles || droppedFiles.length === 0) {
            return;
        }

        mergeFiles(droppedFiles);
    });

    municipio.addEventListener('change', async () => {
        selectedBairroId = '';
        await loadBairros(municipio.value);
    });

    bairro.addEventListener('change', () => {
        selectedBairroId = bairro.value;
        updateBairroActionState();
        if (bairro.value) {
            setBairroFeedback();
        }
    });

    openBairroModalButton.addEventListener('click', openBairroCreateModal);
    editBairroButton.addEventListener('click', openBairroEditModal);

    bairroModalCloseButtons.forEach((button) => {
        button.addEventListener('click', closeBairroModal);
    });

    bairroModal.addEventListener('click', (event) => {
        if (event.target === bairroModal) {
            closeBairroModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !bairroModal.hidden) {
            closeBairroModal();
        }
    });

    bairroCreateForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const bairroNome = bairroModalNome.value.trim();

        if (!municipio.value) {
            setBairroModalFeedback(
                bairroModalMode === 'edit'
                    ? 'Selecione um município antes de editar o bairro.'
                    : 'Selecione um município antes de cadastrar o bairro.',
                'error'
            );
            return;
        }

        if (bairroNome === '') {
            setBairroModalFeedback('Informe o nome do bairro.', 'error');
            return;
        }

        bairroCreateSubmit.disabled = true;
        bairroCreateSubmit.textContent = 'Salvando...';
        setBairroModalFeedback();

        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('municipio_id', municipio.value);
        formData.append('nome', bairroNome);

        try {
            const endpoint = bairroModalMode === 'edit'
                ? `/api/bairros/${editingBairroId}`
                : '/api/bairros';

            const response = await fetch(endpoint, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const result = await response.json().catch(() => ({}));

            if (!response.ok) {
                setBairroModalFeedback(
                    result.message || (
                        bairroModalMode === 'edit'
                            ? 'Não foi possível atualizar o bairro agora.'
                            : 'Não foi possível cadastrar o bairro agora.'
                    ),
                    'error'
                );
                return;
            }

            selectedBairroId = String(result.id || '');
            await loadBairros(municipio.value, selectedBairroId);

            if (selectedBairroId !== '') {
                bairro.value = selectedBairroId;
            }

            setBairroFeedback(result.message || 'Bairro cadastrado e selecionado com sucesso.', 'success');
            closeBairroModal();
        } catch (error) {
            setBairroModalFeedback(
                bairroModalMode === 'edit'
                    ? 'Não foi possível atualizar o bairro agora.'
                    : 'Não foi possível cadastrar o bairro agora.',
                'error'
            );
        } finally {
            bairroCreateSubmit.disabled = false;
            bairroCreateSubmit.textContent = bairroModalMode === 'edit' ? 'Atualizar bairro' : 'Salvar bairro';
        }
    });

    if (municipio.value && bairro.options.length <= 1) {
        loadBairros(municipio.value, selectedBairroId);
    } else {
        updateBairroActionState();
    }

    updateGeolocationActionState();
    if (getCoordinatePair() !== null) {
        renderMapPreview({ reveal: true, scroll: false });
    }
    updateSelectionCount();
});
