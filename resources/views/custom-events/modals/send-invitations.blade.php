<!-- Send Invitations to All Guests Modal -->
<div class="modal fade" id="sendInvitationsModal" tabindex="-1" aria-labelledby="sendInvitationsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendInvitationsModalLabel">Envoyer des invitations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('custom-events.invitations.send-all', $event) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Envoyer des invitations à tous les invités :</p>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="invitation_methods[]" id="emailAllInvites" value="email" checked>
                            <label class="form-check-label" for="emailAllInvites">
                                <i class="fas fa-envelope me-2"></i>Par email ({{ $event->guests->whereNotNull('email')->count() }} invités avec email)
                            </label>
                        </div>
                    </div>
                    
                    @php $supportsSms = $event->canScheduleSms(); @endphp
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="invitation_methods[]" id="smsAllInvites" value="sms" {{ $supportsSms ? '' : 'disabled' }}>
                            <label class="form-check-label" for="smsAllInvites">
                                <i class="fas fa-sms me-2 text-info"></i>Par SMS ({{ $event->guests->whereNotNull('phone')->count() }} invités avec téléphone)
                                @unless($supportsSms)
                                    <span class="text-muted">(disponible à partir de Standard)</span>
                                @endunless
                            </label>
                        </div>
                    </div>
                    
                    @php $supportsWhatsapp = in_array('whatsapp', $event->offer_capabilities['support'] ?? []); @endphp
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="invitation_methods[]" id="whatsappAllInvites" value="whatsapp" {{ $supportsWhatsapp ? '' : 'disabled' }}>
                            <label class="form-check-label" for="whatsappAllInvites">
                                <i class="fab fa-whatsapp me-2 text-success"></i>Par WhatsApp ({{ $event->guests->whereNotNull('phone')->count() }} invités avec téléphone)
                                @unless($supportsWhatsapp)
                                    <span class="text-muted">(disponible à partir de Standard)</span>
                                @endunless
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="invitationStatus" class="form-label">Cibler les invités avec le statut :</label>
                        <select class="form-select" id="invitationStatus" name="status_filter">
                            <option value="all">Tous les invités</option>
                            <option value="pending">En attente uniquement</option>
                            <option value="confirmed">Confirmés uniquement</option>
                            <option value="cancelled">Annulés uniquement</option>
                            <option value="not_invited">Pas encore invités</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="invitationMessage" class="form-label">Message personnalisé (optionnel)</label>
                        <textarea class="form-control" id="invitationMessage" name="message" rows="4">Bonjour {{ '{' }}{{ '{' }}nom{{ '}' }}{{ '}' }}, vous êtes invité à l'événement "{{ $event->title }}"@if($event->start_date) le {{ $event->start_date->format('d/m/Y à H:i') }}@endif.@if($event->invitation_link) Cliquez sur le lien pour plus de détails : {{ route('custom-events.invitation', $event->invitation_link) }}@endif</textarea>
                        <div class="form-text">Utilisez {{ '{' }}{{ '{' }}nom{{ '}' }}{{ '}' }} ou {{ '{' }}{{ '{' }}Nom prénom{{ '}' }}{{ '}' }} pour insérer le nom de l'invité.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="scheduledAt" class="form-label">Planifier l'envoi (optionnel)</label>
                        <input type="datetime-local" class="form-control" id="scheduledAt" name="scheduled_at" min="{{ now()->format('Y-m-d\TH:i') }}">
                        <div class="form-text">Laissez vide pour envoyer immédiatement. Sinon, sélectionnez une date et heure future.</div>
                    </div>
                    
                    <div class="alert alert-warning small mb-0">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Êtes-vous sûr de vouloir envoyer ces invitations ? Cette action enverra des messages à plusieurs destinataires.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" {{ $event->canScheduleSms() ? '' : 'disabled title=\'Programmation/envoi avancé disponible à partir de Standard\'' }}>
                        <i class="fas fa-paper-plane me-1"></i>Envoyer les invitations
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
