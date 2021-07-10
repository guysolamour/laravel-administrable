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
                            <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ Lang::get('administrable::messages.view.comment.plural') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">{{ Lang::get('administrable::messages.view.comment.plural') }}</h3>
                        <div class="btn-group float-right">
                            <a href="#" class="btn btn-danger d-none" data-model="\{{ AdminModule::model('comment') }}" id="delete-all">
                                <i class="fa fa-trash"></i> {{ Lang::get('administrable::messages.default.deleteall') }}</a>
                        </div>
                    </div>

                    <div class="card-body">
                        <table class="table table-vcenter card-table" id='list'>
                            <thead>
                                <th>
                                    <div class="checkbox-fade fade-in-success ">
                                        <label for="check-all">
                                            <input type="checkbox" value="" id="check-all">
                                            <span class="cr">
                                                <i class="cr-icon ik ik-check txt-success"></i>
                                            </span>
                                            {{-- <span>Default</span> --}}
                                        </label>
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
                            </thead>
                            <tbody>
                                @foreach($comments as $comment)
                                <tr>
                                    <td>
                                        <div class="checkbox-fade fade-in-success ">
                                            <label for="check-{{ $comment->id }}">
                                                <input type="checkbox" data-check data-id="{{ $comment->id }}"
                                                    id="check-{{ $comment->id }}">
                                                <span class="cr">
                                                    <i class="cr-icon ik ik-check txt-success"></i>
                                                </span>
                                                {{-- <span>Default</span> --}}
                                            </label>
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
                                            <a href="{{ back_route('comment.approved', $comment) }}" class="btn btn-success" data-toggle="tooltip"
                                                data-placement="top" title="{{ Lang::get('administrable::messages.view.comment.approved') }}"><i class="fas fa-check"></i></a>
                                            @endunless
                                             <a href="{{ back_route('comment.edit', $comment) }}" class="btn btn-info" data-toggle="tooltip"
                                                data-placement="top" title="{{ Lang::get('administrable::messages.default.edit') }}"><i class="fas fa-edit"></i></a>
                                                <a href="#" class="btn btn-secondary" title="Répondre" data-toggle="modal"
                                                data-target="#answerModal{{ $comment->getKey() }}"><i class="fas fa-undo"></i></a>
                                            <a href="{{ back_route('comment.destroy', $comment) }}" data-method="delete"
                                                data-confirm="{{ Lang::get('administrable::messages.view.comment.destroy') }}" class="btn btn-danger"
                                                data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::messages.default.default') }}"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
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
                                                        class="fa fa-plus"></i>  {{ Lang::get('administrable::messages.view.comment.reply') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-administrable::datatable />

@include(back_view_path('partials._deleteAll'))

@endsection
