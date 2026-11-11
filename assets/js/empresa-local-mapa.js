(function () {
    function triggerMapResize(map) {
        if (!map || typeof map.invalidateSize !== 'function') {
            return;
        }

        setTimeout(function () {
            map.invalidateSize();
        }, 150);

        setTimeout(function () {
            map.invalidateSize();
        }, 400);
    }

    function bindMapToModal(modalElement, mapInstance) {
        if (!modalElement || !mapInstance) {
            return;
        }

        modalElement.addEventListener('shown.bs.modal', function () {
            triggerMapResize(mapInstance);
            if (typeof mapInstance._ensureVisibleContext === 'function') {
                mapInstance._ensureVisibleContext();
            }
        });

        modalElement.addEventListener('show.bs.modal', function () {
            triggerMapResize(mapInstance);
        });
    }

    function getSelectedText(option) {
        if (!option) {
            return '';
        }

        return (option.textContent || '').trim();
    }

    function getCityOptionState(option) {
        if (!option) {
            return '';
        }

        return (option.getAttribute('data-estado') || '').trim();
    }

    function getUniqueStatesFromCityOptions(selectElement) {
        if (!selectElement) {
            return [];
        }

        const states = [];
        const seen = {};

        Array.from(selectElement.options).forEach(function (option) {
            const state = getCityOptionState(option);
            if (!state || seen[state]) {
                return;
            }

            seen[state] = true;
            states.push(state);
        });

        return states.sort();
    }

    function applyCityFilterByState(stateSelect, citySelect) {
        if (!stateSelect || !citySelect) {
            return null;
        }

        const selectedState = (stateSelect.value || '').trim();
        const optionList = Array.from(citySelect.querySelectorAll('option[data-estado]'));

        citySelect.innerHTML = '<option value="">Selecione a cidade</option>';

        if (!selectedState) {
            citySelect.disabled = true;
            return null;
        }

        const matchingOptions = optionList.filter(function (option) {
            return (option.getAttribute('data-estado') || '').trim() === selectedState;
        });

        matchingOptions.forEach(function (option) {
            const clone = document.createElement('option');
            clone.value = option.value;
            clone.textContent = option.textContent;
            clone.setAttribute('data-estado', option.getAttribute('data-estado') || '');
            citySelect.appendChild(clone);
        });

        citySelect.disabled = matchingOptions.length === 0;

        if (matchingOptions.length > 0) {
            const selectedCity = matchingOptions.find(function (option) {
                return option.value === citySelect.dataset.preselectedCity || option.value === citySelect.getAttribute('data-selected-value');
            }) || matchingOptions[0];

            citySelect.value = selectedCity.value;
            citySelect.dataset.selectedValue = selectedCity.value;
        }

        return citySelect;
    }

    function syncCityFilterWithStateOnLoad(stateSelect, citySelect) {
        if (!stateSelect || !citySelect) {
            return;
        }

        const selectedCityValue = citySelect.value || citySelect.dataset.selectedValue || '';
        const selectedCityOption = Array.from(citySelect.querySelectorAll('option')).find(function (option) {
            return option.value === selectedCityValue;
        });

        const selectedState = selectedCityOption
            ? (selectedCityOption.getAttribute('data-estado') || '').trim()
            : '';

        if (selectedState) {
            stateSelect.value = selectedState;
        }

        applyCityFilterByState(stateSelect, citySelect);

        if (selectedCityValue) {
            const fallbackOption = Array.from(citySelect.options).find(function (option) {
                return option.value === selectedCityValue;
            });

            if (fallbackOption) {
                citySelect.value = fallbackOption.value;
            }
        }
    }

    function buildMap(config) {
        const mapElement = document.getElementById(config.mapId);
        const searchInput = document.getElementById(config.searchInputId);
        const searchButton = document.getElementById(config.searchButtonId);
        const messageBlock = document.getElementById(config.messageId);
        const latInput = document.getElementById(config.latInputId);
        const lngInput = document.getElementById(config.lngInputId);
        const addressInput = document.getElementById(config.addressInputId);
        const stateSelect = document.getElementById(config.stateSelectId);
        const citySelect = document.getElementById(config.citySelectId);
        const modalElement = document.getElementById(config.modalId);

        if (!mapElement || !searchInput || !searchButton || !latInput || !lngInput || !addressInput || !stateSelect || !citySelect) {
            return null;
        }

        const map = L.map(mapElement, {
            zoomControl: true,
            scrollWheelZoom: true,
            dragging: true,
            attributionControl: true,
            preferCanvas: true
        }).setView([-15.7939, -47.8828], 4);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let marker = null;
        let lastRequestId = 0;
        let currentMapCenter = null;

        function updateMessage(text, type) {
            if (!messageBlock) {
                return;
            }

            messageBlock.textContent = text || '';
            messageBlock.classList.remove('local-map-message-error', 'local-map-message-success');

            if (type) {
                messageBlock.classList.add(type === 'error' ? 'local-map-message-error' : 'local-map-message-success');
            }
        }

        function updateCoordinateInputs(lat, lng) {
            if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                return;
            }

            latInput.value = Number(lat).toFixed(6);
            lngInput.value = Number(lng).toFixed(6);
        }

        function setMarker(lat, lng, options) {
            const settings = Object.assign({ fit: false, message: '', type: 'success' }, options || {});

            if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                return;
            }

            if (!marker) {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                marker.on('dragend', function () {
                    const pos = marker.getLatLng();
                    updateCoordinateInputs(pos.lat, pos.lng);
                    if (addressInput && addressInput.value.trim() === '') {
                        updateMessage('Localização ajustada manualmente no mapa.', 'success');
                    }
                });
            } else {
                marker.setLatLng([lat, lng]);
            }

            updateCoordinateInputs(lat, lng);

            if (settings.fit) {
                map.setView([lat, lng], 16);
            }

            if (settings.message) {
                updateMessage(settings.message, settings.type);
            }
        }

        function regionMapFromCity(cityName, stateName, zoomLevel) {
            const finalQuery = [cityName, stateName, 'Brasil'].filter(Boolean).join(', ');

            const requestId = ++lastRequestId;
            const params = new URLSearchParams({
                q: finalQuery,
                format: 'jsonv2',
                limit: '1',
                addressdetails: '1',
                countrycodes: 'br'
            });

            fetch(`https://nominatim.openstreetmap.org/search?${params.toString()}`, {
                headers: {
                    'Accept-Language': 'pt-BR,pt;q=0.9,en;q=0.8'
                }
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Falha ao geocodificar a cidade.');
                    }
                    return response.json();
                })
                .then(function (results) {
                    if (requestId !== lastRequestId || !Array.isArray(results) || results.length === 0) {
                        return;
                    }

                    const result = results[0];
                    const lat = parseFloat(result.lat);
                    const lng = parseFloat(result.lon);

                    if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                        return;
                    }

                    currentMapCenter = { lat: lat, lng: lng };
                    map.setView([lat, lng], zoomLevel || 12);
                })
                .catch(function () {
                    if (requestId === lastRequestId) {
                        updateMessage('Não foi possível localizar a cidade selecionada no mapa.', 'error');
                    }
                });
        }

        function geocodeAddressQuery(query, settings) {
            const options = Object.assign({ fit: true, keepLastPosition: true, quiet: false }, settings || {});
            const normalized = (query || '').trim();
            const cityName = getSelectedText(citySelect.options[citySelect.selectedIndex]);
            const stateName = stateSelect.value.trim();

            if (!normalized) {
                return;
            }

            if (!cityName || !stateName) {
                updateMessage('Selecione primeiro o estado e a cidade antes de pesquisar o endereço.', 'error');
                return;
            }

            const requestId = ++lastRequestId;
            const finalQuery = `${normalized}, ${cityName}, ${stateName}, Brasil`;
            const params = new URLSearchParams({
                q: finalQuery,
                format: 'jsonv2',
                limit: '1',
                addressdetails: '1',
                countrycodes: 'br'
            });

            if (!options.quiet) {
                updateMessage('Buscando endereço no mapa...', 'info');
            }

            fetch(`https://nominatim.openstreetmap.org/search?${params.toString()}`, {
                headers: {
                    'Accept-Language': 'pt-BR,pt;q=0.9,en;q=0.8'
                }
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Falha ao consultar o mapa.');
                    }
                    return response.json();
                })
                .then(function (results) {
                    if (requestId !== lastRequestId) {
                        return;
                    }

                    if (!Array.isArray(results) || results.length === 0) {
                        if (options.keepLastPosition) {
                            updateMessage('Endereço não encontrado. Mantendo a última posição válida.', 'error');
                        }
                        return;
                    }

                    const result = results[0];
                    const lat = parseFloat(result.lat);
                    const lng = parseFloat(result.lon);

                    if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                        if (options.keepLastPosition) {
                            updateMessage('Resultado inválido. Mantendo a última posição válida.', 'error');
                        }
                        return;
                    }

                    if (addressInput && result.display_name) {
                        addressInput.value = result.display_name;
                        if (searchInput && searchInput !== addressInput) {
                            searchInput.value = result.display_name;
                        }
                    }

                    setMarker(lat, lng, {
                        fit: options.fit,
                        message: 'Localização encontrada e atualizada no mapa.',
                        type: 'success'
                    });

                    currentMapCenter = { lat: lat, lng: lng };
                })
                .catch(function () {
                    if (requestId === lastRequestId && options.keepLastPosition) {
                        updateMessage('Não foi possível consultar o endereço informado. Mantendo a última posição válida.', 'error');
                    }
                });
        }

        function initializeFromSavedData() {
            const initialLat = parseFloat(latInput.value || '');
            const initialLng = parseFloat(lngInput.value || '');

            if (Number.isFinite(initialLat) && Number.isFinite(initialLng)) {
                setMarker(initialLat, initialLng, {
                    fit: true,
                    message: 'Localização carregada do cadastro atual.',
                    type: 'success'
                });
                return;
            }

            const cityName = getSelectedText(citySelect.options[citySelect.selectedIndex]);
            const cityState = stateSelect.value.trim();

            if (cityName && cityState) {
                regionMapFromCity(cityName, cityState, 12);
                updateMessage('Mapa pronto para a cidade selecionada.', 'success');
            }
        }

        function configureCitySelectState() {
            const selectedState = stateSelect.value.trim();
            const cityOptions = Array.from(citySelect.options).filter(function (option) {
                return option.value !== '';
            });

            citySelect.innerHTML = '<option value="">Selecione a cidade</option>';

            if (!selectedState) {
                citySelect.disabled = true;
                updateMessage('Selecione o estado para ativar as cidades.', 'info');
                return;
            }

            cityOptions.forEach(function (option) {
                const optionState = (option.getAttribute('data-estado') || '').trim();
                if (optionState === selectedState) {
                    const clone = document.createElement('option');
                    clone.value = option.value;
                    clone.textContent = option.textContent;
                    clone.setAttribute('data-estado', optionState);
                    citySelect.appendChild(clone);
                }
            });

            citySelect.disabled = citySelect.options.length <= 1;

            if (citySelect.options.length > 1) {
                const selectedValue = citySelect.dataset.selectedValue || citySelect.value || citySelect.options[1].value;
                const match = Array.from(citySelect.options).find(function (option) {
                    return option.value === selectedValue;
                });

                if (match) {
                    citySelect.value = match.value;
                }
            }
        }

        function scheduleAddressSearch() {
            const inputValue = (addressInput.value || searchInput.value || '').trim();
            if (!inputValue) {
                return;
            }

            clearTimeout(searchInput._searchTimer);
            searchInput._searchTimer = setTimeout(function () {
                geocodeAddressQuery(inputValue, { fit: true, keepLastPosition: true, quiet: false });
            }, 700);
        }

        stateSelect.addEventListener('change', function () {
            citySelect.dataset.selectedValue = '';
            configureCitySelectState();

            const cityName = getSelectedText(citySelect.options[citySelect.selectedIndex]);
            if (cityName && stateSelect.value) {
                regionMapFromCity(cityName, stateSelect.value, 12);
            } else {
                map.setView([-15.7939, -47.8828], 4);
            }
        });

        citySelect.addEventListener('change', function () {
            const cityName = getSelectedText(citySelect.options[citySelect.selectedIndex]);
            const selectedState = stateSelect.value.trim();

            if (!cityName || !selectedState) {
                updateMessage('Selecione uma cidade válida.', 'error');
                return;
            }

            citySelect.dataset.selectedValue = citySelect.value;
            regionMapFromCity(cityName, selectedState, 12);
            updateMessage(`Mapa centralizado em ${cityName} - ${selectedState}.`, 'success');
        });

        map.on('click', function (event) {
            setMarker(event.latlng.lat, event.latlng.lng, {
                fit: false,
                message: 'Localização ajustada manualmente no mapa.',
                type: 'success'
            });
        });

        searchButton.addEventListener('click', function () {
            const query = (addressInput.value || searchInput.value || '').trim();
            if (!query) {
                updateMessage('Digite um endereço para buscar no mapa.', 'error');
                return;
            }
            geocodeAddressQuery(query, { fit: true, keepLastPosition: true, quiet: false });
        });

        searchInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                const query = (searchInput.value || addressInput.value || '').trim();
                geocodeAddressQuery(query, { fit: true, keepLastPosition: true, quiet: false });
            }
        });

        addressInput.addEventListener('input', function () {
            scheduleAddressSearch();
        });

        addressInput.addEventListener('change', function () {
            const query = (addressInput.value || '').trim();
            if (query) {
                geocodeAddressQuery(query, { fit: true, keepLastPosition: true, quiet: false });
            }
        });

        if (modalElement) {
            modalElement.addEventListener('shown.bs.modal', function () {
                triggerMapResize(map);
                initializeFromSavedData();
            });
        }

        configureCitySelectState();
        initializeFromSavedData();
        triggerMapResize(map);

        map._ensureVisibleContext = function () {
            const savedLat = parseFloat(latInput.value || '');
            const savedLng = parseFloat(lngInput.value || '');

            if (Number.isFinite(savedLat) && Number.isFinite(savedLng)) {
                map.setView([savedLat, savedLng], 16);
                return;
            }

            const cityName = getSelectedText(citySelect.options[citySelect.selectedIndex]);
            const cityState = stateSelect.value.trim();

            if (cityName && cityState) {
                regionMapFromCity(cityName, cityState, 12);
            }
        };

        return {
            map: map,
            setMarker: setMarker,
            refreshByCity: function () {
                const cityName = getSelectedText(citySelect.options[citySelect.selectedIndex]);
                const cityState = stateSelect.value.trim();
                if (cityName && cityState) {
                    regionMapFromCity(cityName, cityState, 12);
                }
            }
        };
    }

    document.addEventListener('DOMContentLoaded', function () {
        const cadastroState = document.getElementById('estado');
        const cadastroCity = document.getElementById('id_cidade');
        const editState = document.getElementById('editar_estado');
        const editCity = document.getElementById('editar_id_cidade');

        if (cadastroState && cadastroCity) {
            const options = Array.from(cadastroCity.querySelectorAll('option[data-estado]'));
            cadastroState.addEventListener('change', function () {
                const selectedState = cadastroState.value.trim();
                cadastroCity.innerHTML = '<option value="">Selecione a cidade</option>';
                cadastroCity.disabled = !selectedState;

                if (!selectedState) {
                    return;
                }

                options.forEach(function (option) {
                    const optionState = (option.getAttribute('data-estado') || '').trim();
                    if (optionState === selectedState) {
                        const clone = document.createElement('option');
                        clone.value = option.value;
                        clone.textContent = option.textContent;
                        clone.setAttribute('data-estado', optionState);
                        cadastroCity.appendChild(clone);
                    }
                });
            });
        }

        if (editState && editCity) {
            const options = Array.from(editCity.querySelectorAll('option[data-estado]'));
            editState.addEventListener('change', function () {
                const selectedState = editState.value.trim();
                editCity.innerHTML = '<option value="">Selecione a cidade</option>';
                editCity.disabled = !selectedState;

                if (!selectedState) {
                    return;
                }

                options.forEach(function (option) {
                    const optionState = (option.getAttribute('data-estado') || '').trim();
                    if (optionState === selectedState) {
                        const clone = document.createElement('option');
                        clone.value = option.value;
                        clone.textContent = option.textContent;
                        clone.setAttribute('data-estado', optionState);
                        editCity.appendChild(clone);
                    }
                });
            });

            const selectedEditCity = editCity.value;
            if (selectedEditCity) {
                const selectedOption = options.find(function (option) {
                    return option.value === selectedEditCity;
                });

                if (selectedOption) {
                    const selectedState = (selectedOption.getAttribute('data-estado') || '').trim();
                    if (selectedState) {
                        editState.value = selectedState;
                        editState.dispatchEvent(new Event('change'));
                    }
                }
            }
        }

        const cadastroMap = buildMap({
            mapId: 'mapaLocalCadastro',
            searchInputId: 'endereco_busca',
            searchButtonId: 'btnBuscarEndereco',
            messageId: 'mapaMensagemCadastro',
            latInputId: 'latitude',
            lngInputId: 'longitude',
            addressInputId: 'endereco',
            stateSelectId: 'estado',
            citySelectId: 'id_cidade',
            modalId: 'cadastrarLocalModal',
            isEditMode: false
        });

        const editMap = buildMap({
            mapId: 'mapaLocalEditar',
            searchInputId: 'editar_endereco_busca',
            searchButtonId: 'btnBuscarEnderecoEditar',
            messageId: 'mapaMensagemEditar',
            latInputId: 'editar_latitude',
            lngInputId: 'editar_longitude',
            addressInputId: 'editar_endereco',
            stateSelectId: 'editar_estado',
            citySelectId: 'editar_id_cidade',
            modalId: 'editarLocalModal',
            isEditMode: true
        });

        const modalCadastrar = document.getElementById('cadastrarLocalModal');
        const modalEditar = document.getElementById('editarLocalModal');

        if (modalCadastrar && cadastroMap && cadastroMap.map) {
            bindMapToModal(modalCadastrar, cadastroMap.map);
        }

        if (modalEditar && editMap && editMap.map) {
            bindMapToModal(modalEditar, editMap.map);
        }

        window.artourLocalMap = {
            cadastro: cadastroMap,
            edicao: editMap
        };
    });
})();
