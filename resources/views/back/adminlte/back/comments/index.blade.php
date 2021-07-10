@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.view.comment.plural'))

@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1>{{ Lang::get('administrable::messages.view.comment.plural') }}</h1> --}}
                </div>
                <div class="col-sm-6">
                    <div class='float-sm-right'>
                         <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . 'dashboard') }}">{{ Lang::get('administrable::messages.default.dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ Lang::get('administrable::messages.view.comment.plural') }}</li>
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
                    <div class="col-md-12">
                        <div class="card" style='box-shadow: 0 0 1px rgba(0,0,0,0), 0 1px 3px rgba(0,0,0,0);'>

                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ Lang::get('administrable::messages.view.comment.plural') }}</h3>
                                        <div class="btn-group float-right">
                                            <a href="#" class="btn btn-danger d-none" data-model="{{ AdminModule::model('comment') }}"
                                                id="delete-all"> <i class="fa fa-trash"></i> {{ Lang::get('administrable::messages.default.deleteall') }}</a>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped" id="list">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="check-all">
                                                                <label class="custom-control-label"
                                                                    for="check-all"></label>
                                                            </div>
                                                        </th>
                                                          <th>#</th>
                                                          <th>{{ Lang::get('administrable::messages.view.comment.name') }}</th>
                                                          <th>{{ Lang::get('administrable::messages.view.comment.email') }}</th>
                                                          <th>{{ Lang::get('administrable::messages.view.comment.content') }}</th>
                                                          <th>{{ Lang::get('administrable::messages.view.comment.approved') }}</th>
                                                          <th>{{ Lang::get('administrable::messages.view.comment.createdat') }}</th>
                                                          {{-- add fields here --}}
                                                          <th>{{ Lang::get('administrable::messages.view.comment.actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($comments as $comment)
                                                    <tr>
                                                        <td>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" data-check
                                                                    class="custom-control-input"
                                                                    data-id="{{ $comment->id }}"
                                                                    id="check-{{ $comment->id }}">
                                                                <label class="custom-control-label"
                                                                    for="check-{{ $comment->id }}"></label>
                                                            </div>
                                                        </td>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $comment->getCommenterName() }}</td>
                                                        <td>{{ $comment->getCommenterEmail() }}</td>
                                                        <td>{!! Str::limit(strip_tags($comment->comment),50) !!}</td>
                                                        <td>
                                                            @if ($comment->approved)
                                                              <a data-provide="tooltip" title="{{ Lang::get('administrable::messages.view.comment.approved') }}"><i class="fas fa-circle text-success"></i></a>
                                                            @else
                                                              <a data-provide="tooltip" title="{{ Lang::get('administrable::messages.view.comment.disapproved') }}"><i class="fas fa-circle text-secondary"></i></a>
                                                            @endif
                                                        </td>

                                                        <td>{{ $comment->created_at->format('d/m/Y h:i') }}</td>
                                                        {{-- add values here --}}
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                @unless ($comment->approved)
                                                                  <a href="{{ back_route('comment.approved', $comment) }}"
                                                                    class="btn btn-success" data-toggle="tooltip"
                                                                    data-placement="top" title="{{ Lang::get('administrable::messages.view.comment.approved') }}"><i
                                                                        class="fas fa-check"></i></a>
                                                                @endunless
                                                                <a href="{{ back_route('comment.edit', $comment) }}"
                                                                    class="btn btn-info" data-toggle="tooltip"
                                                                    data-placement="top" title="{{ Lang::get('administrable::messages.default.edit') }}"><i
                                                                        class="fas fa-edit"></i></a>
                                                                <a href="#" class="btn btn-secondary" title="{{ Lang::get('administrable::messages.view.comment.reply') }}" data-toggle="modal"
                                                                    data-target="#answerModal{{ $comment->getKey() }}"><i class="fas fa-undo"></i></a>
                                                                <a href="{{ back_route('comment.destroy', $comment) }}"
                                                                    data-method="delete"
                                                                    data-confirm="{{ Lang::get('administrable::messages.view.comment.destroy') }}"
                                                                    class="btn btn-danger" data-toggle="tooltip"
                                                                    data-placement="top" title="{{ Lang::get('administrable::messages.default.default') }}"><i
                                                                        class="fas fa-trash"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <div class="modal fade" id="answerModal{{ $comment->getKey() }}" tabindex="-1"
                                                        aria-labelledby="answerModal{{ $comment->getKey() }}Label" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="answerModal{{ $comment->getKey() }}Label">Répondre au commentaire de
                                                                        :<i>`{{ $comment->getCommenterName() }}`</i></h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="alert alert-secondary">
                                                                        {{ $comment->comment }}
                                                                    </div>
                                                                    <form action="{{ back_route('comment.reply', $comment) }}" method="post"
                                                                        id="answerComment{{ $comment->getKey() }}">
                                                                        @csrf
                                                                        <input type="hidden" name="child_id" value="{{ $comment->getKey() }}">

                                                                        <div class="form-group">
                                                                            <input type="text" name="guest_name" class="form-control" placeholder="{{ Lang::get('administrable::messages.view.comment.name') }}"
                                                                                value="{{ get_guard('full_name') }}">
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <input type="text" name="guest_email" class="form-control" placeholder="{{ Lang::get('administrable::messages.view.comment.email') }}"
                                                                                value="{{ get_guard('email') }}">
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <textarea name="comment" class="form-control" placeholder="{{ Lang::get('administrable::messages.view.comment.answer') }}"
                                                                                rows="10" required></textarea>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ Lang::get('administrable::messages.default.cancel') }}</button>
                                                                    <button type="submit" form="answerComment{{ $comment->getKey() }}" class="btn btn-primary"><i
                                                                            class="fa fa-plus"></i> {{ Lang::get('administrable::messages.view.comment.reply') }}</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.mail-box-messages -->
                            </div>

                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>

        </div>
        <!-- /.card-body -->

        <!-- /.card -->

    </section>
    <!-- /.content -->
</div>

<x-administrable::datatable />

@include(back_view_path('partials._deleteAll'))
@endsection
