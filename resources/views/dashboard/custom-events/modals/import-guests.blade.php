<!-- Import Guests Modal -->
<div class="modal fade" id="importGuestsModal" tabindex="-1" aria-labelledby="importGuestsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importGuestsModalLabel">Importer des invités</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('custom-events.guests.import', $event) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Instructions d'importation</h6>
                        <p class="mb-0">Importez une liste d'invités à partir d'un fichier CSV ou Excel. Le fichier doit contenir les colonnes suivantes : <strong>Nom complet</strong> (obligatoire), <strong>Email</strong>, <strong>Téléphone</strong>.</p>
                        <p class="mb-0 mt-2">
                            <a href="{{ route('custom-events.invitations.csv-template') }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i>Télécharger le modèle CSV
                            </a>
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label for="importFile" class="form-label">Fichier à importer *</label>
                        <input class="form-control @error('import_file') is-invalid @enderror" type="file" id="importFile" name="import_file" accept=".csv, .xlsx, .xls" required>
                        @error('import_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Formats acceptés : .csv, .xlsx, .xls (taille max : 2MB)</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="hasHeaders" name="has_headers" checked>
                            <label class="form-check-label" for="hasHeaders">
                                La première ligne du fichier contient des en-têtes de colonnes
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="sendInvitationsAfterImport" name="send_invitations" value="1">
                            <label class="form-check-label" for="sendInvitationsAfterImport">
                                Envoyer automatiquement les invitations après l'importation
                            </label>
                        </div>
                        <div class="ms-4 mt-2" id="invitationMethodContainer" style="display: none;">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="invitation_method" id="inviteByEmail" value="email" checked>
                                <label class="form-check-label" for="inviteByEmail">
                                    <i class="fas fa-envelope me-1"></i>Par email
                                </label>
                            </div>
                            @if($event->canScheduleSms())
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="invitation_method" id="inviteBySms" value="sms">
                                <label class="form-check-label" for="inviteBySms">
                                    <i class="fas fa-sms me-1 text-info"></i>Par SMS
                                </label>
                            </div>
                            @endif
                            @php $supportsWhatsapp = in_array('whatsapp', $event->offer_capabilities['support'] ?? []); @endphp
                            @if($supportsWhatsapp)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="invitation_method" id="inviteByWhatsApp" value="whatsapp">
                                <label class="form-check-label" for="inviteByWhatsApp">
                                    <i class="fab fa-whatsapp me-1 text-success"></i>Par WhatsApp
                                </label>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="alert alert-warning small mb-0">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        L'importation d'un grand nombre d'invités peut prendre quelques instants. Ne fermez pas cette page pendant l'opération.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-import me-1"></i>Importer les invités
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Show/hide invitation method options based on checkbox
    document.getElementById('sendInvitationsAfterImport').addEventListener('change', function() {
        const container = document.getElementById('invitationMethodContainer');
        container.style.display = this.checked ? 'block' : 'none';
    });
    
    // Initialize visibility on page load
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('invitationMethodContainer');
        const checkbox = document.getElementById('sendInvitationsAfterImport');
        container.style.display = checkbox.checked ? 'block' : 'none';
    });
</script>
@endpush
