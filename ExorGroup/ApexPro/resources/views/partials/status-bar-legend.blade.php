@foreach($processedValues as $index => $valueData)
    @if($valueData['percentage'] > 0)
        <div class="status-bar-segment" 
             style="background-color: {{ $valueData['color'] }}; 
                    width: {{ $valueData['percentage'] }}%;
                    height: 100%;
                    @if($index < count($processedValues) - 1 && $gap !== '0px') 
                        margin-right: {{ $gap }};
                    @endif"
             data-label="{{ $valueData['label'] }}"
             data-value="{{ $valueData['value'] }}"
             data-formatted-value="{{ $valueData['formattedValue'] }}"
             data-percentage="{{ $valueData['percentage'] }}">
        </div>
    @endif
@endforeach