@extends( back_view_path('layouts.base') )

@section('title',  Lang::get("administrable::messages.view.page.plural"))

@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get("administrable::messages.default.dashboard") }}</a></li>
                <li class="breadcrumb-item active">{{ Lang::get("administrable::messages.view.page.plural") }}</li>
            </ol>
        </nav>

    </div>

    <div class="card">
        {{-- <h4 class="card-title">
                {{ Lang::get("administrable::messages.view.page.plural") }}
            </h4> --}}

        <div class="card-body">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title h3">
                        {{ Lang::get("administrable::messages.view.page.plural") }}
                    </h5>
                    <div class="btn-group">
                        <a href="{{ back_route('page.create') }}"
                            class="btn btn-sm btn-label btn-round btn-primary"><label><i class="fa fa-plus"></i></label>
                            {{ Lang::get("administrable::messages.default.add") }}</a>
                        @role('super-' . config('administrable.guard'), config('administrable.guard'))
                        <a href="#" data-model="\{{ AdminModule::model('page') }}" id="delete-all"
                            class="btn btn-sm btn-label btn-round btn-danger d-none"><label><i
                                    class="fa fa-trash"></i></label>  {{ Lang::get("administrable::messages.default.deleteall") }}</a>
                        @endrole

                    </div>
                </div>

                <table class="table table-hover table-has-action" id='list'>
                    <thead>
                        <tr>
                            @role('super-' . config('administrable.guard'), config('administrable.guard'))
                            <th>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="check-all">
                                    <label class="form-check-label" for="check-all"></label>
                                </div>
                            </th>
                            @endrole
                            <th></th>
                            <th>{{ Lang::get("administrable::messages.view.page.code") }}</th>
                            <th>{{ Lang::get("administrable::messages.view.page.name") }}</th>
                            <th>{{ Lang::get("administrable::messages.view.page.route") }}</th>
                            <th>{{ Lang::get("administrable::messages.view.page.uri") }}</th>
                            {{-- add fields here --}}
                            <th>{{ Lang::get("administrable::messages.view.page.actions") }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pages as $page)
                        <tr>
                            @role('super-' . config('administrable.guard'), config('administrable.guard'))
                            <td>
                                <div class="form-check">
                                    <input type="checkbox" data-check class="form-check-input"
                                        data-id="{{ $page->id }}" id="check-{{ $page->id }}">
                                    <label class="form-check-label" for="check-{{ $page->id }}"></label>
                                </div>
                            </td>
                            @endrole
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $page->code }}</td>
                            <td><a class="text-dark">{{ $page->name }}</a></td>
                            <td>{{ $page->route }}</td>
                            <td>
                                @if($page->uri)
                                    <a href="{{ $page->uri }}" target="_blank">{{ $page->uri }}</a>
                                @else
                                    {{ Lang::get("administrable::messages.view.page.nouri") }}
                                @endif
                            </td>
                            {{-- add values here --}}

                            <td>
                                <nav class="nav no-gutters gap-2 fs-16">
                                    <a class="nav-link hover-primary"
                                        href="{{ back_route('page.show', $page) }}" data-provide="tooltip"
                                        title="{{ Lang::get("administrable::messages.default.show") }}"><i class="ti-eye"></i></a>

                                    <a class="nav-link hover-primary" href="{{ back_route('model.clone', get_clone_model_params($page)) }}" data-provide="tooltip"
                                        title="{{ Lang::get("administrable::messages.default.clone") }}"><i class="ti-layers"></i></a>

                                     <a class="nav-link hover-primary" href="{{ back_route('page.edit', $page) }}" data-provide="tooltip"
                                            title="{{ Lang::get("administrable::messages.default.edit") }}"><i class="ti-pencil"></i></a>

                                    @role('super-' . config('administrable.guard'), config('administrable.guard'))
                                    <a class="nav-link hover-danger"
                                        href="{{ back_route('page.destroy', $page) }}" data-provide="tooltip"
                                        title="{{ Lang::get("administrable::messages.default.delete") }}" data-method="delete"
                                        data-confirm="{{ Lang::get("administrable::messages.view.page.destroy") }}"
                                        ><i class="ti-close"></i></a>
                                    @endrole
                                </nav>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>


</div>

<x-administrable::datatable />

@role('super-' . config('administrable.guard'), config('administrable.guard'))
@include(back_view_path('partials._deleteAll'))
@endrole

@endsection
