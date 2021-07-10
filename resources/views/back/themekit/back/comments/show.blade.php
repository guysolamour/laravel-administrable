@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.view.comment.plural'))

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route(config('administrable.guard') . '.dashboard') }}"><i class="ik ik-home"></i></a>
                            </li>
                           <li class="breadcrumb-item"><a href="{{ back_route('comment.index') }}">{{ Lang::get('administrable::messages.view.comment.plural') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ $comment->name }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">{{ Str::singular(Lang::get('administrable::messages.view.comment.plural')) }}</h3>
                        <div class="btn-group float-right">

                        <a href="{{ back_route('comment.edit', $comment) }}" class="btn btn-primary" title="{{ Lang::get('administrable::messages.default.edit') }}">
                            <i class="fas fa-edit"></i> {{ Lang::get('administrable::messages.default.edit') }}</a>
                        @unless ($comment->approved)
                        <a href="{{ back_route('comment.approved', $comment) }}" class="btn btn-success" data-toggle="tooltip"
                            data-placement="top" title="{{ Lang::get('administrable::messages.view.comment.approved') }}"><i class="fas fa-check"></i> {{ Lang::get('administrable::messages.view.comment.approved') }}</a>
                        @endunless
                        <a href="{{ back_route('comment.destroy', $comment) }}" class="btn btn-danger" data-method="delete"
                            data-confirm="{{ Lang::get('administrable::messages.view.comment.destroy') }}">
                            <i class="fas fa-trash"></i> {{ Lang::get('administrable::messages.default.delete') }}</a>
                        </div>
                    </div>

                    <div class="card-body row">
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

        </div>
    </div>
</div>
@endsection
