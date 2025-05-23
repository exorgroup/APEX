@if(isset($debugInfo))
{!! $debugInfo !!}
@endif
<div class="apex-test-widget {{ $cssClass }}" id="{{ $id }}">
    <h3 class="text-xl font-bold mb-2">{{ $title }}</h3>
    <div class="content">
        {!! $content !!}
    </div>
</div>