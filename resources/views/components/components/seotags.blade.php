@props([
    'model'       => null,
    'title'       => null,
    'force'       => false,
    'image'       => null,
    'keywords'    => null,
    'description' => null,
])


@php
    if ($model && is_object($model)){
        $seo = $model->seo;
    }else {

        $force = true;

        $seo = new (config('administrable.modules.seo.model'));

        if ($title){
            $seo->setAttribute('page:title', $title);
        }

        if ($description){
            $seo->setAttribute('page:description', $description);
        }

        if ($keywords){
            $seo->setAttribute('page:meta:keywords', $keywords);
        }

        if ($image){
            $seo->setAttribute('og:image', $image);
        }
    }
@endphp

@section('seo')

{!! $seo?->getHtmlTags($force) !!}

@endsection
