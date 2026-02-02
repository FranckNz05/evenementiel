<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Check-in temps réel - {{ $event->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .checkin-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .event-header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
        }
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .search-section {
            margin-bottom: 30px;
        }
        .guest-list {
            max-height: 600px;
            overflow-y: auto;
        }
        .guest-item {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            transition: all 0.3s;
        }
        .guest-item:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .guest-item.checked-in {
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .qr-scanner-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        #qr-reader {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }
        .alert-fraudulent {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        .alert-valid {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .badge-checked-in {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85rem;
        }
        .badge-pending {
            background-color: #6c757d;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85rem;
        }
        .refresh-indicator {
            display: none;
            color: #28a745;
        }
        .refresh-indicator.active {
            display: inline-block;
        }
        .guest-item {
            transition: all 0.3s ease;
        }
        .guest-item.checked-in {
            border-left: 4px solid #28a745;
        }
        .manual-guest-result {
            cursor: pointer;
            transition: all 0.2s;
        }
        .manual-guest-result:hover {
            background-color: #f0f0f0;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        .loading {
            animation: pulse 1.5s infinite;
        }
    </style>
</head>
<body>
    <div class="checkin-container">
        <!-- Event Header -->
        <div class="event-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">{{ $event->title }}</h1>
                    <p class="text-muted mb-0">
                        <i class="far fa-calendar-alt me-2"></i>
                        @if($event->start_date)
                            {{ $event->start_date->format('d/m/Y à H:i') }}
                            @if($event->end_date)
                                - {{ $event->end_date->format('d/m/Y à H:i') }}
                            @endif
                        @endif
                        @if($event->location)
                            <span class="ms-3">
                                <i class="fas fa-map-marker-alt me-2"></i>{{ $event->location }}
                            </span>
                        @endif
                    </p>
                </div>
                <div>
                    <span class="refresh-indicator" id="refreshIndicator">
                        <i class="fas fa-sync-alt fa-spin me-2"></i>Actualisation...
                    </span>
                    <button class="btn btn-outline-primary" onclick="refreshGuests()">
                        <i class="fas fa-sync-alt me-2"></i>Actualiser
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-card">
            <div class="row">
                <div class="col-md-3 stat-item">
                    <div class="stat-value" id="statTotal">0</div>
                    <div class="stat-label">Total invités</div>
                </div>
                <div class="col-md-3 stat-item">
                    <div class="stat-value" id="statCheckedIn">0</div>
                    <div class="stat-label">Entrés</div>
                </div>
                <div class="col-md-3 stat-item">
                    <div class="stat-value" id="statPending">0</div>
                    <div class="stat-label">En attente</div>
                </div>
                <div class="col-md-3 stat-item">
                    <div class="stat-value" id="statPercentage">0%</div>
                    <div class="stat-label">Taux de présence</div>
                </div>
            </div>
        </div>

        <!-- Search and QR Scanner Section -->
        <div class="row">
            <div class="col-md-8">
                <!-- Search Section -->
                <div class="search-section">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control form-control-lg" id="searchInput" placeholder="Rechercher un invité (nom, email, téléphone)">
                        <button class="btn btn-primary" onclick="searchGuests()">
                            <i class="fas fa-search me-2"></i>Rechercher
                        </button>
                    </div>
                </div>

                <!-- Guest List -->
                <div class="guest-list" id="guestList">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <p class="text-muted mt-2">Chargement de la liste des invités...</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- QR Scanner Section -->
                <div class="qr-scanner-section">
                    <h5 class="mb-3"><i class="fas fa-qrcode me-2"></i>Scanner un QR Code</h5>
                    <div id="qr-reader" style="width:100%"></div>
                    <div class="mt-3">
                        <button class="btn btn-primary w-100" onclick="startQRScanner()">
                            <i class="fas fa-camera me-2"></i>Démarrer le scanner
                        </button>
                        <button class="btn btn-secondary w-100 mt-2" onclick="stopQRScanner()" style="display:none;" id="stopScannerBtn">
                            <i class="fas fa-stop me-2"></i>Arrêter le scanner
                        </button>
                    </div>
                    <div id="qrResult" class="mt-3"></div>
                </div>

                <!-- Manual Check-in -->
                <div class="mt-4">
                    <h5 class="mb-3"><i class="fas fa-user-check me-2"></i>Check-in manuel</h5>
                    <div class="mb-3">
                        <label for="manualGuestSearch" class="form-label small">Rechercher un invité :</label>
                        <input type="text" class="form-control form-control-sm" id="manualGuestSearch" placeholder="Nom, email ou téléphone" onkeyup="searchGuestForManualCheckIn(this.value)">
                        <div id="manualGuestResults" class="mt-2" style="max-height: 200px; overflow-y: auto;"></div>
                    </div>
                </div>
                
                <!-- Instructions -->
                <div class="alert alert-info small mt-4">
                    <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Instructions</h6>
                    <ul class="mb-0 ps-3">
                        <li>Utilisez le scanner QR pour scanner les codes d'invitation</li>
                        <li>Recherchez un invité par nom, email ou téléphone</li>
                        <li>Cliquez sur "Marquer entré" pour un check-in manuel</li>
                        <li>La liste se met à jour automatiquement toutes les 5 secondes</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        const checkinUrl = '{{ $event->checkin_url }}';
        const eventId = {{ $event->id }};
        const csrfToken = '{{ csrf_token() }}';
        let html5QrcodeScanner = null;
        let refreshInterval = null;

        // Charger la liste des invités
        function loadGuests(showLoading = false) {
            if (showLoading) {
                document.getElementById('refreshIndicator').classList.add('active');
            }
            
            fetch(`/checkin/${checkinUrl}/guests`)
                .then(response => response.json())
                .then(data => {
                    updateStats(data.stats);
                    displayGuests(data.guests);
                    if (showLoading) {
                        setTimeout(() => {
                            document.getElementById('refreshIndicator').classList.remove('active');
                        }, 500);
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des invités:', error);
                    if (showLoading) {
                        document.getElementById('refreshIndicator').classList.remove('active');
                    }
                    showNotification('Erreur lors du chargement des invités', 'error');
                });
        }

        // Mettre à jour les statistiques
        function updateStats(stats) {
            document.getElementById('statTotal').textContent = stats.total;
            document.getElementById('statCheckedIn').textContent = stats.checked_in;
            document.getElementById('statPending').textContent = stats.pending;
            
            const percentage = stats.total > 0 ? Math.round((stats.checked_in / stats.total) * 100) : 0;
            document.getElementById('statPercentage').textContent = percentage + '%';
        }

        // Afficher la liste des invités
        function displayGuests(guests) {
            const guestList = document.getElementById('guestList');
            guestList.innerHTML = '';

            if (guests.length === 0) {
                guestList.innerHTML = '<div class="text-center py-5"><p class="text-muted">Aucun invité trouvé</p></div>';
                return;
            }

            guests.forEach(guest => {
                const guestItem = document.createElement('div');
                guestItem.className = `guest-item ${guest.is_checked_in ? 'checked-in' : ''}`;
                guestItem.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${guest.full_name}</h6>
                            <small class="text-muted">
                                ${guest.email ? `<i class="fas fa-envelope me-1"></i>${guest.email}` : ''}
                                ${guest.phone ? `<i class="fas fa-phone me-2 ms-2"></i>${guest.phone}` : ''}
                            </small>
                            ${guest.checked_in_via ? `<small class="d-block text-muted mt-1"><i class="fas fa-${guest.checked_in_via === 'qrcode' ? 'qrcode' : 'user-check'} me-1"></i>Entré via ${guest.checked_in_via === 'qrcode' ? 'QR Code' : 'manuel'}</small>` : ''}
                        </div>
                        <div class="text-end">
                            ${guest.is_checked_in 
                                ? `<span class="badge-checked-in"><i class="fas fa-check me-1"></i>Entré${guest.checked_in_at ? ' le ' + guest.checked_in_at : ''}</span>`
                                : `<span class="badge-pending">En attente</span>`
                            }
                            ${!guest.is_checked_in 
                                ? `<button class="btn btn-sm btn-success ms-2" onclick="manualCheckInById(${guest.id}, this)">
                                    <i class="fas fa-check me-1"></i>Marquer entré
                                   </button>`
                                : ''
                            }
                        </div>
                    </div>
                `;
                guestList.appendChild(guestItem);
            });
        }

        // Rechercher des invités
        function searchGuests() {
            const query = document.getElementById('searchInput').value;
            
            if (!query) {
                loadGuests();
                return;
            }

            fetch(`/checkin/${checkinUrl}/search?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    displayGuests(data.guests);
                })
                .catch(error => {
                    console.error('Erreur lors de la recherche:', error);
                });
        }

        // Check-in manuel par ID
        function manualCheckInById(guestId, buttonElement = null) {
            // Désactiver le bouton pour éviter les clics multiples
            if (buttonElement) {
                buttonElement.disabled = true;
                const originalHtml = buttonElement.innerHTML;
                buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>...';
            }
            
            fetch(`/checkin/${checkinUrl}/manual`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ guest_id: guestId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Afficher un message de succès
                    showNotification(data.message, 'success');
                    loadGuests();
                    // Vider le champ de recherche
                    const searchInput = document.getElementById('manualGuestSearch');
                    if (searchInput) {
                        searchInput.value = '';
                    }
                    const resultsDiv = document.getElementById('manualGuestResults');
                    if (resultsDiv) {
                        resultsDiv.innerHTML = '';
                    }
                } else {
                    showNotification(data.message || 'Erreur lors du check-in', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur lors du check-in:', error);
                showNotification('Erreur lors du check-in', 'error');
            })
            .finally(() => {
                if (buttonElement) {
                    buttonElement.disabled = false;
                    buttonElement.innerHTML = '<i class="fas fa-check"></i>';
                }
            });
        }
        
        // Afficher une notification
        function showNotification(message, type) {
            // Supprimer les notifications existantes
            const existingNotifications = document.querySelectorAll('.checkin-notification');
            existingNotifications.forEach(n => n.remove());
            
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'danger'} alert-dismissible fade show position-fixed checkin-notification`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);';
            notification.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'times-circle'} me-2"></i>
                    <div class="flex-grow-1">${message}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            document.body.appendChild(notification);
            
            // Supprimer automatiquement après 5 secondes
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.classList.remove('show');
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }
            }, 5000);
        }

        // Stocker les données des invités recherchés
        let searchedGuests = [];
        let searchTimeout = null;
        
        // Rechercher un invité pour check-in manuel
        function searchGuestForManualCheckIn(query) {
            clearTimeout(searchTimeout);
            const resultsDiv = document.getElementById('manualGuestResults');
            
            if (!query || query.length < 2) {
                resultsDiv.innerHTML = '';
                searchedGuests = [];
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch(`/checkin/${checkinUrl}/search?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.guests && data.guests.length > 0) {
                            // Stocker les invités pour la sélection
                            searchedGuests = data.guests;
                            
                            resultsDiv.innerHTML = data.guests.map(guest => {
                                const guestNameEscaped = guest.full_name.replace(/'/g, "\\'").replace(/"/g, '&quot;').replace(/\n/g, ' ');
                                return `
                                <div class="card card-sm mb-2 ${guest.is_checked_in ? 'border-success bg-light' : 'border-light'}" style="cursor: ${guest.is_checked_in ? 'default' : 'pointer'}; transition: all 0.2s;" ${guest.is_checked_in ? '' : `onclick="selectGuestForCheckIn(${guest.id}, '${guestNameEscaped}', event)" onmouseover="if(!this.classList.contains('border-success')) this.style.backgroundColor='#f8f9fa'" onmouseout="if(!this.classList.contains('border-success')) this.style.backgroundColor=''"`}>
                                    <div class="card-body p-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="flex-grow-1">
                                                <strong>${guest.full_name}</strong>
                                                ${guest.email ? `<br><small class="text-muted">${guest.email}</small>` : ''}
                                                ${guest.phone ? `<br><small class="text-muted">${guest.phone}</small>` : ''}
                                            </div>
                                            ${guest.is_checked_in 
                                                ? '<span class="badge bg-success ms-2"><i class="fas fa-check me-1"></i>Déjà entré</span>'
                                                : `<button class="btn btn-sm btn-success ms-2" onclick="event.stopPropagation(); selectGuestForCheckIn(${guest.id}, '${guestNameEscaped}', event)" title="Marquer comme entré"><i class="fas fa-check"></i></button>`
                                            }
                                        </div>
                                    </div>
                                </div>
                            `;
                            }).join('');
                        } else {
                            searchedGuests = [];
                            resultsDiv.innerHTML = '<div class="text-muted small">Aucun invité trouvé</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la recherche:', error);
                        searchedGuests = [];
                        resultsDiv.innerHTML = '<div class="text-danger small">Erreur lors de la recherche</div>';
                    });
            }, 300);
        }
        
        // Sélectionner un invité pour check-in
        function selectGuestForCheckIn(guestId, guestName, event) {
            // Empêcher la propagation de l'événement
            if (event) {
                event.stopPropagation();
            }
            
            // Trouver l'invité dans les résultats de recherche
            const guest = searchedGuests.find(g => g.id === guestId);
            
            // Si l'invité est déjà entré, ne rien faire
            if (guest && guest.is_checked_in) {
                showNotification('Cet invité est déjà marqué comme entré', 'warning');
                return;
            }
            
            // Vider le champ de recherche et les résultats
            const searchInput = document.getElementById('manualGuestSearch');
            if (searchInput) {
                searchInput.value = guestName;
            }
            const resultsDiv = document.getElementById('manualGuestResults');
            if (resultsDiv) {
                resultsDiv.innerHTML = '';
            }
            
            // Procéder au check-in
            manualCheckInById(guestId);
        }

        // Démarrer le scanner QR
        function startQRScanner() {
            if (html5QrcodeScanner) {
                return;
            }

            html5QrcodeScanner = new Html5Qrcode("qr-reader");
            const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                scanQrCode(decodedText);
            };
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            html5QrcodeScanner.start(
                { facingMode: "environment" },
                config,
                qrCodeSuccessCallback
            ).then(() => {
                document.getElementById('stopScannerBtn').style.display = 'block';
            }).catch(err => {
                console.error('Erreur lors du démarrage du scanner:', err);
                alert('Impossible de démarrer le scanner. Vérifiez les permissions de la caméra.');
            });
        }

        // Arrêter le scanner QR
        function stopQRScanner() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop().then(() => {
                    html5QrcodeScanner.clear();
                    html5QrcodeScanner = null;
                    document.getElementById('stopScannerBtn').style.display = 'none';
                    document.getElementById('qrResult').innerHTML = '';
                }).catch(err => {
                    console.error('Erreur lors de l\'arrêt du scanner:', err);
                });
            }
        }

        // Scanner un QR code
        function scanQrCode(qrCode) {
            // Afficher un indicateur de chargement
            const qrResult = document.getElementById('qrResult');
            qrResult.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin me-2"></i>Validation du QR code...</div>';
            
            fetch(`/checkin/${checkinUrl}/scan`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ qr_code: qrCode })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Erreur lors de la validation');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.valid) {
                    // Afficher le résultat dans la zone QR
                    qrResult.innerHTML = `
                        <div class="alert alert-valid">
                            <h6><i class="fas fa-check-circle me-2"></i>${data.message}</h6>
                            ${data.guest ? `
                                <p class="mb-0">
                                    <strong>Invité:</strong> ${data.guest.full_name}<br>
                                    ${data.guest.email ? `<strong>Email:</strong> ${data.guest.email}<br>` : ''}
                                    ${data.guest.phone ? `<strong>Téléphone:</strong> ${data.guest.phone}<br>` : ''}
                                    ${data.guest.checked_in_at ? `<strong>Check-in:</strong> ${data.guest.checked_in_at}` : ''}
                                </p>
                            ` : ''}
                            ${data.already_checked_in ? '<small class="text-warning d-block mt-2"><i class="fas fa-exclamation-triangle me-1"></i>Cet invité était déjà marqué comme entré.</small>' : ''}
                        </div>
                    `;
                    
                    // Afficher une notification
                    showNotification(data.message, data.already_checked_in ? 'warning' : 'success');
                    
                    // Recharger la liste
                    loadGuests();
                    
                    // Arrêter le scanner après un scan réussi (sauf si déjà entré)
                    if (html5QrcodeScanner && !data.already_checked_in) {
                        setTimeout(() => {
                            stopQRScanner();
                        }, 2000);
                    }
                } else {
                    // Afficher une erreur
                    qrResult.innerHTML = `
                        <div class="alert alert-fraudulent">
                            <h6><i class="fas fa-times-circle me-2"></i>${data.message}</h6>
                            ${data.fraudulent ? '<small class="text-danger d-block mt-2"><i class="fas fa-exclamation-triangle me-1"></i>Ce code QR est invalide ou frauduleux.</small>' : ''}
                            ${data.guest ? `<p class="mb-0 mt-2"><strong>Invitation trouvée pour:</strong> ${data.guest.full_name}</p>` : ''}
                        </div>
                    `;
                    showNotification(data.message, 'error');
                }

                // Réinitialiser après 5 secondes
                setTimeout(() => {
                    qrResult.innerHTML = '';
                }, 5000);
            })
            .catch(error => {
                console.error('Erreur lors du scan:', error);
                const qrResult = document.getElementById('qrResult');
                qrResult.innerHTML = `
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Erreur</h6>
                        <p class="mb-0">${error.message || 'Une erreur est survenue lors du scan du QR code.'}</p>
                    </div>
                `;
                showNotification(error.message || 'Erreur lors du scan du QR code', 'error');
                
                // Réinitialiser après 5 secondes
                setTimeout(() => {
                    qrResult.innerHTML = '';
                }, 5000);
            });
        }

        // Actualiser la liste
        function refreshGuests() {
            loadGuests(true);
        }

        // Recherche en temps réel
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchGuests();
            }
        });

        // Charger les invités au chargement de la page
        loadGuests();

        // Actualiser automatiquement toutes les 5 secondes
        refreshInterval = setInterval(() => {
            loadGuests();
        }, 5000);

        // Nettoyer à la fermeture
        window.addEventListener('beforeunload', () => {
            if (html5QrcodeScanner) {
                stopQRScanner();
            }
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });
    </script>
</body>
</html>

