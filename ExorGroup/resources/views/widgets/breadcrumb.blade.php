<div id="{{ $id }}" 
     class="apex-breadcrumb apex-breadcrumb-style-{{ $style }} {{ $cssClass }}"
     style="
        --breadcrumb-bg: {{ $backgroundColor }};
        --breadcrumb-text: {{ $textColor }};
        --breadcrumb-link: {{ $linkColor }};
        --breadcrumb-hover: {{ $hoverColor }};
        --breadcrumb-active: {{ $activeColor }};
        --breadcrumb-border: {{ $borderColor }};
        --breadcrumb-border-width: {{ $borderWidth }};
        --breadcrumb-font-size: {{ $fontSize }};
        --breadcrumb-font-weight: {{ $fontWeight }};
        --breadcrumb-padding: {{ $padding }};
        --breadcrumb-border-radius: {{ $borderRadius }};
        --breadcrumb-gap: {{ $gap }};
        --breadcrumb-height: {{ $height }};
        @if($showBorder) border: var(--breadcrumb-border-width) solid var(--breadcrumb-border); @endif
        @if($showShadow) box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); @endif
     ">
    
    @if($style == 1)
        {{-- Style 1: Plain text --}}
        @foreach($items as $index => $item)
            @if($index > 0)
                <span class="breadcrumb-delimiter">{{ $delimiter }}</span>
            @endif
            
            @if(!empty($item['url']))
                <a href="{{ $item['url'] }}" class="breadcrumb-link">{{ $item['label'] }}</a>
            @else
                <span class="breadcrumb-active">{{ $item['label'] }}</span>
            @endif
        @endforeach
        
    @elseif($style == 2)
        {{-- Style 2: Rounded pills (first image style) --}}
        @foreach($items as $index => $item)
            @if($index > 0)
                <span class="breadcrumb-delimiter">{{ $delimiter }}</span>
            @endif
            
            <div class="breadcrumb-pill">
                @if(!empty($item['url']))
                    <a href="{{ $item['url'] }}" class="breadcrumb-link">{{ $item['label'] }}</a>
                @else
                    <span class="breadcrumb-active">{{ $item['label'] }}</span>
                @endif
            </div>
        @endforeach
        
    @elseif($style == 3)
        {{-- Style 3: Connected arrows (second image style) --}}
        @foreach($items as $index => $item)
            <div class="breadcrumb-arrow {{ $index === count($items) - 1 ? 'last' : '' }}">
                @if(!empty($item['url']))
                    <a href="{{ $item['url'] }}" class="breadcrumb-link">{{ $item['label'] }}</a>
                @else
                    <span class="breadcrumb-active">{{ $item['label'] }}</span>
                @endif
            </div>
        @endforeach
        
    @elseif($style == 4)
        {{-- Style 4: Connected circles (third image style) --}}
        @foreach($items as $index => $item)
            <div class="breadcrumb-circle-container">
                <div class="breadcrumb-circle {{ $index === count($items) - 1 ? 'active' : '' }}">
                    <span class="breadcrumb-number">{{ $index + 1 }}</span>
                </div>
                @if($index < count($items) - 1)
                    <div class="breadcrumb-connector {{ $index < count($items) - 2 ? 'completed' : '' }}"></div>
                @endif
            </div>
            <div class="breadcrumb-label">
                @if(!empty($item['url']))
                    <a href="{{ $item['url'] }}" class="breadcrumb-link">{{ $item['label'] }}</a>
                @else
                    <span class="breadcrumb-active">{{ $item['label'] }}</span>
                @endif
            </div>
        @endforeach
        
    @elseif($style == 5)
        {{-- Style 5: Pill tabs (fourth image style) --}}
        @foreach($items as $index => $item)
            <div class="breadcrumb-tab {{ $index === count($items) - 1 ? 'active' : '' }}">
                @if(!empty($item['url']))
                    <a href="{{ $item['url'] }}" class="breadcrumb-link">{{ $item['label'] }}</a>
                @else
                    <span class="breadcrumb-active">{{ $item['label'] }}</span>
                @endif
            </div>
        @endforeach
    @endif
</div>

<style>
.apex-breadcrumb {
    display: flex;
    align-items: center;
    gap: var(--breadcrumb-gap);
    background: var(--breadcrumb-bg);
    border-radius: var(--breadcrumb-border-radius);
    padding: var(--breadcrumb-padding);
    font-size: var(--breadcrumb-font-size);
    font-weight: var(--breadcrumb-font-weight);
    min-height: var(--breadcrumb-height);
}

