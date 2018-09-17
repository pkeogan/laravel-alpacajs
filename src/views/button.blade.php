<button type="button" class="btn btn-{{ $button['style'] }}" data-toggle="modal" data-target="#{{$id}}-modal" data-toggle="tooltip" title="@if(isset($button['tooltip'])){{ $button['tooltip'] }}@else{{ $modal['header']['text'] }}@endif">
    @if($button['text']){{ $button['text'] }} @endif   
    @if($button['icon']) <i class="{{ $button['icon'] }}" aria-hidden="true"></i> @endif
</button>