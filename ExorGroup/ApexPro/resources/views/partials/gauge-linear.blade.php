<div class="gauge-linear" style="position: relative; display: inline-block; width: {{ $gaugeWidth }};">
    <div class="gauge-track" style="
        width: 100%;
        height: {{ $gaugeHeight }};
        background-color: {{ $gaugeBackgroundColor }};
        border-radius: {{ intval($gaugeHeight) / 2 }}px;
        overflow: hidden;
    ">
        <div class="gauge-fill" style="
            height: 100%;
            width: {{ $gaugeValue }}%;
            background-color: {{ $gaugeColor }};
            border-radius: {{ intval($gaugeHeight) / 2 }}px;
            transition: width 1s ease-in-out;
        "></div>
    </div>
    
    @if($showPercentage)
        <div class="gauge-percentage" style="
            position: absolute;
            top: -1.5rem;
            right: 0;
            font-size: 0.875rem;
            font-weight: 600;
            color: #1f2937;
        ">
            {{ $gaugeValue }}%
        </div>
    @endif
</div>