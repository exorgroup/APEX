<div id="{{ $id }}" class="apex-pro-info-tub {{ $boxClass }}" 
     style="border: {{ $borderWidth }} solid {{ $borderColor }};">
    
    @if($position === 'top')
        <div class="info-tub-image">
            <img src="{{ $image }}" alt="Info icon" 
                 style="width: {{ $size }}; height: {{ $size }}; 
                        @if($imageAlign === 'left')
                            margin-left: 0; margin-right: auto;
                        @elseif($imageAlign === 'right')
                            margin-left: auto; margin-right: 0;
                        @else
                            margin-left: auto; margin-right: auto;
                        @endif">
        </div>
    @endif
    
    <div class="info-tub-content">
        <div class="info-tub-text {{ $textClass }}">
            <span style="text-align: {{ $textAlign }}; display: block;">{{ $text }}</span>
        </div>
        <div class="info-tub-caption {{ $captionClass }}">
            <span style="text-align: {{ $captionAlign }}; display: block;">{{ $caption }}</span>
        </div>
    </div>
    
    @if($position === 'bottom')
        <div class="info-tub-image">
            <img src="{{ $image }}" alt="Info icon" 
                 style="width: {{ $size }}; height: {{ $size }}; 
                        @if($imageAlign === 'left')
                            margin-left: 0; margin-right: auto;
                        @elseif($imageAlign === 'right')
                            margin-left: auto; margin-right: 0;
                        @else
                            margin-left: auto; margin-right: auto;
                        @endif">
        </div>
    @endif
</div>

<style>
    .apex-pro-info-tub {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        max-width: 200px;
        /* Remove margin: 0 auto to allow proper alignment */
    }
    
    .info-tub-image {
        margin-bottom: 1rem;
    }
    
    .info-tub-image img {
        border-radius: 6px;
        object-fit: cover;
    }
    
    .info-tub-content {
        width: 100%; /* Ensure content takes full width */
    }
    
    .info-tub-text {
        font-size: 2rem;
        font-weight: bold;
        color: #1f2937;
        margin-bottom: 0.5rem;
        width: 100%; /* Ensure text takes full width */
    }
    
    .info-tub-caption {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
        width: 100%; /* Ensure caption takes full width */
    }
    
    /* Responsive */
    @media (max-width: 640px) {
        .apex-pro-info-tub {
            max-width: 150px;
            padding: 1rem;
        }
        
        .info-tub-text {
            font-size: 1.5rem;
        }
        
        .info-tub-caption {
            font-size: 0.75rem;
        }
    }
    
    @media (max-width: 480px) {
        .apex-pro-info-tub {
            max-width: 120px;
            padding: 0.75rem;
        }
        
        .info-tub-text {
            font-size: 1.25rem;
        }
    }
    </style>