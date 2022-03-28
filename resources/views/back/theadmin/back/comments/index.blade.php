@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.view.comment.plural'))

@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
             <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get('administrable::messages.default.dashboard') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ Lang::get('administrable::messages.view.comment.plural') }}</a></li>
            </ol>
        </nav>

    </div>

    <div class="card">
        {{-- <h4 class="card-title">
                {{ Lang::get('administrable::messages.view.comment.plural') }}
            </h4> --}}

        <div class="card-body">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title h3">
                        {{ Lang::get('administrable::messages.view.comment.plural') }}
                    </h5>
                    <div class="btn-group">
                        <a href="#" data-model="\{{ AdminModule::model('comment') }}" id="delete-all"
                            class="btn btn-sm btn-label btn-round btn-danger d-none"><label><i class="fa fa-trash"></i></label>
                            {{ Lang::get('administrable::messages.default.deleteall') }}</a>
                    </div>
                </div>

                <table class="table table-hover table-has-action" id='list'>
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="check-all">
                                    <label class="form-check-label" for="check-all"></label>
                                </div>
                            </th>
                            <th></th>
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
                                <div class="form-check">
                                    <input type="checkbox" data-check class="form-check-input" data-id="{{ $comment->id }}"
                                        id="check-{{ $comment->id }}">
                                    <label class="form-check-label" for="check-{{ $comment->id }}"></label>
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
                                <nav class="nav no-gutters gap-2 fs-16">
                                    @unless ($comment->approved)
                                    <a class="nav-link hover-primary" href="{{ back_route('comment.approved', $comment) }}" data-provide="tooltip"
                                        title="{{ Lang::get('administrable::messages.view.comment.approved') }}"><i class="ti-check"></i></a>
                                    @endunless
                                    <a class="nav-link hover-primary" href="{{ back_route('comment.edit', $comment) }}" data-provide="tooltip"
                                        title="{{ Lang::get('administrable::messages.default.edit') }}"><i class="ti-pencil"></i></a>
                                    <a href="#" class="btn btn-secondary" title="{{ Lang::get('administrable::messages.view.comment.reply') }}" data-toggle="modal"
                                    data-target="#answerModal{{ $comment->getKey() }}"><i class="fas fa-undo"></i></a>
                                    <a class="nav-link hover-danger" href="{{ back_route('comment.destroy',$comment) }}" data-provide="tooltip"
                                    data-method="delete"
                                    data-confirm="{{ Lang::get('administrable::messages.view.comment.destroy') }}"
                                        title="{{ Lang::get('administrable::messages.default.default') }}"><i class="ti-close"></i></a>
                                </nav>
                            </td>
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
                                                    {{ strip_tags($comment->comment) }}
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
                            </div>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>


<x-administrable::datatable />

@include(back_view_path('partials._deleteAll'))

@endsection
