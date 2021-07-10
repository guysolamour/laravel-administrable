@extends(back_view_path('layouts.base'))


@section('title', Lang::get('administrable::extensions.mailbox.label'))


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
                                <a href="{{ route(config('administrable.guard'). '.dashboard') }}"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ Lang::get('administrable::extensions.mailbox.label') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">
                            {{ Lang::get('administrable::extensions.mailbox.label') }}
                            @if ($unread != 0)
                                (<small>{{ $unread }} {{ Lang::get('administrable::extensions.mailbox.view.unread') }}
                            @endif
                        </h3>
                        <div class="btn-group float-right">
                            <a href="#" class="btn btn-danger d-none" data-model="{{ AdminExtension::model('mailbox') }}"
                                id="delete-all">
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
                                <th>{{ Lang::get('administrable::extensions.mailbox.view.read') }}</th>
                                <th>{{ Lang::get('administrable::extensions.mailbox.view.name') }}</th>
                                <th>{{ Lang::get('administrable::extensions.mailbox.view.content') }}</th>
                                <th>{{ Lang::get('administrable::extensions.mailbox.view.createdat') }}</th>
                                {{-- add fields here --}}
                                <th>{{ Lang::get('administrable::extensions.mailbox.view.actions') }}</th>
                            </thead>
                            <tbody>
                                @foreach($mailboxes as $mailbox)
                                <tr>
                                    <td>
                                        <div class="checkbox-fade fade-in-success ">
                                            <label for="check-{{ $mailbox->id }}">
                                                <input type="checkbox" data-check data-id="{{ $mailbox->id }}"
                                                    id="check-{{ $mailbox->id }}">
                                                <span class="cr">
                                                    <i class="cr-icon ik ik-check txt-success"></i>
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                   <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <a href="Javascript:void(1)">
                                            <i class=" {{ ($mailbox->read) ? 'far fa-star' : 'fas fa-star'}} text-warning"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="block-email">{{ $mailbox->name }}</span>
                                    </td>
                                    <td>{{ Str::limit($mailbox->content,50) }}</td>
                                    <td>{{ $mailbox->created_at->diffForHumans() }}</td>
                                    {{-- add values here --}}
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ back_route('extensions.mailbox.mailbox.show', $mailbox) }}" class="btn btn-primary" data-toggle="tooltip"
                                                data-placement="top" title="{{ Lang::get('administrable::messages.default.show') }}"><i class="fas fa-eye"></i></a>
                                            <a href="{{ back_route('extensions.mailbox.mailbox.destroy', $mailbox) }}" data-method="delete"
                                                data-confirm="{{ Lang::get('administrable::extensions.mailbox.view.destroy') }}" class="btn btn-danger"
                                                data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::messages.default.delete') }}"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
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
