<div id="{{ $id }}" 
     class="apex-pro-progress-tub {{ $containerClass }}"
     style="width: {{ $width }}; height: {{ $height }}; 
            border-radius: {{ $borderRadius }}; 
            background-color: {{ $backgroundColor }};
            padding: {{ $padding }};
            @if($shadow) box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); @endif">
    
    @if($gaugePosition === 'top')
        <!-- Gauge at Top -->
        <div class="progress-tub-gauge-container">
            @include('apexpro::partials.gauge', [
                'gaugeType' => $gaugeType,
                'gaugeValue' => $gaugeValue,
                'gaugeColor' => $gaugeColor,
                'gaugeBackgroundColor' => $gaugeBackgroundColor,
                'gaugeWidth' => $gaugeWidth,
                'gaugeHeight' => $gaugeHeight,
                'gaugeThickness' => $gaugeThickness,
                'showPercentage' => $showPercentage,
                'gaugeLabel' => $gaugeLabel,
                'gaugeLabelClass' => $gaugeLabelClass,
                'gaugeLabelAlign' => $gaugeLabelAlign,
                'gaugeLabelPosition' => $gaugeLabelPosition,
                'uniqueId' => $id
            ])
        </div>
    @endif
    
    <div class="progress-tub-content">
        @if($text)
            <div class="progress-tub-text {{ $textClass }}" style="text-align: {{ $textAlign }};">
                {{ $text }}
            </div>
        @endif
        
        @if($caption)
            <div class="progress-tub-caption {{ $captionClass }}" style="text-align: {{ $captionAlign }};">
                {{ $caption }}
            </div>
        @endif
        
        @if(!empty($badges))
            <div class="progress-tub-badges">
                @foreach($badges as $badge)
                    <div class="progress-tub-badge {{ $badge['class'] ?? '' }}">
                        @if(isset($badge['image']) && $badge['image'])
                            <img src="{{ $badge['image'] }}" alt="{{ $badge['alt'] ?? '' }}" class="badge-image">
                        @endif
                        @if(isset($badge['value']) && $badge['value'])
                            <span class="badge-value">{{ $badge['value'] }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    
    @if($gaugePosition === 'bottom')
        <!-- Gauge at Bottom -->
        <div class="progress-tub-gauge-container">
            @include('apexpro::partials.gauge', [
                'gaugeType' => $gaugeType,
                'gaugeValue' => $gaugeValue,
                'gaugeColor' => $gaugeColor,
                'gaugeBackgroundColor' => $gaugeBackgroundColor,
                'gaugeWidth' => $gaugeWidth,
                'gaugeHeight' => $gaugeHeight,
                'gaugeThickness' => $gaugeThickness,
                'showPercentage' => $showPercentage,
                'gaugeLabel' => $gaugeLabel,
                'gaugeLabelClass' => $gaugeLabelClass,
                'gaugeLabelAlign' => $gaugeLabelAlign,
                'gaugeLabelPosition' => $gaugeLabelPosition,
                'uniqueId' => $id
            ])
        </div>
    @endif
</div>

<style>
    .apex-pro-progress-tub {
        background: white;
        border-radius: 12px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .apex-pro-progress-tub:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
    }
    
    .progress-tub-gauge-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0.5rem 0; /* Reduced margin for tighter spacing */
    }
    
    .progress-tub-content {
        flex: 1;
        width: 100%;
    }
    
    .progress-tub-text {
        font-size: 2rem;
        font-weight: bold;
        color: #1f2937;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }
    
    .progress-tub-caption {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
        margin-bottom: 1rem;
        line-height: 1.4;
    }
    
    .progress-tub-badges {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-top: 0.5rem;
    }
    
    .progress-tub-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        background-color: #f3f4f6;
        color: #374151;
    }
    
    .progress-tub-badge.badge-positive {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .progress-tub-badge.badge-negative {
        background-color: #fee2e2;
        color: #dc2626;
    }
    
    .badge-image {
        width: 12px;
        height: 12px;
        object-fit: contain;
    }
    
    .badge-value {
        line-height: 1;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .progress-tub-text {
            font-size: 1.5rem;
        }
        
        .progress-tub-caption {
            font-size: 0.8125rem;
        }
        
        .progress-tub-badge {
            font-size: 0.6875rem;
            padding: 0.1875rem 0.375rem;
        }
    }
    
    @media (max-width: 480px) {
        .apex-pro-progress-tub {
            padding: 1rem !important;
        }
        
        .progress-tub-text {
            font-size: 1.25rem;
        }
        
        .progress-tub-caption {
            font-size: 0.75rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize gauge animation
        const gauge = document.querySelector('#{{ $id }} .gauge-progress');
        if (gauge) {
            // Animate gauge fill
            setTimeout(function() {
                gauge.style.transition = 'stroke-dashoffset 1s ease-in-out';
            }, 100);
        }
    });
</script>