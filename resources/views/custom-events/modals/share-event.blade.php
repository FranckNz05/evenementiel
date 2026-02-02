<!-- Share Event Modal -->
<div class="modal fade" id="shareEventModal" tabindex="-1" aria-labelledby="shareEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareEventModalLabel">Partager l'événement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <label class="form-label">Lien de partage</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="shareableLink" 
                               value="{{ route('custom-events.invitation', $event->invitation_link) }}" readonly>
                        <button class="btn btn-outline-secondary" type="button" id="copyLinkBtn" data-bs-toggle="tooltip" title="Copier le lien">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <div class="form-text">Partagez ce lien pour permettre aux invités de voir les détails de l'événement.</div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Partager via</label>
                    <div class="d-flex gap-2">
                        <a href="mailto:?subject=Invitation à l'événement : {{ urlencode($event->title) }}&body=Je vous invite à rejoindre mon événement : {{ urlencode(route('custom-events.invitation', $event->invitation_link)) }}" 
                           class="btn btn-outline-primary flex-grow-1" target="_blank">
                            <i class="fas fa-envelope me-2"></i>Email
                        </a>
                        <a href="https://wa.me/?text={{ urlencode('Je vous invite à rejoindre mon événement : ' . $event->title . ' - ' . route('custom-events.invitation', $event->invitation_link)) }}" 
                           class="btn btn-outline-success flex-grow-1" target="_blank">
                            <i class="fab fa-whatsapp me-2"></i>WhatsApp
                        </a>
                    </div>
                </div>
                
                <div class="card border-0 bg-light">
                    <div class="card-body py-2">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <i class="fas fa-qrcode fa-2x text-muted"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">QR Code d'accès</h6>
                                <p class="small text-muted mb-0">Téléchargez le QR Code pour l'impression ou le partage numérique.</p>
                            </div>
                            <div class="flex-shrink-0">
                                <button class="btn btn-sm btn-outline-primary" id="downloadQrCode">
                                    <i class="fas fa-download me-1"></i>Télécharger
                                </button>
                            </div>
                        </div>
                        <div class="text-center mt-3 d-none" id="qrCodePreview">
                            <img src="{{ route('custom-events.qrcode', $event) }}" alt="QR Code" class="img-fluid" style="max-width: 150px;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Copy link to clipboard
        document.getElementById('copyLinkBtn').addEventListener('click', function() {
            const linkInput = document.getElementById('shareableLink');
            linkInput.select();
            document.execCommand('copy');
            
            // Show feedback
            const tooltip = bootstrap.Tooltip.getInstance(this);
            const originalTitle = this.getAttribute('data-bs-original-title');
            tooltip.setContent({ '.tooltip-inner': 'Lien copié !' });
            
            // Reset after delay
            setTimeout(() => {
                tooltip.setContent({ '.tooltip-inner': originalTitle });
            }, 2000);
        });
        
        // Toggle QR code preview
        const qrCodePreview = document.getElementById('qrCodePreview');
        const downloadQrBtn = document.getElementById('downloadQrCode');
        
        downloadQrBtn.addEventListener('mouseenter', function() {
            if (qrCodePreview.classList.contains('d-none')) {
                qrCodePreview.classList.remove('d-none');
                qrCodePreview.classList.add('d-block');
            }
        });
        
        // Download QR code
        downloadQrBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const link = document.createElement('a');
            link.href = '{{ route('custom-events.qrcode', $event) }}';
            link.download = `qr-code-{{ Str::slug($event->title) }}.png`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
        
        // Show QR code when modal is shown
        const shareModal = document.getElementById('shareEventModal');
        shareModal.addEventListener('shown.bs.modal', function () {
            qrCodePreview.classList.remove('d-none');
            qrCodePreview.classList.add('d-block');
        });
        
        // Hide QR code when modal is hidden
        shareModal.addEventListener('hidden.bs.modal', function () {
            qrCodePreview.classList.add('d-none');
            qrCodePreview.classList.remove('d-block');
        });
    });
</script>
@endpush
