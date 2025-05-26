<div id="{{ $id }}" 
     class="apex-pro-page-title {{ $containerClass }}"
     style="background-color: {{ $backgroundColor }};
            padding: {{ $padding }};
            border-radius: {{ $borderRadius }};
            @if($border) border: {{ $borderWidth }} solid {{ $borderColor }}; @endif
            @if($shadow) box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); @endif">
    
    <!-- Page Title (Top Line) -->
    <h1 class="page-title {{ $titleClass }}">{{ $title }}</h1>
    
    <!-- Breadcrumbs (Bottom Line) -->
    @if(!empty($breadcrumbs))
        <nav class="page-breadcrumbs {{ $breadcrumbClass }}">
            @foreach($breadcrumbs as $index => $breadcrumb)
                @if($index > 0)
                    <span class="breadcrumb-delimiter">{{ $delimiter }}</span>
                @endif
                
                @if(!empty($breadcrumb['url']))
                    <a href="{{ $breadcrumb['url'] }}" 
                       class="breadcrumb-link"
                       @if(!empty($breadcrumb['target'])) target="{{ $breadcrumb['target'] }}" @endif>
                        {{ $breadcrumb['label'] }}
                    </a>
                @else
                    <span class="breadcrumb-item">{{ $breadcrumb['label'] }}</span>
                @endif
            @endforeach
        </nav>
    @endif
</div>

<style>
    .apex-pro-page-title {
        background: white;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    @if($shadow)
    .apex-pro-page-title:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }
    @endif
    
    /* Page Title Styles */
    .page-title {
        font-size: 1.875rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 0.5rem 0;
        line-height: 1.2;
    }
    
    /* Breadcrumb Styles */
    .page-breadcrumbs {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.25rem;
    }
    
    /* Fixed breadcrumb link styles - inherit color and remove underline */
    .breadcrumb-link {
        color: inherit !important;
        text-decoration: none !important;
        transition: opacity 0.2s ease;
    }
    
    .breadcrumb-link:hover {
        color: inherit !important;
        text-decoration: none !important;
        opacity: 0.7;
    }
    
    .breadcrumb-link:visited {
        color: inherit !important;
    }
    
    .breadcrumb-link:active {
        color: inherit !important;
    }
    
    .breadcrumb-item {
        color: inherit;
    }
    
    .breadcrumb-delimiter {
        color: #9ca3af;
        margin: 0 0.25rem;
        font-weight: 400;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .page-title {
            font-size: 1.5rem;
        }
        
        .page-breadcrumbs {
            font-size: 0.8125rem;
        }
        
        .apex-pro-page-title {
            padding: 0.75rem 1rem !important;
        }
    }
    
    @media (max-width: 480px) {
        .page-title {
            font-size: 1.25rem;
        }
        
        .page-breadcrumbs {
            font-size: 0.75rem;
            flex-direction: column;
            align-items: flex-start;
        }
        
        .breadcrumb-delimiter {
            display: none;
        }
        
        .page-breadcrumbs::before {
            content: "Navigation: ";
            font-weight: 500;
            color: #4b5563;
        }
    }
</style>