<div class="gauge-wrapper" style="position: relative; display: inline-block;">
    @if($gaugeLabelPosition === 'above' && $gaugeLabel)
        <div class="gauge-label gauge-label-above {{ $gaugeLabelClass }}" 
             style="text-align: {{ $gaugeLabelAlign }}; margin-bottom: 0.25rem;">
            {{ $gaugeLabel }}
        </div>
    @endif
    
    @if($gaugeType === 'radial')
        @include('apexpro::partials.gauge-radial', compact('gaugeValue', 'gaugeColor', 'gaugeBackgroundColor', 'gaugeWidth', 'gaugeThickness', 'showPercentage', 'uniqueId'))
    @elseif($gaugeType === 'round')
        @include('apexpro::partials.gauge-round', compact('gaugeValue', 'gaugeColor', 'gaugeBackgroundColor', 'gaugeWidth', 'gaugeThickness', 'showPercentage', 'uniqueId'))
    @elseif($gaugeType === 'linear')
        @include('apexpro::partials.gauge-linear', compact('gaugeValue', 'gaugeColor', 'gaugeBackgroundColor', 'gaugeWidth', 'gaugeHeight', 'showPercentage', 'uniqueId'))
    @endif
    
    @if($gaugeLabelPosition === 'below' && $gaugeLabel)
        <div class="gauge-label gauge-label-below {{ $gaugeLabelClass }}" 
             style="text-align: {{ $gaugeLabelAlign }}; margin-top: 0.25rem;">
            {{ $gaugeLabel }}
        </div>
    @endif
</div>

<style>
    .gauge-label {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
        line-height: 1.4;
    }
    
    .gauge-label-above {
        margin-bottom: 0.25rem;
    }
    
    .gauge-label-below {
        margin-top: 0.25rem;
    }
</style>