.apex-breadcrumb a {
    color: var(--breadcrumb-link) !important;
    text-decoration: none !important;
    transition: color 0.2s ease;
}

.apex-breadcrumb a:hover {
    color: var(--breadcrumb-hover) !important;
    text-decoration: none !important;
}

.breadcrumb-active {
    color: var(--breadcrumb-active);
}

.breadcrumb-delimiter {
    color: var(--breadcrumb-text);
    margin: 0 4px;
}

/* Style 1: Plain text */
.apex-breadcrumb-style-1 {
    /* Base styles already applied */
}

/* Style 2: Rounded pills */
.apex-breadcrumb-style-2 .breadcrumb-pill {
    background: var(--breadcrumb-bg);
    border: 1px solid var(--breadcrumb-border);
    border-radius: 20px;
    padding: 6px 12px;
    transition: all 0.2s ease;
}

.apex-breadcrumb-style-2 .breadcrumb-pill:hover {
    background: #f9fafb;
    border-color: var(--breadcrumb-hover);
}

/* Style 3: Connected arrows */
.apex-breadcrumb-style-3 {
    padding: 0;
    gap: 0;
    overflow: hidden;
}

.apex-breadcrumb-style-3 .breadcrumb-arrow {
    position: relative;
    background: var(--breadcrumb-bg);
    padding: 12px 20px 12px 30px;
    margin-right: 10px;
    display: flex;
    align-items: center;
    min-height: 40px;
}

.apex-breadcrumb-style-3 .breadcrumb-arrow:first-child {
    padding-left: 20px;
}

.apex-breadcrumb-style-3 .breadcrumb-arrow:not(.last):after {
    content: '';
    position: absolute;
    right: -10px;
    top: 0;
    width: 0;
    height: 0;
    border-top: 20px solid transparent;
    border-bottom: 20px solid transparent;
    border-left: 10px solid var(--breadcrumb-bg);
    z-index: 2;
}

.apex-breadcrumb-style-3 .breadcrumb-arrow:not(:first-child):before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 0;
    height: 0;
    border-top: 20px solid transparent;
    border-bottom: 20px solid transparent;
    border-left: 10px solid var(--breadcrumb-border);
    z-index: 1;
}

/* Style 4: Connected circles */
.apex-breadcrumb-style-4 {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
}

.apex-breadcrumb-style-4 .breadcrumb-circle-container {
    display: flex;
    align-items: center;
    position: relative;
}

.apex-breadcrumb-style-4 .breadcrumb-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: white;
    border: 2px solid var(--breadcrumb-border);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--breadcrumb-text);
    font-weight: 500;
    z-index: 2;
    position: relative;
}

.apex-breadcrumb-style-4 .breadcrumb-circle.active {
    background: var(--breadcrumb-link);
    color: white;
    border-color: var(--breadcrumb-link);
}

.apex-breadcrumb-style-4 .breadcrumb-connector {
    width: 60px;
    height: 2px;
    background: var(--breadcrumb-border);
    margin: 0 8px;
}

.apex-breadcrumb-style-4 .breadcrumb-connector.completed {
    background: var(--breadcrumb-link);
}

.apex-breadcrumb-style-4 .breadcrumb-label {
    margin-left: 40px;
    margin-top: 4px;
}

/* Style 5: Pill tabs */
.apex-breadcrumb-style-5 {
    background: #f3f4f6;
    border-radius: 25px;
    padding: 4px;
}

.apex-breadcrumb-style-5 .breadcrumb-tab {
    padding: 8px 16px;
    border-radius: 20px;
    transition: all 0.2s ease;
    cursor: pointer;
}

.apex-breadcrumb-style-5 .breadcrumb-tab.active {
    background: white;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.apex-breadcrumb-style-5 .breadcrumb-tab:hover:not(.active) {
    background: rgba(255, 255, 255, 0.5);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .apex-breadcrumb {
        font-size: 12px;
        gap: 6px;
        padding: 6px 12px;
    }
    
    .apex-breadcrumb-style-2 .breadcrumb-pill {
        padding: 4px 8px;
    }
    
    .apex-breadcrumb-style-3 .breadcrumb-arrow {
        padding: 8px 15px 8px 20px;
        min-height: 32px;
    }
    
    .apex-breadcrumb-style-4 .breadcrumb-circle {
        width: 24px;
        height: 24px;
        font-size: 12px;
    }
    
    .apex-breadcrumb-style-5 .breadcrumb-tab {
        padding: 6px 12px;
        font-size: 12px;
    }
}
</style>