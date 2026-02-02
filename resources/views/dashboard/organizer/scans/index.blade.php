@extends('layouts.dashboard')

@section('title', 'Scanner')

@section('content')
<div class="container-fluid">
    <!-- Section Scanner -->
    <div class="mb-4">
        <h5 class="mb-3">
            <i class="fas fa-qrcode me-2 text-primary"></i>
            Scanner un billet
        </h5>
        <div class="row">
            <div class="col-md-6">
                <div class="text-center mb-4">
                    <video id="qr-scanner" width="100%" style="border: 1px solid #ddd; border-radius: 8px;"></video>
                </div>
                <div class="form-group">
                    <label for="manual-code" class="form-label">Ou saisir le code manuellement :</label>
                    <form id="scanForm" action="{{ route('scans.verify') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="text" id="manual-code" name="qr_code" class="form-control" placeholder="Entrez le code QR" required>
                            <button type="submit" id="verify-btn" class="btn btn-primary">
                                <i class="fas fa-check me-1"></i>Vérifier
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div id="scan-result" class="p-3" style="display: none;">
                    <div id="result-content"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Historique -->
    <div class="mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                <i class="fas fa-history me-2 text-primary"></i>
                Historique des scans récents
            </h5>
            <a href="{{ route('scans.history') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-list me-1"></i>Voir tout l'historique
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable">
                <thead class="table-light">
                    <tr>
                        <th>Date/Heure</th>
                        <th>Événement</th>
                        <th>Billet</th>
                        <th>Client</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($scans as $scan)
                    <tr>
                        <td>{{ $scan->scanned_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $scan->ticket->event->title ?? 'N/A' }}</td>
                        <td>{{ $scan->ticket->name ?? 'N/A' }}</td>
                        <td>{{ $scan->order->user->name ?? 'N/A' }}</td>
                        <td>
                            @if($scan->is_valid)
                                <span class="badge bg-success">Valide</span>
                            @else
                                <span class="badge bg-danger">Invalide</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            Aucun scan récent
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($scans->hasPages())
        <div class="mt-4">
            {{ $scans->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
    $(document).ready(function() {
        const video = document.getElementById("qr-scanner");
        const canvas = document.createElement("canvas");
        const ctx = canvas.getContext("2d");
        let scanning = false;

        // Démarrer le scanner
        function startScanner() {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
                .then(function(stream) {
                    video.srcObject = stream;
                    video.play();
                    scanning = true;
                    scanQRCode();
                })
                .catch(function(err) {
                    console.error("Erreur d'accès à la caméra: ", err);
                    alert("Impossible d'accéder à la caméra. Veuillez vérifier les permissions.");
                });
        }

        // Scanner le QR code
        // Scanner le QR code
function scanQRCode() {
    if (!scanning) return;

    if (video.readyState === video.HAVE_ENOUGH_DATA) {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height);

        if (code) {
            $('#manual-code').val(code.data);
            $('#scanForm').submit();
        }
    }

    requestAnimationFrame(scanQRCode);
}

// Gestion du formulaire
$('#scanForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            displayResult(response);
        },
        error: function(xhr) {
            displayResult(xhr.responseJSON || { 
                valid: false, 
                message: "Erreur lors de la vérification" 
            });
        }
    });
});

        // Afficher le résultat
        function displayResult(result) {
            const $result = $('#scan-result');
            const $content = $('#result-content');

            $content.html(`
                <div class="alert alert-${result.valid ? 'success' : 'danger'}">
                    ${result.message}
                </div>
                ${result.payment ? `
                <div class="card mt-3">
                    <div class="card-body-modern">
                        <h5 class="card-title">Détails du billet</h5>
                        <p><strong>Événement:</strong> ${result.payment.ticket.event.title}</p>
                        <p><strong>Billet:</strong> ${result.payment.ticket.name}</p>
                        <p><strong>Client:</strong> ${result.payment.user.name}</p>
                        <p><strong>Prix:</strong> ${result.payment.montant} FCFA</p>
                        <p><strong>Date d'achat:</strong> ${new Date(result.payment.created_at).toLocaleString()}</p>
                    </div>
                </div>
                ` : ''}
            `);

            $result.show();
        }

        // Démarrer le scanner au chargement
        startScanner();
    });

    // Dans votre JavaScript
$('#scanForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.valid) {
                // Afficher succès
                alert('Billet valide pour ' + response.ticket.user);
            } else {
                // Afficher erreur
                alert(response.message);
            }
        },
        error: function(xhr) {
            alert('Erreur lors de la vérification du billet');
            console.error(xhr.responseText);
        }
    });
});
</script>
@endpush
