@extends(back_view_path('layouts.base'))


@section('title', Lang::get('administrable::extensions.mailbox.label'))


@section('content')

<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') .'.dashboard') }}">{{ Lang::get('administrable::messages.default.dashboard') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ Lang::get('administrable::extensions.mailbox.label') }}</a></li>
            </ol>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between mb-3">
            <h3>
                {{ Lang::get('administrable::extensions.mailbox.label') }}
                @if ($unread != 0)
                    (<small>{{ $unread }} {{ Lang::get('administrable::extensions.mailbox.view.unread') }}
                @endif
            </h3>
            <a href="#" class="btn btn-danger d-none" data-model="\{{ AdminExtension::model('mailbox') }}" id="delete-all"><i
                    class="fa fa-trash"></i> &nbsp; {{ Lang::get('administrable::messages.default.deleteall') }}</a>
        </div>

        <table class="table table-vcenter card-table" id='list'>
            <thead>
                <th></th>
                <th>
                    <label class="form-check" for="check-all">
                        <input class="form-check-input" type="checkbox" id="check-all">
                        <span class="form-check-label"></span>
                    </label>
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
                <tr class="tr-shadow">
                    <td></td>
                    <td>
                        <label class="form-check" for="check-{{ $mailbox->id }}">
                            <input class="form-check-input" type="checkbox" data-check data-id="{{ $mailbox->id }}"
                                id="check-{{ $mailbox->id }}" <span class="form-check-label"></span>
                        </label>
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
                    <td>{{ Str::limit($mailbox->content, 50) }}</td>
                    <td>{{ $mailbox->created_at->diffForHumans() }}</td>
                    {{-- add values here --}}
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ back_route('extensions.mailbox.mailbox.show', $mailbox) }}" class="btn btn-primary"
                                data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::messages.default.show') }}"><i
                                    class="fas fa-eye"></i></a>
                            <a href="{{ back_route('extensions.mailbox.mailbox.destroy', $mailbox) }}" data-method="delete"
                                data-confirm="{{ Lang::get('administrable::extensions.mailbox.view.destroy') }}"
                                class="btn btn-danger" data-toggle="tooltip" data-placement="top"
                                title="{{ Lang::get('administrable::messages.default.delete') }}"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</div>


<x-administrable::datatable />

@include(back_view_path('partials._deleteAll'))

@endsection
