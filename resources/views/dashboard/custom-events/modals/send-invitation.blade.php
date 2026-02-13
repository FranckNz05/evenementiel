@foreach($event->guests as $guest)
<!-- Send Invitation Modal {{ $guest->id }} -->
<div class="modal fade" id="sendInvitationModal{{ $guest->id }}" tabindex="-1" aria-labelledby="sendInvitationModalLabel{{ $guest->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendInvitationModalLabel{{ $guest->id }}">Envoyer une invitation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('custom-events.invitations.send', [$event, $guest]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Envoyer une invitation à <strong>{{ $guest->full_name }}</strong> via :</p>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="invitation_method" id="emailInvite{{ $guest->id }}" value="email" checked>
                            <label class="form-check-label" for="emailInvite{{ $guest->id }}">
                                <i class="fas fa-envelope me-2"></i>Email @if($guest->email)({{ $guest->email }})@else<span class="text-danger">(Aucun email fourni)</span>@endif
                            </label>
                        </div>
                        @if(!$guest->email)
                            <div class="ms-4 mt-2">
                                <label for="emailTemp{{ $guest->id }}" class="form-label small">Adresse email :</label>
                                <input type="email" class="form-control form-control-sm" id="emailTemp{{ $guest->id }}" name="email_temp">
                            </div>
                        @endif
                    </div>
                    
                    @if($event->canScheduleSms())
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="invitation_method" id="smsInvite{{ $guest->id }}" value="sms">
                            <label class="form-check-label" for="smsInvite{{ $guest->id }}">
                                <i class="fas fa-sms me-2 text-info"></i>SMS @if($guest->phone)({{ $guest->phone }})@else<span class="text-danger">(Aucun téléphone fourni)</span>@endif
                            </label>
                        </div>
                        @if(!$guest->phone)
                            <div class="ms-4 mt-2">
                                <label for="phoneTempSms{{ $guest->id }}" class="form-label small">Numéro de téléphone :</label>
                                <input type="tel" class="form-control form-control-sm" id="phoneTempSms{{ $guest->id }}" name="phone_temp">
                            </div>
                        @endif
                    </div>
                    @endif
                    
                    @php $supportsWhatsapp = in_array('whatsapp', $event->offer_capabilities['support'] ?? []); @endphp
                    @if($supportsWhatsapp)
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="invitation_method" id="whatsappInvite{{ $guest->id }}" value="whatsapp">
                            <label class="form-check-label" for="whatsappInvite{{ $guest->id }}">
                                <i class="fab fa-whatsapp me-2 text-success"></i>WhatsApp @if($guest->phone)({{ $guest->phone }})@else<span class="text-danger">(Aucun téléphone fourni)</span>@endif
                            </label>
                        </div>
                        @if(!$guest->phone)
                            <div class="ms-4 mt-2">
                                <label for="phoneTemp{{ $guest->id }}" class="form-label small">Numéro de téléphone :</label>
                                <input type="tel" class="form-control form-control-sm" id="phoneTemp{{ $guest->id }}" name="phone_temp">
                            </div>
                        @endif
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <label for="message{{ $guest->id }}" class="form-label">Message personnalisé (optionnel)</label>
                        <textarea class="form-control" id="message{{ $guest->id }}" name="message" rows="3">Bonjour {{ $guest->full_name }}, vous êtes invité à l'événement "{{ $event->title }}"@if($event->start_date) le {{ $event->start_date->format('d/m/Y à H:i') }}@endif.@if($event->invitation_link) Cliquez sur le lien pour plus de détails : {{ route('custom-events.invitation', $event->invitation_link) }}@endif</textarea>
                        <div class="form-text">Pour SMS: message simple. Pour WhatsApp: message avec QR code crypté.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="scheduledAt{{ $guest->id }}" class="form-label">Planifier l'envoi (optionnel)</label>
                        <input type="datetime-local" class="form-control" id="scheduledAt{{ $guest->id }}" name="scheduled_at" min="{{ now()->format('Y-m-d\TH:i') }}">
                        <div class="form-text">Laissez vide pour envoyer immédiatement.</div>
                    </div>
                    
                    <div class="alert alert-info small mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Pour WhatsApp, un QR code avec code crypté sera inclus automatiquement. Pour SMS, un message simple sera envoyé.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" @if(!$guest->email && !$guest->phone) disabled @endif>
                        <i class="fas fa-paper-plane me-1"></i>Envoyer l'invitation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
