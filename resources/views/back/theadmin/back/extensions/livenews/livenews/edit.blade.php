@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.default.edition'))


@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get('administrable::messages.default.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.livenews.livennews.index') }}">{{ Lang::get('administrable::extensions.livenews.label') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.livenews.livenews.show', $livenews) }}">{{ Str::limit(strip_tags($livenews->content), 20) }}</a></li>
                <li class="breadcrumb-item active">{{ Lang::get('administrable::messages.default.edition') }}</li>
            </ol>
        </nav>


    </div>

    <div class="card">
        <h4 class="card-title">
            {{ Lang::get('administrable::messages.default.edition') }}
        </h4>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    @include(back_view_path('extensions.livenews.livenews._form'), ['edit' => true])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
