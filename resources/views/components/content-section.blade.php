{{-- Composant Section de Contenu Unifiée --}}
<div class="content-section {{ $class ?? '' }}">
    @if(isset($title))
        <h2 class="section-title">
            @if(isset($icon))
                <i class="{{ $icon }}"></i>
            @endif
            {{ $title }}
        </h2>
    @endif
    
    {{ $slot }}
</div>

