@php
    if (!empty($group_key)){
        $adgroup = config('administrable.extensions.ad.models.group')::getByKey($group_key);
    }
@endphp

@if($adgroup)
    <div data-group="slick"
    @if($group_attrs)
        @foreach ($group_attrs as $key => $value)
        {{ $key }}="{{ $value }}"
        @endforeach
    @endif
    >
        @php
            $group_ads = $adgroup->getAds();
        @endphp
        @foreach ($group_ads as $ad)
        {!! $ad->render([
            'width'  => $adgroup->width,
            'height' => $adgroup->height,
        ]) !!}
        @endforeach
    </div>

    @if($adgroup->slider)
        @once
            @push('css')
                <link rel="stylesheet" href="{{ asset('vendor/extensions/ad/css/slick.min.css') }}">
                <link rel="stylesheet" href="{{ asset('vendor/extensions/ad/css/slick-theme.min.css') }}">
            @endpush
            @push('js')
            <script src="{{ asset('vendor/extensions/ad/js/slick.min.js') }}"></script>
            @endpush
        @endonce

        @push('js')
            <script>
                $('[data-group="slick"]').slick({
                    infinite: true,
                    arrows: false,
                    //pauseOnFocus: false,
                    autoplay: true,
                    autoplaySpeed: {{ $group_slider_speed ?? 10000 }},
                    //duration: 300,
                    slidesToScroll: {{ $group_slides_to_scroll ?? 1 }},
                    slidesToShow: {{ $group_slides_to_show ?? 1 }},
                });
            </script>
        @endpush
    @endif
@endif
