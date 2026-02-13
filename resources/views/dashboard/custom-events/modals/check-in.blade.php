<!-- Check-In Modal -->
<div class="modal fade" id="checkInModal" tabindex="-1" aria-labelledby="checkInModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="checkInModalLabel">Scanner un QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div id="scanner-container" class="border rounded p-2 mb-3" style="position: relative; width: 100%; height: 300px; overflow: hidden;">
                        <video id="qr-video" style="width: 100%; height: 100%; object-fit: cover;"></video>
                        <div id="scanner-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 70%; height: 200px; border: 3px solid #0d6efd; border-radius: 8px;"></div>
                        </div>
                    </div>
                    <p class="text-muted small">Positionnez le QR Code dans le cadre pour scanner</p>
                </div>
                
                <div id="scan-result" class="d-none">
                    <div class="alert alert-success">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle fa-2x me-3"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Invité trouvé</h6>
                                <p class="mb-0" id="guest-name">Nom de l'invité</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button id="check-in-btn" class="btn btn-primary">
                            <i class="fas fa-check-circle me-1"></i>Confirmer la présence
                        </button>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <button id="manual-checkin-toggle" class="btn btn-link text-decoration-none">
                        <i class="fas fa-keyboard me-1"></i>Saisie manuelle
                    </button>
                </div>
                
                <div id="manual-checkin-form" class="mt-3" style="display: none;">
                    <div class="mb-3">
                        <label for="ticket-code" class="form-label">Code du billet</label>
                        <input type="text" class="form-control" id="ticket-code" placeholder="Saisissez le code du billet">
                    </div>
                    <div class="d-grid">
                        <button id="verify-ticket" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>Vérifier le billet
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qr-scanner@1.4.2/qr-scanner.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let qrScanner;
        const modal = document.getElementById('checkInModal');
        
        // Initialize QR Scanner when modal is shown
        modal.addEventListener('shown.bs.modal', function () {
            const videoElem = document.getElementById('qr-video');
            
            // Check for camera permissions
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                .then(function(stream) {
                    // Camera access granted
                    qrScanner = new QrScanner(
                        videoElem,
                        result => handleQrCode(result),
                        {
                            preferredCamera: 'environment', // Use back camera by default
                            highlightScanRegion: true,
                            highlightCodeOutline: true,
                        }
                    );
                    qrScanner.start();
                    
                    // Clean up when modal is closed
                    modal.addEventListener('hidden.bs.modal', function () {
                        if (qrScanner) {
                            qrScanner.stop();
                            qrScanner.destroy();
                            qrScanner = null;
                        }
                        resetScanner();
                    });
                })
                .catch(function(err) {
                    console.error('Error accessing camera:', err);
                    alert('Impossible d\'accéder à la caméra. Veuillez vérifier les permissions.');
                });
        });
        
        // Toggle manual check-in form
        document.getElementById('manual-checkin-toggle').addEventListener('click', function() {
            const form = document.getElementById('manual-checkin-form');
            const scanner = document.getElementById('scanner-container');
            
            if (form.style.display === 'none') {
                form.style.display = 'block';
                scanner.style.display = 'none';
                this.innerHTML = '<i class="fas fa-qrcode me-1"></i>Scanner un QR Code';
                
                if (qrScanner) {
                    qrScanner.stop();
                }
            } else {
                form.style.display = 'none';
                scanner.style.display = 'block';
                this.innerHTML = '<i class="fas fa-keyboard me-1"></i>Saisie manuelle';
                
                if (qrScanner) {
                    qrScanner.start();
                }
            }
        });
        
        // Handle QR code scan result
        function handleQrCode(result) {
            console.log('QR Code detected:', result);
            
            // Stop the scanner
            if (qrScanner) {
                qrScanner.stop();
            }
            
            // Show the scan result
            document.getElementById('scanner-container').classList.add('d-none');
            document.getElementById('scan-result').classList.remove('d-none');
            
            // Extract guest ID from the QR code data
            // Format: event-{event_id}-guest-{guest_id}
            const match = result.data.match(/event-\d+-guest-(\d+)/);
            if (match && match[1]) {
                const guestId = match[1];
                
                // Fetch guest details
                fetch(`/api/events/{{ $event->id }}/guests/${guestId}`)
                    .then(response => response.json())
                    .then(guest => {
                        document.getElementById('guest-name').textContent = guest.name;
                        
                        // Set up check-in button
                        const checkInBtn = document.getElementById('check-in-btn');
                        checkInBtn.onclick = function() {
                            recordCheckIn(guestId);
                        };
                    })
                    .catch(error => {
                        console.error('Error fetching guest:', error);
                        alert('Erreur lors de la récupération des informations de l\'invité.');
                        resetScanner();
                    });
            } else {
                alert('QR Code invalide. Veuillez scanner un billet valide pour cet événement.');
                resetScanner();
            }
        }
        
        // Record check-in
        function recordCheckIn(guestId) {
            fetch(`/api/events/{{ $event->id }}/guests/${guestId}/check-in`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Présence enregistrée avec succès !');
                    window.location.reload(); // Refresh to update the UI
                } else {
                    throw new Error(data.message || 'Erreur lors de l\'enregistrement de la présence');
                }
            })
            .catch(error => {
                console.error('Error checking in guest:', error);
                alert('Erreur: ' + (error.message || 'Impossible d\'enregistrer la présence'));
                resetScanner();
            });
        }
        
        // Reset scanner to initial state
        function resetScanner() {
            document.getElementById('scanner-container').classList.remove('d-none');
            document.getElementById('scan-result').classList.add('d-none');
            document.getElementById('manual-checkin-form').style.display = 'none';
            document.getElementById('manual-checkin-toggle').innerHTML = '<i class="fas fa-keyboard me-1"></i>Saisie manuelle';
            
            if (qrScanner) {
                qrScanner.start();
            }
        }
        
        // Handle manual ticket verification
        document.getElementById('verify-ticket').addEventListener('click', function() {
            const ticketCode = document.getElementById('ticket-code').value.trim();
            
            if (!ticketCode) {
                alert('Veuillez saisir un code de billet.');
                return;
            }
            
            // Validate ticket code format (event-{id}-guest-{id})
            const match = ticketCode.match(/event-(\d+)-guest-(\d+)/);
            if (!match || match[1] !== '{{ $event->id }}') {
                alert('Code de billet invalide pour cet événement.');
                return;
            }
            
            const guestId = match[2];
            
            // Fetch guest details
            fetch(`/api/events/{{ $event->id }}/guests/${guestId}`)
                .then(response => response.json())
                .then(guest => {
                    if (confirm(`Confirmer la présence de ${guest.name} ?`)) {
                        return recordCheckIn(guestId);
                    }
                })
                .catch(error => {
                    console.error('Error verifying ticket:', error);
                    alert('Billet non trouvé ou invalide.');
                });
        });
    });
</script>
@endpush
