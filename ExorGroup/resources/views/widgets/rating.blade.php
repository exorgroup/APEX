{{-- resources/views/apex/widgets/rating.blade.php --}}
<div 
    id="{{ $id }}" 
    class="apex-rating {{ $cssClass }} {{ $orientation === 'vertical' ? 'apex-rating-vertical' : 'apex-rating-horizontal' }}"
    style="--rating-height: {{ $height }}px;"
>
    @foreach($indicators as $indicator)
        <span class="apex-rating-indicator">
            {!! $indicator !!}
        </span>
    @endforeach
</div>

<style>
.apex-rating {
    display: flex;
    gap: 2px;
    align-items: center;
}

.apex-rating-horizontal {
    flex-direction: row;
}

.apex-rating-vertical {
    flex-direction: column;
}

.apex-rating-indicator {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}

.apex-rating-indicator svg {
    display: block;
    width: var(--rating-height);
    height: var(--rating-height);
}

/* Partial fill container styling */
.rating-indicator-container {
    display: inline-block;
    transform-origin: center center;
}

.vertical-container {
    transform-origin: center center !important;
}

.rating-filled {
    clip-path: inset(0);
}

.rating-unfilled {
    z-index: -1;
}

/* Vertical orientation specific styles */
.apex-rating-vertical .rating-indicator-container,
.apex-rating-vertical .apex-rating-indicator > div {
    transform-origin: center center;
}

/* Ensure consistent sizing */
.apex-rating-indicator svg * {
    vector-effect: non-scaling-stroke;
}
</style>