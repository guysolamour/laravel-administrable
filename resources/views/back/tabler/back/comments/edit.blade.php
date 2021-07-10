@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.default.edition'))

@section('content')
<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . 'dashboard') }}">{{ Lang::get('administrable::messages.default.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('comment.index') }}">{{ Lang::get('administrable::messages.view.comment.plural') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('comment.show', $comment) }}">{{ $comment->getCommenterName() }}</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ Lang::get('administrable::messages.default.edition') }}</a></li>
            </ol>

            <div class="btn-group">
                <a href="{{ back_route('comment.destroy', $comment) }}" class="btn btn-danger" data-method="delete"
                    data-confirm="{{ Lang::get('administrable::messages.view.comment.destroy') }}">
                    <i class="fas fa-trash"></i>&nbsp; {{ Lang::get('administrable::messages.default.delete') }}</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h3 class="title-5 m-b-35">
            {{ Lang::get('administrable::messages.default.edition') }}
        </h3>
        <div class="row">
            <div class="col-md-12">
                @include(back_view_path('comments._form'), ['edit' => true])
            </div>
        </div>
    </div>
</div>
@endsection

