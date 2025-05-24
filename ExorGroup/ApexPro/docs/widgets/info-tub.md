# InfoTub Widget Documentation

The InfoTub widget is a responsive card component designed to display statistics, metrics, and key information with optional icons or images. Perfect for dashboards, KPI displays, and social media statistics.

## Overview

The InfoTub widget creates clean, professional-looking cards that automatically adapt to different screen sizes. Each widget can display a main value, descriptive caption, and an optional image with full control over positioning and alignment.

## Basic Syntax
```
!!apex-infoTub:{"parameter":"value"}!!
```

## Required Parameters

| Parameter | Description |
|-----------|-------------|
| `text` | The main value or statistic to display |
| `caption` | Descriptive text explaining the statistic |

## Optional Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `id` | string | auto-generated | Unique identifier for the widget |
| `image` | string | `""` | URL to icon or image file |
| `size` | string | `"60px"` | Image dimensions (width and height) |
| `position` | string | `"top"` | Image position: `"top"` or `"bottom"` |
| `textAlign` | string | `"left"` | Text alignment: `"left"`, `"center"`, or `"right"` |
| `captionAlign` | string | `"left"` | Caption alignment: `"left"`, `"center"`, or `"right"` |
| `imageAlign` | string | `"center"` | Image alignment: `"left"`, `"center"`, or `"right"` |
| `borderWidth` | string | `"1px"` | Border thickness (e.g., `"2px"`, `"0px"`) |
| `borderColor` | string | `"#e5e7eb"` | Border color (hex, rgb, or CSS color name) |
| `textClass` | string | `""` | Additional CSS classes for the main text |
| `captionClass` | string | `""` | Additional CSS classes for the caption |
| `boxClass` | string | `""` | Additional CSS classes for the widget container |

## Usage Examples

### Simple Statistics Card
Display basic statistics without an image:

```
!!apex-infoTub:{"text":"1,247", "caption":"Total Users"}!!
```

### Social Media Statistics
Create LinkedIn-style statistics with icons:

```
!!apex-infoTub:{
    "text":"9.3k", 
    "caption":"Amazing mates",
    "image":"https://cdn-icons-png.flaticon.com/512/174/174857.png",
    "size":"50px",
    "borderColor":"#0066cc",
    "borderWidth":"2px",
    "textAlign":"center",
    "captionAlign":"center"
}!!
```

### Revenue Dashboard Card
Display financial metrics with custom styling:

```
!!apex-infoTub:{
    "text":"$45,230",
    "caption":"Monthly Revenue",
    "image":"https://cdn-icons-png.flaticon.com/512/2769/2769339.png",
    "textClass":"text-green-600 font-bold text-3xl",
    "captionClass":"text-gray-500 uppercase tracking-wide",
    "borderColor":"#10b981",
    "borderWidth":"2px"
}!!
```

### Performance Indicators
Show completion rates or scores:

```
!!apex-infoTub:{
    "text":"94%",
    "caption":"Completion Rate",
    "image":"https://cdn-icons-png.flaticon.com/512/3472/3472620.png",
    "size":"45px",
    "textAlign":"center",
    "captionAlign":"center",
    "textClass":"text-blue-600",
    "boxClass":"shadow-lg"
}!!
```

### Image Position Variations
Image at Bottom

```
!!apex-infoTub:{
    "text":"342",
    "caption":"Posts Shared",
    "image":"https://cdn-icons-png.flaticon.com/512/1006/1006771.png",
    "position":"bottom",
    "textAlign":"center",
    "captionAlign":"center"
}!!
```

### Custom Image Alignment
```
!!apex-infoTub:{
    "text":"1.2M",
    "caption":"Followers",
    "image":"https://cdn-icons-png.flaticon.com/512/1077/1077114.png",
    "imageAlign":"left",
    "textAlign":"right",
    "captionAlign":"center"
}!!
```

### Dashboard Grid Layout
Create a responsive dashboard with multiple InfoTub widgets:

