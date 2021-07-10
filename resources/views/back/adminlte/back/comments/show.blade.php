@extends(back_view_path('layouts.base'))

@section('title', $comment->getCommenterName())

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1></h1> --}}
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get('administrable::messages.default.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route('comment.index') }}">{{ Lang::get('administrable::messages.view.comment.plural') }}</a></li>
                            <li class="breadcrumb-item active">{{ $comment->getCommenterName() }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->

    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip"
                        title="Réduire">
                        <i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class='row'>
                    <div class='col-md-8'>
                        {{-- add fields here --}}
                        <div class="pb-2">
                                <p><span class="font-weight-bold">{{ Lang::get('administrable::messages.view.comment.name') }}:</span></p>
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
                    <div class='col-md-4'>
                        @if ($comment->approved)
                        <a target="_blank"
                            href="{{ route( Str::lower(config('administrable.front_namespace')) . strtolower(class_basename($comment->commentable)) .'.show', $comment->commentable) . '#comment-' . $comment->getKey() }}"
                            class="btn btn-secondary" data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::messages.default.show') }}"><i class="fas fa-eye"></i>
                            {{ Lang::get('administrable::messages.default.show') }}</a>
                        @endif
                        <a href="{{ back_route('comment.edit', $comment) }}" class="btn btn-primary">
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

                </div>
            </div>

        </div>
    </section>
</div>
@endsection
