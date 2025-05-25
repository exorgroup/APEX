<div id="{{ $id }}" 
     class="apex-pro-image-tub {{ $layout === 'horizontal' ? 'horizontal' : 'vertical' }} {{ $containerClass }}"
     style="width: {{ $width }}; height: {{ $height }}; 
            border-radius: {{ $borderRadius }}; 
            background-color: {{ $backgroundColor }};
            padding: {{ $padding }};
            @if($shadow) box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); @endif">
    
    @if($layout === 'vertical')
        <!-- Vertical Layout -->
        <div class="image-tub-image-container vertical-image">
            @if($image)
                <img src="{{ $image }}" 
                     alt="{{ $imageAlt }}" 
                     class="image-tub-image"
                     style="width: {{ $imageSize }}; height: {{ $imageSize }};">
            @endif
        </div>
        
        <div class="image-tub-content vertical-content">
            @if($title)
                <h3 class="image-tub-title {{ $titleClass }}">
                    @if($titleUrl && $titleUrl !== '#')
                        <a href="{{ $titleUrl }}" target="{{ $titleTarget }}" class="title-link">
                            {{ $title }}
                        </a>
                    @else
                        {{ $title }}
                    @endif
                </h3>
            @endif
            
            @if($line1)
                <div class="image-tub-line {{ $line1Class }}">{{ $line1 }}</div>
            @endif
            
            @if($line2)
                <div class="image-tub-line {{ $line2Class }}">{{ $line2 }}</div>
            @endif
            
            @if($line3)
                <div class="image-tub-line {{ $line3Class }}">{{ $line3 }}</div>
            @endif
        </div>
    @else
        <!-- Horizontal Layout -->
        <div class="image-tub-horizontal-container">
            <div class="image-tub-image-container horizontal-image">
                @if($image)
                    <img src="{{ $image }}" 
                         alt="{{ $imageAlt }}" 
                         class="image-tub-image"
                         style="width: {{ $imageWidth }}; height: {{ $imageHeight }};">
                @endif
            </div>
            
            <div class="image-tub-content horizontal-content">
                @if($title)
                    <h3 class="image-tub-title {{ $titleClass }}">
                        @if($titleUrl && $titleUrl !== '#')
                            <a href="{{ $titleUrl }}" target="{{ $titleTarget }}" class="title-link">
                                {{ $title }}
                            </a>
                        @else
                            {{ $title }}
                        @endif
                    </h3>
                @endif
                
                @if($line1)
                    <div class="image-tub-line {{ $line1Class }}">{{ $line1 }}</div>
                @endif
                
                @if($line2)
                    <div class="image-tub-line {{ $line2Class }}">{{ $line2 }}</div>
                @endif
                
                @if($line3)
                    <div class="image-tub-line {{ $line3Class }}">{{ $line3 }}</div>
                @endif
            </div>
        </div>
    @endif
</div>

<style>
    .apex-pro-image-tub {
        background: white;
        border-radius: 12px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .apex-pro-image-tub:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
    }
    
    /* Vertical Layout Styles */
    .apex-pro-image-tub.vertical {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .vertical-image {
        margin-bottom: 1rem;
        flex-shrink: 0;
    }
    
    .vertical-content {
        flex: 1;
        width: 100%;
    }
    
    /* Horizontal Layout Styles */
    .apex-pro-image-tub.horizontal {
        min-height: 140px;
    }
    
    .image-tub-horizontal-container {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        height: 100%;
    }
    
    .horizontal-image {
        flex-shrink: 0;
    }
    
    .horizontal-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
    }
    
    /* Image Styles */
    .image-tub-image {
        border-radius: 8px;
        object-fit: cover;
        display: block;
    }
    
    .vertical .image-tub-image {
        margin: 0 auto;
    }
    
    /* Content Styles */
    .image-tub-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 0.75rem 0;
        line-height: 1.3;
    }
    
    .image-tub-title .title-link {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s ease;
    }
    
    .image-tub-title .title-link:hover {
        color: #3b82f6;
        text-decoration: underline;
    }
    
    .image-tub-line {
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }
    
    .image-tub-line:last-child {
        margin-bottom: 0;
    }
    
    /* Default Line Styles */
    .image-tub-line1 {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }
    
    .image-tub-line2 {
        font-size: 1.1rem;
        color: #059669;
        font-weight: 600;
    }
    
    .image-tub-line3 {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 400;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .apex-pro-image-tub.horizontal {
            min-height: auto;
        }
        
        .image-tub-horizontal-container {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        .horizontal-image {
            margin-bottom: 1rem;
        }
        
        .horizontal-content {
            align-items: center;
        }
        
        .image-tub-title {
            font-size: 1.125rem;
        }
        
        .image-tub-line1 {
            font-size: 0.8125rem;
        }
        
        .image-tub-line2 {
            font-size: 1rem;
        }
        
        .image-tub-line3 {
            font-size: 0.8125rem;
        }
    }
    
    @media (max-width: 480px) {
        .apex-pro-image-tub {
            padding: 1rem !important;
        }
        
        .image-tub-title {
            font-size: 1rem;
        }
        
        .image-tub-line1,
        .image-tub-line3 {
            font-size: 0.75rem;
        }
        
        .image-tub-line2 {
            font-size: 0.9375rem;
        }
    }
    
    /* Animation for loading images */
    .image-tub-image {
        opacity: 0;
        animation: fadeInImage 0.5s ease-in-out forwards;
    }
    
    @keyframes fadeInImage {
        to {
            opacity: 1;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add loading effect for images
        const imageTubImages = document.querySelectorAll('#{{ $id }} .image-tub-image');
        
        imageTubImages.forEach(function(img) {
            if (img.complete) {
                img.style.opacity = '1';
            } else {
                img.addEventListener('load', function() {
                    this.style.opacity = '1';
                });
                
                img.addEventListener('error', function() {
                    this.style.opacity = '0.5';
                    this.alt = 'Image not found';
                });
            }
        });
    });
</script>