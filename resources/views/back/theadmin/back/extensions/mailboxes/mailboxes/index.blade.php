@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::extensions.mailbox.label'))


@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') .'.dashboard') }}">{{ Lang::get('administrable::messages.default.dashboard') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ Lang::get('administrable::extensions.mailbox.label') }}</li>
            </ol>
        </nav>

    </div>

    <div class="card">
        {{-- <h4 class="card-title">
                {{ Lang::get('administrable::extensions.mailbox.label') }}
            </h4> --}}

        <div class="card-body">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title h3">
                        {{ Lang::get('administrable::extensions.mailbox.label') }}
                        @if ($unread != 0)
                            (<small>{{ $unread }} {{ Lang::get('administrable::extensions.mailbox.view.unread') }}
                        @endif
                    </h5>
                    <div class="btn-group">

                        <a href="#" class="btn btn-sm btn-label btn-round btn-danger d-none" data-model="\{{ AdminExtension::model('mailbox') }}" id="delete-all"><label><i
                                    class="fa fa-trash"></i></label> {{ Lang::get('administrable::messages.default.deleteall') }}</a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <table class="table table-separated" id='list'>
                            <thead>
                                 <tr>
                                    <th>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="check-all">
                                            <label class="form-check-label" for="check-all"></label>
                                        </div>
                                    </th>
                                    <th></th>
                                    <th>{{ Lang::get('administrable::extensions.mailbox.view.read') }}</th>
                                    <th>{{ Lang::get('administrable::extensions.mailbox.view.name') }}</th>
                                    <th>{{ Lang::get('administrable::extensions.mailbox.view.content') }}</th>
                                    <th>{{ Lang::get('administrable::extensions.mailbox.view.createdat') }}</th>
                                    {{-- add fields here --}}
                                    <th>{{ Lang::get('administrable::extensions.mailbox.view.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                              @foreach($mailboxes as $mailbox)
                                <tr>
                                    <td>
                                         <div class="form-check">
                                            <input type="checkbox" data-check class="form-check-input" data-id="{{ $mailbox->id }}"
                                                id="check-{{ $mailbox->id }}">
                                            <label class="form-check-label" for="check-{{ $mailbox->id }}"></label>
                                        </div>
                                    </td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><a href="Javascript:void(1)"><i class=" {{ ($mailbox->read) ? 'far fa-star' : 'fas fa-star'}} text-warning"></i></a></td>
                                    <td>{{ $mailbox->name }}</td>
                                    <td>{{ Str::limit($mailbox->content, 25) }}</td>
                                    <td>{{ $mailbox->created_at->diffForHumans() }}</td>
                                    {{-- add values here --}}
                                    <td>
                                        <nav class="nav no-gutters gap-2 fs-16">
                                            <a class="nav-link hover-primary" href="{{ back_route('extensions.mailbox.mailbox.show', $mailbox) }}" data-provide="tooltip"
                                                title="{{ Lang::get('administrable::messages.default.show') }}" ><i class="ti-eye"></i></a>

                                            <a class="nav-link hover-danger" href="{{ back_route('extensions.mailbox.mailbox.destroy', $mailbox) }}"
                                            data-method="delete" data-confirm="{{ Lang::get('administrable::extensions.mailbox.view.destroy') }}" data-provide="tooltip"  title="{{ Lang::get('administrable::messages.default.delete') }}"><i
                                                    class="ti-close"></i></a>
                                        </nav>
                                    </td>
                                </tr>
                            </tbody>
                            @endforeach
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
