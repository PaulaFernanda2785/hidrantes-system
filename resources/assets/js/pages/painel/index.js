window.HidrantesApp.onReady(() => {
    const mapContainer = document.getElementById('painel-map');
    const mapEmpty = document.getElementById('painel-map-empty');
    const mapHint = document.getElementById('painel-map-hint');
    const pointsScript = document.getElementById('painel-map-points');
    const layerButtons = Array.from(document.querySelectorAll('[data-painel-map-layer]'));
    const useLocationButton = document.getElementById('painel-use-location-button');
    const centerUserButton = document.getElementById('painel-center-user-button');
    const locationFeedback = document.getElementById('painel-location-feedback');
    const nearestEmpty = document.getElementById('painel-nearest-empty');
    const nearestList = document.getElementById('painel-nearest-list');
    const drawer = document.getElementById('painel-hidrante-drawer');
    const drawerCloseButtons = drawer ? drawer.querySelectorAll('[data-painel-drawer-close]') : [];
    const drawerTitle = document.getElementById('painel-drawer-title');
    const drawerSubtitle = document.getElementById('painel-drawer-subtitle');
    const badgeNumber = document.getElementById('painel-drawer-badge-number');
    const badgeRegion = document.getElementById('painel-drawer-badge-region');
    const badgeStatus = document.getElementById('painel-drawer-badge-status');
    const directionsLink = document.getElementById('painel-drawer-directions');
    const mapSection = document.getElementById('painel-drawer-map-section');
    const mapCoordinates = document.getElementById('painel-drawer-coordinates');
    const mapFrame = document.getElementById('painel-drawer-map-frame');
    const operacaoSection = document.getElementById('painel-drawer-section-operacao');
    const condicoesSection = document.getElementById('painel-drawer-section-condicoes');
    const testeSection = document.getElementById('painel-drawer-section-teste');
    const localizacaoSection = document.getElementById('painel-drawer-section-localizacao');
    const photoCount = document.getElementById('painel-drawer-photo-count');
    const photoEmpty = document.getElementById('painel-drawer-photo-empty');
    const photoGrid = document.getElementById('painel-drawer-photo-grid');

    if (
        !mapContainer
        || !mapEmpty
        || !mapHint
        || !pointsScript
        || !useLocationButton
        || !centerUserButton
        || !locationFeedback
        || !nearestEmpty
        || !nearestList
        || !drawer
        || !drawerTitle
        || !drawerSubtitle
        || !badgeNumber
        || !badgeRegion
        || !badgeStatus
        || !directionsLink
        || !mapSection
        || !mapCoordinates
        || !mapFrame
        || !operacaoSection
        || !condicoesSection
        || !testeSection
        || !localizacaoSection
        || !photoCount
        || !photoEmpty
        || !photoGrid
    ) {
        return;
    }

    const textValue = (value, fallback = '-') => {
        const normalized = String(value ?? '').trim();
        return normalized === '' ? fallback : normalized;
    };

    const parsePoints = () => {
        try {
            const parsed = JSON.parse(pointsScript.textContent || '[]');
            return Array.isArray(parsed) ? parsed : [];
        } catch (error) {
            return [];
        }
    };

    const getCoordinatePair = (item) => {
        const latitudeValue = String(item.latitude ?? '').trim().replace(',', '.');
        const longitudeValue = String(item.longitude ?? '').trim().replace(',', '.');
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

    const statusColor = (status) => {
        switch (String(status ?? '').trim().toLowerCase()) {
            case 'operante':
                return '#16a34a';
            case 'operante com restricao':
                return '#f59e0b';
            case 'inoperante':
                return '#dc2626';
            default:
                return '#0b4f8a';
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

    let mapHintTimeoutId = null;
    let leafletMap = null;
    let userLocation = null;
    let userMarker = null;
    let userAccuracyCircle = null;
    const markerEntries = [];

    const updateLayerButtons = (activeLayerName) => {
        layerButtons.forEach((button) => {
            const isActive = button.dataset.painelMapLayer === activeLayerName;
            button.classList.toggle('is-active', isActive);
            button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    };

    const pulseMapHint = () => {
        mapHint.classList.add('is-active');

        if (mapHintTimeoutId !== null) {
            window.clearTimeout(mapHintTimeoutId);
        }

        mapHintTimeoutId = window.setTimeout(() => {
            mapHint.classList.remove('is-active');
        }, 1800);
    };

    const showMapEmpty = (title, message) => {
        mapHint.hidden = true;
        mapEmpty.hidden = false;
        mapEmpty.innerHTML = `<strong>${title}</strong><p>${message}</p>`;
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

    const buildDirectionsUrl = (coordinatePair) => {
        return `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(`${coordinatePair.latitudeValue},${coordinatePair.longitudeValue}`)}&travelmode=driving`;
    };

    const createDetailItem = (label, value, full = false) => {
        const wrapper = document.createElement('div');
        wrapper.className = `painel-drawer-detail-item${full ? ' is-full' : ''}`;

        const term = document.createElement('dt');
        term.textContent = label;

        const description = document.createElement('dd');
        description.textContent = textValue(value, 'Não informado');

        wrapper.appendChild(term);
        wrapper.appendChild(description);

        return wrapper;
    };

    const renderSection = (container, items) => {
        container.innerHTML = '';

        items.forEach(([label, value, full = false]) => {
            container.appendChild(createDetailItem(label, value, full));
        });
    };

    const renderPhotos = (item) => {
        const photos = Array.isArray(item.fotos) ? item.fotos.filter((photo) => photo && photo.url) : [];

        photoCount.textContent = `${photos.length} foto(s)`;
        photoEmpty.hidden = photos.length > 0;
        photoGrid.hidden = photos.length === 0;
        photoGrid.innerHTML = '';

        photos.forEach((photo) => {
            const link = document.createElement('a');
            link.href = photo.url;
            link.target = '_blank';
            link.rel = 'noopener noreferrer';
            link.className = 'painel-drawer-photo-card';

            const image = document.createElement('img');
            image.src = photo.url;
            image.alt = textValue(photo.label);
            image.loading = 'lazy';

            const caption = document.createElement('span');
            caption.textContent = textValue(photo.label);

            link.appendChild(image);
            link.appendChild(caption);
            photoGrid.appendChild(link);
        });
    };

    const renderDrawerMap = (item) => {
        const coordinatePair = getCoordinatePair(item);

        if (coordinatePair === null) {
            mapSection.hidden = true;
            directionsLink.hidden = true;
            directionsLink.removeAttribute('href');
            mapCoordinates.textContent = '-';
            mapFrame.removeAttribute('src');
            return;
        }

        mapCoordinates.textContent = `${coordinatePair.latitudeValue}, ${coordinatePair.longitudeValue}`;
        mapFrame.src = buildMapPreviewUrl(coordinatePair);
        directionsLink.href = buildDirectionsUrl(coordinatePair);
        directionsLink.hidden = false;
        mapSection.hidden = false;
    };

    const renderDrawer = (item) => {
        drawerTitle.textContent = `Hidrante ${textValue(item.numero_hidrante)}`;
        drawerSubtitle.textContent = `${textValue(item.endereco, 'Endereço não informado')} | Atualizado em ${textValue(item.atualizado_em, 'Não informado')}`;
        badgeNumber.textContent = textValue(item.numero_hidrante);
        badgeRegion.textContent = `${textValue(item.municipio_nome)} / ${textValue(item.bairro_nome)}`;
        badgeStatus.textContent = statusLabel(item.status_operacional);
        badgeStatus.className = `painel-drawer-badge ${statusClass(item.status_operacional)}`;

        renderSection(operacaoSection, [
            ['Número do hidrante', item.numero_hidrante],
            ['Equipe responsável', item.equipe_responsavel],
            ['Área', item.area],
            ['Existe no local', item.existe_no_local],
            ['Tipo do hidrante', item.tipo_hidrante, true],
            ['Status operacional', statusLabel(item.status_operacional), true],
        ]);

        renderSection(condicoesSection, [
            ['Acessibilidade', item.acessibilidade],
            ['Tampo e conexões', item.tampo_conexoes],
            ['Tampas ausentes', item.tampas_ausentes],
            ['Caixa de proteção', item.caixa_protecao],
            ['Condição da caixa', item.condicao_caixa],
            ['Presença de água', item.presenca_agua_interior],
        ]);

        renderSection(testeSection, [
            ['Teste realizado', item.teste_realizado],
            ['Resultado do teste', item.resultado_teste, true],
        ]);

        renderSection(localizacaoSection, [
            ['Município', item.municipio_nome],
            ['Bairro', item.bairro_nome],
            ['Endereço', item.endereco, true],
            ['Latitude', item.latitude],
            ['Longitude', item.longitude],
            ['Criado em', item.criado_em],
            ['Atualizado em', item.atualizado_em],
        ]);

        renderDrawerMap(item);
        renderPhotos(item);
    };

    const closeDrawer = () => {
        drawer.hidden = true;
        document.body.classList.remove('modal-open');
        mapFrame.removeAttribute('src');
    };

    const openDrawer = (item) => {
        renderDrawer(item);
        drawer.hidden = false;
        document.body.classList.add('modal-open');
    };

    const setLocationFeedback = (message, state = 'neutral') => {
        locationFeedback.textContent = message;

        if (state === 'neutral') {
            delete locationFeedback.dataset.state;
            return;
        }

        locationFeedback.dataset.state = state;
    };

    const resetNearestHydrants = (message = 'Ative sua localização para mostrar seu ponto no mapa e calcular os hidrantes mais próximos.') => {
        nearestList.hidden = true;
        nearestList.innerHTML = '';
        nearestEmpty.hidden = false;
        nearestEmpty.textContent = message;
    };

    const formatDistance = (distanceMeters) => {
        if (!Number.isFinite(distanceMeters)) {
            return '-';
        }

        if (distanceMeters < 1000) {
            return `${Math.round(distanceMeters)} m`;
        }

        const precision = distanceMeters < 10000 ? 2 : 1;
        return `${(distanceMeters / 1000).toFixed(precision).replace('.', ',')} km`;
    };

    const toRadians = (value) => {
        return (value * Math.PI) / 180;
    };

    const distanceMetersBetween = (fromCoordinatePair, toCoordinatePair) => {
        const earthRadius = 6371000;
        const latitudeDelta = toRadians(toCoordinatePair.latitude - fromCoordinatePair.latitude);
        const longitudeDelta = toRadians(toCoordinatePair.longitude - fromCoordinatePair.longitude);
        const a = Math.sin(latitudeDelta / 2) ** 2
            + Math.cos(toRadians(fromCoordinatePair.latitude))
            * Math.cos(toRadians(toCoordinatePair.latitude))
            * Math.sin(longitudeDelta / 2) ** 2;

        return earthRadius * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
    };

    const applyHydrantMarkerStyle = (marker, item, highlighted = false) => {
        marker.setStyle({
            radius: highlighted ? 11 : 9,
            color: '#ffffff',
            weight: highlighted ? 3 : 2,
            fillColor: statusColor(item.status_operacional),
            fillOpacity: 1,
        });
    };

    const highlightNearestHydrants = (nearestItems) => {
        const highlightedIds = new Set(nearestItems.map((entry) => entry.item.id));

        markerEntries.forEach((entry) => {
            applyHydrantMarkerStyle(entry.marker, entry.item, highlightedIds.has(entry.item.id));
        });
    };

    const focusHydrant = (item, coordinatePair) => {
        if (leafletMap && coordinatePair) {
            leafletMap.flyTo([coordinatePair.latitude, coordinatePair.longitude], Math.max(leafletMap.getZoom(), 16), {
                duration: 0.6,
            });
        }

        openDrawer(item);
    };

    const renderNearestHydrants = (items) => {
        nearestList.innerHTML = '';

        if (items.length === 0) {
            resetNearestHydrants('Não foi possível calcular os hidrantes próximos com os dados atuais.');
            return;
        }

        nearestEmpty.hidden = true;
        nearestList.hidden = false;

        items.forEach((entry, index) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'painel-nearest-card';

            const head = document.createElement('div');
            head.className = 'painel-nearest-card-head';

            const meta = document.createElement('div');
            meta.className = 'painel-nearest-card-meta';

            const title = document.createElement('strong');
            title.textContent = `Hidrante ${textValue(entry.item.numero_hidrante)}`;

            const distance = document.createElement('span');
            distance.className = 'painel-nearest-distance';
            distance.textContent = formatDistance(entry.distanceMeters);

            const region = document.createElement('span');
            region.textContent = `${textValue(entry.item.municipio_nome)} / ${textValue(entry.item.bairro_nome)}`;

            const status = document.createElement('span');
            status.className = `painel-nearest-status ${statusClass(entry.item.status_operacional)}`;
            status.textContent = statusLabel(entry.item.status_operacional || 'Status não informado');

            const address = document.createElement('small');
            address.textContent = textValue(entry.item.endereco, 'Endereço não informado');

            head.appendChild(title);
            head.appendChild(distance);
            meta.appendChild(region);
            meta.appendChild(status);
            button.appendChild(head);
            button.appendChild(meta);
            button.appendChild(address);

            button.addEventListener('click', () => {
                focusHydrant(entry.item, entry.coordinatePair);
            });

            if (index === 0) {
                button.classList.add('is-active');
            }

            nearestList.appendChild(button);
        });
    };

    const updateNearestHydrants = (fitMap = false) => {
        if (!leafletMap || userLocation === null || markerEntries.length === 0) {
            centerUserButton.hidden = true;
            highlightNearestHydrants([]);
            resetNearestHydrants();
            return;
        }

        const nearestItems = markerEntries
            .map((entry) => ({
                ...entry,
                distanceMeters: distanceMetersBetween(userLocation, entry.coordinatePair),
            }))
            .sort((left, right) => left.distanceMeters - right.distanceMeters)
            .slice(0, 5);

        renderNearestHydrants(nearestItems);
        highlightNearestHydrants(nearestItems.slice(0, 3));
        centerUserButton.hidden = false;

        if (fitMap) {
            const bounds = nearestItems
                .slice(0, 3)
                .map((entry) => [entry.coordinatePair.latitude, entry.coordinatePair.longitude]);

            bounds.unshift([userLocation.latitude, userLocation.longitude]);

            if (bounds.length === 1) {
                leafletMap.flyTo(bounds[0], 16, {
                    duration: 0.6,
                });
            } else {
                leafletMap.fitBounds(bounds, {
                    padding: [42, 42],
                    maxZoom: 16,
                });
            }
        }
    };

    const geolocationErrorMessage = (error) => {
        switch (error && error.code) {
            case 1:
                return 'Permita o acesso à localização do dispositivo para mostrar o seu ponto no mapa.';
            case 2:
                return 'Não foi possível obter a localização atual do dispositivo.';
            case 3:
                return 'A captura da localização demorou demais. Tente novamente em um local com melhor sinal.';
            default:
                return 'Não foi possível obter a localização atual.';
        }
    };

    const renderUserLocation = (position) => {
        if (!leafletMap) {
            return;
        }

        const latLng = [position.latitude, position.longitude];
        const accuracyRadius = Math.max(20, Math.round(position.accuracy || 0));

        if (!userMarker) {
            userMarker = window.L.circleMarker(latLng, {
                radius: 10,
                color: '#ffffff',
                weight: 3,
                fillColor: '#2563eb',
                fillOpacity: 1,
            }).addTo(leafletMap);

            userMarker.bindTooltip('Sua localização aproximada', {
                direction: 'top',
                offset: [0, -8],
            });
        } else {
            userMarker.setLatLng(latLng);
        }

        if (!userAccuracyCircle) {
            userAccuracyCircle = window.L.circle(latLng, {
                radius: accuracyRadius,
                color: '#2563eb',
                weight: 1,
                fillColor: '#60a5fa',
                fillOpacity: 0.16,
            }).addTo(leafletMap);
        } else {
            userAccuracyCircle.setLatLng(latLng);
            userAccuracyCircle.setRadius(accuracyRadius);
        }

        userMarker.bringToFront();
    };

    const captureUserLocation = () => {
        if (!leafletMap) {
            setLocationFeedback('O mapa ainda não está pronto para capturar localização.', 'warning');
            return;
        }

        if (!('geolocation' in navigator)) {
            setLocationFeedback('Este navegador não oferece suporte à geolocalização.', 'error');
            return;
        }

        const loopbackHosts = new Set(['localhost', '127.0.0.1', '::1']);
        if (!window.isSecureContext && !loopbackHosts.has(window.location.hostname)) {
            setLocationFeedback(
                `O navegador só libera localização em HTTPS ou localhost. No endereço atual (${window.location.hostname}), ative HTTPS no Wamp para testar.`,
                'error',
            );
            return;
        }

        useLocationButton.disabled = true;
        useLocationButton.textContent = 'Obtendo localização...';
        setLocationFeedback('Solicitando a localização atual do dispositivo...', 'warning');

        navigator.geolocation.getCurrentPosition(
            (position) => {
                userLocation = {
                    latitude: Number(position.coords.latitude),
                    longitude: Number(position.coords.longitude),
                    accuracy: Number(position.coords.accuracy || 0),
                };

                renderUserLocation(userLocation);
                updateNearestHydrants(true);
                pulseMapHint();
                setLocationFeedback(
                    `Sua localização foi marcada no mapa. Precisão aproximada de ${Math.round(position.coords.accuracy)} metros.`,
                    'success',
                );
                useLocationButton.disabled = false;
                useLocationButton.textContent = 'Atualizar minha localização';
            },
            (error) => {
                setLocationFeedback(geolocationErrorMessage(error), 'error');
                useLocationButton.disabled = false;
                useLocationButton.textContent = 'Usar minha localização';
            },
            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0,
            },
        );
    };

    const points = parsePoints().filter((item) => getCoordinatePair(item) !== null);

    resetNearestHydrants();

    if (points.length === 0) {
        showMapEmpty(
            'Nenhum ponto georreferenciado disponível.',
            'Cadastre latitude e longitude nos hidrantes para visualizar a cobertura operacional no mapa do painel.',
        );
        setLocationFeedback('Não há hidrantes georreferenciados suficientes para calcular proximidade.', 'warning');
        useLocationButton.disabled = true;
    } else if (!window.L) {
        showMapEmpty(
            'Não foi possível carregar o mapa.',
            'Verifique a conexão com a internet para carregar a biblioteca do mapa e tente novamente.',
        );
        setLocationFeedback('O mapa não carregou, então a localização atual não pode ser mostrada agora.', 'error');
        useLocationButton.disabled = true;
    } else {
        const streetsLayer = window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 20,
            attribution: '&copy; OpenStreetMap contributors',
        });

        const operationalLayer = window.L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
            maxZoom: 20,
            attribution: '&copy; OpenStreetMap contributors, HOT',
        });

        const satelliteLayer = window.L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 20,
            attribution: 'Tiles &copy; Esri',
        });

        const map = window.L.map(mapContainer, {
            zoomControl: false,
            attributionControl: true,
            dragging: true,
            scrollWheelZoom: true,
            touchZoom: true,
            doubleClickZoom: true,
            boxZoom: false,
            keyboard: true,
            tap: true,
            zoomSnap: 0.5,
            zoomDelta: 0.5,
            wheelPxPerZoomLevel: 100,
            minZoom: 5,
            maxZoom: 20,
        });

        leafletMap = map;

        window.L.control.zoom({
            position: 'bottomright',
        }).addTo(map);

        map.dragging.enable();
        map.scrollWheelZoom.enable();
        map.touchZoom.enable();
        map.doubleClickZoom.enable();
        map.keyboard.enable();
        map.getContainer().style.overscrollBehavior = 'contain';
        map.getContainer().tabIndex = 0;

        const baseLayers = {
            streets: streetsLayer,
            operational: operationalLayer,
            satellite: satelliteLayer,
        };
        let activeBaseLayerName = 'streets';
        let activeBaseLayer = baseLayers[activeBaseLayerName];

        activeBaseLayer.addTo(map);
        updateLayerButtons(activeBaseLayerName);

        const setBaseLayer = (layerName) => {
            const nextLayer = baseLayers[layerName];

            if (!nextLayer || nextLayer === activeBaseLayer) {
                return;
            }

            if (map.hasLayer(activeBaseLayer)) {
                map.removeLayer(activeBaseLayer);
            }

            nextLayer.addTo(map);
            activeBaseLayer = nextLayer;
            activeBaseLayerName = layerName;
            updateLayerButtons(activeBaseLayerName);
        };

        layerButtons.forEach((button) => {
            button.addEventListener('click', () => {
                setBaseLayer(button.dataset.painelMapLayer || 'streets');
            });
        });

        const bounds = [];

        points.forEach((item) => {
            const coordinatePair = getCoordinatePair(item);
            if (!coordinatePair) {
                return;
            }

            const marker = window.L.circleMarker([coordinatePair.latitude, coordinatePair.longitude], {});
            applyHydrantMarkerStyle(marker, item, false);
            marker.addTo(map);
            marker.bindTooltip(textValue(item.numero_hidrante), {
                direction: 'top',
                offset: [0, -8],
            });
            marker.on('click', () => openDrawer(item));

            markerEntries.push({
                item,
                marker,
                coordinatePair,
            });
            bounds.push([coordinatePair.latitude, coordinatePair.longitude]);
        });

        if (bounds.length === 1) {
            map.setView(bounds[0], 15);
        } else {
            map.fitBounds(bounds, {
                padding: [28, 28],
            });
        }

        window.addEventListener('resize', () => {
            map.invalidateSize();
        });

        window.setTimeout(() => {
            map.invalidateSize();
        }, 0);

        setLocationFeedback('Ative sua localização para marcar o seu ponto no mapa e listar os hidrantes mais próximos.', 'neutral');
        pulseMapHint();
    }

    useLocationButton.addEventListener('click', captureUserLocation);

    centerUserButton.addEventListener('click', () => {
        if (!leafletMap || userLocation === null) {
            return;
        }

        leafletMap.flyTo([userLocation.latitude, userLocation.longitude], 16, {
            duration: 0.6,
        });
    });

    drawerCloseButtons.forEach((button) => {
        button.addEventListener('click', closeDrawer);
    });

    drawer.addEventListener('click', (event) => {
        if (event.target === drawer) {
            closeDrawer();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !drawer.hidden) {
            closeDrawer();
        }
    });
});

