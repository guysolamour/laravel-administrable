@extends(back_view_path('layouts.base'))

@section('title', $comment->getCommenterName())


@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get('administrable::messages.default.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('comment.index') }}">{{ Lang::get('administrable::messages.view.comment.plural') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a
                        href="#">{{ $comment->getCommenterName() }}</a></li>
            </ol>
        </nav>

    </div>

    <div class="card">
        {{-- <h4 class="card-title">
                {{ Str::singular(Lang::get('administrable::messages.view.comment.plural')) }}
            </h4> --}}

        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
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
                            {{ strip_tags($comment->comment) }}
                        </p>
                    </div>

                    <div class="pb-2">
                        <p><span class="font-weight-bold">{{ Lang::get('administrable::messages.view.comment.createdat') }}:</span></p>
                        <p>
                            {{ $comment->created_at->format('d/m/Y h:i') }}
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <section style="margin-bottom: 2rem;">
                        <a href="{{ back_route('comment.edit', $comment) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> {{ Lang::get('administrable::messages.default.edit') }}</a>
                        @unless ($comment->approved)
                        <a href="{{ back_route('comment.approved', $comment) }}" class="btn btn-success" data-toggle="tooltip"
                            data-placement="top" title="{ Lang::get('administrable::messages.view.comment.approved') }}"><i class="fas fa-check"></i> { Lang::get('administrable::messages.view.comment.approved') }}</a>
                        @endunless
                        <a href="{{ back_route('comment.destroy', $comment) }}" class="btn btn-danger" data-method="delete"
                            data-confirm="{{ Lang::get('administrable::messages.view.comment.destroy') }}">
                            <i class="fas fa-trash"></i> {{ Lang::get('administrable::messages.default.destroy') }}</a>
                    </section>
                </div>

            </div>
        </div>
    </div>

    <div class="fab fab-fixed">
        <button class="btn btn-float btn-primary" data-toggle="button">
            <i class="fab-icon-default ti-plus"></i>
            <i class="fab-icon-active ti-close"></i>
        </button>

        <ul class="fab-buttons">
            <li><a class="btn btn-float btn-sm btn-info" href="{{ back_route('comment.edit', $comment) }}" data-provide="tooltip" data-placement="left"
                    title="{{ Lang::get('administrable::messages.default.edit') }}"><i class="ti-pencil"></i> </a></li>
            <li><a class="btn btn-float btn-sm btn-danger" href="{{ back_route('comment.destroy',$comment) }}" data-method="delete" data-confirm="{{ Lang::get('administrable::messages.view.comment.destroy') }}"  title="{{ Lang::get('administrable::messages.default.delete') }}" data-provide="tooltip"
                    data-placement="left" ><i class="ti-trash"></i> </a></li>
        </ul>
    </div>
</div>
@endsection
