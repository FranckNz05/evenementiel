@foreach($event->guests as $guest)
<!-- Edit Guest Modal {{ $guest->id }} -->
<div class="modal fade" id="editGuestModal{{ $guest->id }}" tabindex="-1" aria-labelledby="editGuestModalLabel{{ $guest->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editGuestModalLabel{{ $guest->id }}">Modifier l'invité</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('custom-events.guests.update', [$event, $guest]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name{{ $guest->id }}" class="form-label">Nom complet *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name{{ $guest->id }}" name="name" value="{{ old('name', $guest->full_name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email{{ $guest->id }}" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email{{ $guest->id }}" name="email" value="{{ old('email', $guest->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone{{ $guest->id }}" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone{{ $guest->id }}" name="phone" value="{{ old('phone', $guest->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status{{ $guest->id }}" class="form-label">Statut *</label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status{{ $guest->id }}" name="status" required>
                            <option value="pending" {{ old('status', $guest->status) == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="confirmed" {{ old('status', $guest->status) == 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                            <option value="cancelled" {{ old('status', $guest->status) == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                            <option value="attended" {{ old('status', $guest->status) == 'attended' ? 'selected' : '' }}>Présent</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes{{ $guest->id }}" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                 id="notes{{ $guest->id }}" name="notes" rows="2">{{ old('notes', $guest->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