```
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    !!apex-infoTub:{
        "text":"2,847",
        "caption":"Active Users",
        "image":"https://cdn-icons-png.flaticon.com/512/1077/1077114.png",
        "borderColor":"#3b82f6"
    }!!
    
    !!apex-infoTub:{
        "text":"$12,450",
        "caption":"Total Revenue",
        "image":"https://cdn-icons-png.flaticon.com/512/2769/2769339.png",
        "borderColor":"#10b981",
        "textClass":"text-green-600"
    }!!
    
    !!apex-infoTub:{
        "text":"156",
        "caption":"New Orders",
        "image":"https://cdn-icons-png.flaticon.com/512/3081/3081559.png",
        "borderColor":"#f59e0b",
        "textClass":"text-yellow-600"
    }!!
    
    !!apex-infoTub:{
        "text":"98.5%",
        "caption":"Uptime",
        "image":"https://cdn-icons-png.flaticon.com/512/3472/3472620.png",
        "borderColor":"#ef4444",
        "textClass":"text-red-600"
    }!!
</div>
```

## Styling and Customization

### Border Customization
Control the widget's border appearance:

```
!!apex-infoTub:{
    "text":"500",
    "caption":"Downloads",
    "borderWidth":"3px",
    "borderColor":"#8b5cf6"
}!!
```

### Text Styling
Apply custom CSS classes for advanced styling:

```
!!apex-infoTub:{
    "text":"99.9%",
    "caption":"Reliability Score",
    "textClass":"text-4xl font-extrabold text-purple-600",
    "captionClass":"text-sm text-gray-400 uppercase letter-spacing-wide"
}!!
```

### Container Styling
Add shadow, hover effects, and other container styles:

```
!!apex-infoTub:{
    "text":"1,847",
    "caption":"Satisfied Customers",
    "boxClass":"shadow-2xl hover:shadow-3xl transition-all duration-300 bg-gradient-to-br from-blue-50 to-indigo-100"
}!!
```

## Responsive Behavior
The InfoTub widget automatically adapts to different screen sizes:

- Desktop (>640px): Full size display with maximum 200px width
- Tablet (≤640px): Reduced to 150px maximum width with smaller fonts
- Mobile (≤480px): Further reduced to 120px width for optimal mobile viewing

## Best Practices
### Image Guidelines

- Use square images for best results (the widget will crop to square automatically)
- Recommended image size: 256x256px or larger
- Use PNG format with transparent backgrounds for icons
- Ensure images are web-optimized for fast loading

### Content Guidelines

- Keep text values concise (ideally under 8 characters)
- Use clear, descriptive captions
- Maintain consistent styling across related widgets
- Consider color psychology when choosing border colors

### Layout Recommendations

- Use CSS Grid or Flexbox for responsive layouts
- Maintain consistent spacing between widgets
- Group related statistics together
- Ensure adequate whitespace around widgets

## Common Use Cases
### Business Dashboards
Perfect for displaying KPIs, revenue metrics, user statistics, and performance indicators.

#### Social Media Analytics
Ideal for showing follower counts, engagement rates, post statistics, and reach metrics.

### E-commerce Metrics
Great for displaying sales figures, inventory levels, customer counts, and conversion rates.

### System Monitoring
Excellent for uptime percentages, response times, error rates, and system health indicators.

## Troubleshooting
Issue: Image not displaying
Solution: Verify the image URL is accessible and returns a valid image format (PNG, JPG, SVG)

Issue: Alignment not working as expected <br/>
Solution: Ensure alignment values are exactly "left", "center", or "right" (case-sensitive)

Issue: Widget appears too wide on mobile <<br/>
Solution: The widget is responsive by default, but you may need to adjust the container's CSS grid or flexbox settings

Issue: Custom classes not applying<br/>
Solution: Verify that your custom CSS classes are loaded and have sufficient specificity to override default styles
