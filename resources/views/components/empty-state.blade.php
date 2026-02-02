{{-- Composant État Vide --}}
<div class="empty-state">
    <div class="empty-state-icon">
        <i class="{{ $icon ?? 'fas fa-inbox' }}"></i>
    </div>
    <h3 class="empty-state-title">{{ $title }}</h3>
    <p class="empty-state-message">{{ $message }}</p>
    
    @if(isset($action))
        <div>
            {{ $action }}
        </div>
    @endif
</div>

