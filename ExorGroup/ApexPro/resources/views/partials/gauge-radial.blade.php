@php
    $size = intval($gaugeWidth);
    $viewBoxSize = 120;
    $radius = 40;
    $strokeWidth = intval($gaugeThickness);
    $circumference = 3.14159 * $radius * 2 * 0.75; // 75% of full circle for radial gauge
@endphp

<div class="gauge-radial" style="position: relative; display: inline-block;">
    <svg width="{{ $gaugeWidth }}" height="{{ $size * 0.72 }}" viewBox="0 0 {{ $viewBoxSize }} {{ $viewBoxSize * 0.65 }}" class="gauge-svg">
        <!-- Background arc (partial circle like in the image) -->
        <path 
            d="M 25 60 A {{ $radius }} {{ $radius }} 0 1 1 95 60" 
            fill="none" 
            stroke="{{ $gaugeBackgroundColor }}" 
            stroke-width="{{ $strokeWidth }}" 
            stroke-linecap="round"
            class="gauge-background"
        />
        <!-- Progress arc -->
        <path 
            d="M 25 60 A {{ $radius }} {{ $radius }} 0 1 1 95 60" 
            fill="none" 
            stroke="{{ $gaugeColor }}" 
            stroke-width="{{ $strokeWidth }}" 
            stroke-linecap="round"
            stroke-dasharray="{{ $circumference }}"
            stroke-dashoffset="{{ $circumference - ($circumference * $gaugeValue / 100) }}"
            class="gauge-progress"
            style="transition: stroke-dashoffset 1s ease-in-out;"
        />
    </svg>
    
    @if($showPercentage)
        <div class="gauge-percentage" style="
            position: absolute;
            top: 60%;
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