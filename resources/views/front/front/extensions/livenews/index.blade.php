@php
    $livenews = config('administrable.extensions.livenews.model')::latest()->get();
@endphp

@if( $livenews->isNotEmpty())
<div class="marquee">
  @foreach ($livenews as $news)
      @if(
          $news->online && !empty($news->content) &&
          $news->started_at->isPast() && $news->ended_at->isFuture()
      )
          <span style="
              font-size: {{ $news->size }}px;
              background-color: {{ $news->background_color }};
              color: {{ $news->text_color }}; padding: 5px; text-align: center;"
          " >{{ $news->parseContent() }}</span>
      @endif
  @endforeach
</div>

<link rel="stylesheet" href="{{ asset('vendor/extensions/livenews/css/marquee.css') }}">
<script  src='{{ asset('vendor/extensions/livenews/js/marquee.js') }}'></script>
<script>
    $(function(){
        $('.marquee').marquee({
            duration: {{ $livenews_duration ?? 10000 }},
            delayBeforeStart: {{ $livenews_delay_before_start ?? 5000 }},
            direction: '{{ $livenews_direction ?? 'left' }}',
            allowCss3Support: true,
        });
    });
</script>
@endif



