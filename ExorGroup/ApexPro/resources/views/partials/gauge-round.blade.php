@php
    $size = intval($gaugeWidth);
    $viewBoxSize = 120;
    $radius = 40;
    $strokeWidth = intval($gaugeThickness);
    $circumference = 2 * 3.14159 * $radius;
@endphp

<div class="gauge-round" style="position: relative; display: inline-block;">
    <svg width="{{ $gaugeWidth }}" height="{{ $gaugeWidth }}" viewBox="0 0 {{ $viewBoxSize }} {{ $viewBoxSize }}" class="gauge-svg">
        <!-- Background circle -->
        <circle 
            cx="60" 
            cy="60" 
            r="{{ $radius }}" 
            fill="none" 
            stroke="{{ $gaugeBackgroundColor }}" 
            stroke-width="{{ $strokeWidth }}"
            class="gauge-background"
        />
        <!-- Progress circle -->
        <circle 
            cx="60" 
            cy="60" 
            r="{{ $radius }}" 
            fill="none" 
            stroke="{{ $gaugeColor }}" 
            stroke-width="{{ $strokeWidth }}" 
            stroke-linecap="round"
            stroke-dasharray="{{ $circumference }}"
            stroke-dashoffset="{{ $circumference - ($circumference * $gaugeValue / 100) }}"
            class="gauge-progress"
            style="transition: stroke-dashoffset 1s ease-in-out; transform: rotate(-90deg); transform-origin: center;"
        />
    </svg>
    
    @if($showPercentage)
        <div class="gauge-percentage" style="
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
        ">
            {{ $gaugeValue }}%
        </div>
    @endif
</div>