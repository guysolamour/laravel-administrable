@extends(back_view_path('layouts.base'))

@section('title', $comment->getCommenterName())


@section('content')
<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get('administrable::messages.default.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('comment.index') }}">{{ Lang::get('administrable::messages.view.comment.plural') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a
                        href="#">{{ $comment->getCommenterName() }}</a></li>
            </ol>

            <div class="btn-group">
                <a href="{{ back_route('comment.edit', $comment) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> &nbsp;{{ Lang::get('administrable::messages.default.edit') }}</a>
                @unless ($comment->approved)
                <a href="{{ back_route('comment.approved', $comment) }}" class="btn btn-success" data-toggle="tooltip"
                    data-placement="top" title="{{ Lang::get('administrable::messages.view.comment.approved') }}"><i class="fas fa-check"></i> &nbsp;{{ Lang::get('administrable::messages.view.comment.approved') }}</a>
                @endunless
                <a href="{{ back_route('comment.destroy', $comment) }}" class="btn btn-danger" data-method="delete"
                    data-confirm="{{ Lang::get('administrable::messages.view.comment.destroy') }}">
                    <i class="fas fa-trash"></i> &nbsp;{{ Lang::get('administrable::messages.default.delete') }}</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h3 class="title-5 m-b-35">
            {{ Str::singular(Lang::get('administrable::messages.view.comment.plural')) }}
        </h3>
        <div class="row">
            <div class="col-md-12">
                {{-- add fields here --}}
                <div class="pb-2">
                    <p><span class="font-weight-bold">{{ Lang::get('administrable::messages.view.comment.name')  }}:</span></p>
                    <p>
                        {{ $comment->getCommenterName() }}
                    </p>
                </div>
                <div class="pb-2">
                    <p><span class="font-weight-bold">{{ Lang::get('administrable::messages.view.comment.email') }}:</span></p>
                    <p>
                        {{ $comment->getCommenterEmail() }}
                    </p>
                </div>
                <div class="pb-2">
                    <p><span class="font-weight-bold">{{ Lang::get('administrable::messages.view.comment.content') }}:</span></p>
                    <p>
                        {{ $comment->comment }}
                    </p>
                </div>

                <div class="pb-2">
                    <p><span class="font-weight-bold">{{ Lang::get('administrable::messages.view.comment.createdat') }}:</span></p>
                    <p>
                        {{ $comment->created_at->format('d/m/Y h:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
