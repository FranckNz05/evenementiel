{{-- Composant En-tête de Page Unifié --}}
<div class="page-header fade-in">
    <div class="page-header-content">
        <div>
            <h1 class="page-title">
                @if(isset($icon))
                    <i class="{{ $icon }}"></i>
                @endif
                {{ $title }}
            </h1>
            @if(isset($subtitle))
                <p class="page-subtitle">{{ $subtitle }}</p>
            @endif
        </div>
        
        @if(isset($actions))
            <div class="page-actions">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
