<div id="{{ $id }}" 
     class="apex-pro-status-bar {{ $containerClass }}"
     style="background: white;
            border-radius: {{ $borderRadius }};
            border: {{ $borderWidth }} solid {{ $borderColor }};
            padding: {{ $padding }};
            @if($shadow) box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); @endif
            @if($legendPosition === 'left' || $legendPosition === 'right') display: flex; align-items: flex-start; @endif">
    
    @if($showLegend && $legendPosition === 'top')
        <div class="status-bar-legend legend-top {{ $legendClass }}">
            @foreach($processedValues as $valueData)
                @if($valueData['percentage'] > 0)
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: {{ $valueData['color'] }};"></div>
                        <span class="legend-label {{ $valueData['textClass'] }}">{{ $valueData['label'] }}</span>
                        <span class="legend-value">{{ number_format($valueData['value'], 2) }}{{ $unit ? ' ' . $unit : '' }}</span>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    @if($legendPosition === 'left' && $showLegend)
        <div class="status-bar-legend legend-left {{ $legendClass }}" style="margin-right: 1.5rem;">
            @foreach($processedValues as $valueData)
                @if($valueData['percentage'] > 0)
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: {{ $valueData['color'] }};"></div>
                        <span class="legend-label {{ $valueData['textClass'] }}">{{ $valueData['label'] }}</span>
                        <span class="legend-value">{{ number_format($valueData['value'], 2) }}{{ $unit ? ' ' . $unit : '' }}</span>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    <div class="status-bar-container" style="@if($legendPosition === 'left' || $legendPosition === 'right') flex: 1; @endif">
        @if($title)
            <h3 class="status-bar-title {{ $titleClass }}">{{ $title }}</h3>
        @endif

        <div class="status-bar-wrapper" 
             style="background-color: {{ $backgroundColor }}; 
                    border-radius: 4px; 
                    height: {{ $height }}; 
                    overflow: hidden;
                    display: flex;
                    margin-bottom: 0.75rem;">
            
            @foreach($processedValues as $index => $valueData)
                @if($valueData['percentage'] > 0)
                    <div class="status-bar-segment" 
                         style="background-color: {{ $valueData['color'] }}; 
                                width: {{ $valueData['percentage'] }}%;
                                height: 100%;
                                @if($index < count($processedValues) - 1 && $gap !== '0px') 
                                    margin-right: {{ $gap }};
                                @endif"
                         data-label="{{ $valueData['label'] }}"
                         data-value="{{ $valueData['value'] }}"
                         data-percentage="{{ $valueData['percentage'] }}">
                    </div>
                @endif
            @endforeach
        </div>

        @if($showLegend && $legendPosition === 'bottom')
            <div class="status-bar-legend legend-bottom {{ $legendClass }}">
                @foreach($processedValues as $valueData)
                    @if($valueData['percentage'] > 0)
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: {{ $valueData['color'] }};"></div>
                            <span class="legend-label {{ $valueData['textClass'] }}">{{ $valueData['label'] }}</span>
                            <span class="legend-value">{{ number_format($valueData['value'], 2) }}{{ $unit ? ' ' . $unit : '' }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    @if($legendPosition === 'right' && $showLegend)
        <div class="status-bar-legend legend-right {{ $legendClass }}" style="margin-left: 1.5rem;">
            @foreach($processedValues as $valueData)
                @if($valueData['percentage'] > 0)
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: {{ $valueData['color'] }};"></div>
                        <span class="legend-label {{ $valueData['textClass'] }}">{{ $valueData['label'] }}</span>
                        <span class="legend-value">{{ number_format($valueData['value'], 2) }}{{ $unit ? ' ' . $unit : '' }}</span>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>

<style>
.apex-pro-status-bar {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    max-width: 100%;
    background: white;
    transition: all 0.3s ease;
    position: relative;
    overflow: visible;
}

.apex-pro-status-bar:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
}

.status-bar-title {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    margin: 0 0 0.75rem 0;
    line-height: 1.4;
}

.status-bar-wrapper {
    position: relative;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
}

.status-bar-segment {
    transition: all 0.3s ease;
    position: relative;
}

.status-bar-segment:hover {
    opacity: 0.8;
    cursor: pointer;
}

/* Legend Styles */
.status-bar-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    font-size: 0.75rem;
}

.legend-top {
    margin-bottom: 1rem;
}

.legend-bottom {
    margin-top: 1rem;
}

.legend-left,
.legend-right {
    flex-direction: column;
    gap: 0.75rem;
    min-width: 140px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    white-space: nowrap;
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 2px;
    flex-shrink: 0;
}

.legend-label {
    color: #374151;
    font-weight: 500;
    flex: 1;
}

.legend-value {
    color: #6b7280;
    font-weight: 400;
    margin-left: auto;
    font-size: 0.7rem;
}

/* Responsive Design */
@media (max-width: 640px) {
    .apex-pro-status-bar[style*="flex"] {
        flex-direction: column !important;
    }
    
    .legend-left,
    .legend-right {
        margin: 1rem 0 0 0 !important;
        flex-direction: row !important;
        min-width: auto !important;
        flex-wrap: wrap;
    }
    
    .status-bar-legend {
        gap: 0.75rem;
    }
    
    .legend-item {
        min-width: auto;
        flex: 0 0 auto;
    }
}

/* Hover tooltip effect */
.status-bar-segment::after {
    content: attr(data-label) ': ' attr(data-value) ' (' attr(data-percentage) '%)';
    position: absolute;
    bottom: 120%;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
    z-index: 10;
}

.status-bar-segment:hover::after {
    opacity: 1;
}

/* Animation for loading */
.status-bar-segment {
    transform: scaleX(0);
    transform-origin: left;
    animation: expandSegment 0.6s ease-out forwards;
}

@keyframes expandSegment {
    to {
        transform: scaleX(1);
    }
}

/* Stagger animation for multiple segments */
.status-bar-segment:nth-child(1) { animation-delay: 0.1s; }
.status-bar-segment:nth-child(2) { animation-delay: 0.2s; }
.status-bar-segment:nth-child(3) { animation-delay: 0.3s; }
.status-bar-segment:nth-child(4) { animation-delay: 0.4s; }
.status-bar-segment:nth-child(5) { animation-delay: 0.5s; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add accessibility attributes
    const statusBar = document.getElementById('{{ $id }}');
    const segments = statusBar.querySelectorAll('.status-bar-segment');
    
    segments.forEach(function(segment) {
        segment.setAttribute('role', 'progressbar');
        segment.setAttribute('aria-label', segment.dataset.label);
        segment.setAttribute('aria-valuenow', segment.dataset.percentage);
        segment.setAttribute('aria-valuemin', '0');
        segment.setAttribute('aria-valuemax', '100');
    });
    
    // Optional: Add click handlers for interactivity
    segments.forEach(function(segment) {
        segment.addEventListener('click', function() {
            // Dispatch custom event that can be caught by parent applications
            const event = new CustomEvent('apex:status-bar:segment-clicked', {
                detail: {
                    label: this.dataset.label,
                    value: this.dataset.value,
                    percentage: this.dataset.percentage,
                    widgetId: '{{ $id }}'
                },
                bubbles: true
            });
            
            this.dispatchEvent(event);
        });
    });
});
</script>