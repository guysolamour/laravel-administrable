@extends( back_view_path('layouts.base') )

@section('title',  Lang::get("administrable::messages.view.page.plural"))

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
                            <li class="breadcrumb-item active" aria-current="page">{{ Lang::get("administrable::messages.view.page.plural") }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">{{ Lang::get("administrable::messages.view.page.plural") }}</h3>
                        <div class="btn-group float-right">
                            <a href="{{ back_route('page.create') }}" class="btn  btn-primary"> <i
                                    class="fa fa-plus"></i> {{ Lang::get("administrable::messages.default.add") }}</a>

                            @role('super-' . config('administrable.guard'), config('administrable.guard'))
                            <a href="#" class="btn btn-danger d-none" data-model="{{ AdminModule::model('page') }}" id="delete-all"> <i class="fa fa-trash"></i>
                                {{ Lang::get("administrable::messages.default.deleteall") }}</a>
                            @endrole
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <table class="table table-vcenter card-table" id='list'>
                            <thead>
                                @role('super-' . config('administrable.guard'), config('administrable.guard'))
                                <th>
                                    <div class="checkbox-fade fade-in-success ">
                                        <label for="check-all">
                                            <input type="checkbox" value="" id="check-all">
                                            <span class="cr">
                                                <i class="cr-icon ik ik-check txt-success"></i>
                                            </span>
                                        </label>
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
                            </thead>
                            <tbody>
                                @foreach($pages as $page)
                                <tr class="tr-shadow">
                                    @role('super-' . config('administrable.guard'), config('administrable.guard'))
                                    <td>
                                        <div class="checkbox-fade fade-in-success ">
                                            <label for="check-{{ $page->id }}">
                                                <input type="checkbox" data-check data-id="{{ $page->id }}"
                                                    id="check-{{ $page->id }}">
                                                <span class="cr">
                                                    <i class="cr-icon ik ik-check txt-success"></i>
                                                </span>
                                                {{-- <span>Default</span> --}}
                                            </label>
                                        </div>
                                    </td>
                                    @endrole

                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $page->code }}</td>
                                    <td>{{ $page->name }}</td>
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
                                        <div class="btn-group" role="group">
                                            <a href="{{ back_route('page.show', $page) }}" class="btn btn-primary"
                                                data-toggle="tooltip" data-placement="top" title="{{ Lang::get("administrable::messages.default.show") }}"><i
                                                    class="fas fa-eye"></i></a>

                                             <a href="{{ back_route('model.clone', get_clone_model_params($page)) }}" class="btn btn-secondary"
                                            title="{{ Lang::get("administrable::messages.default.clone") }}"><i class="fas fa-clone"></i></a>

                                            <a href="{{ back_route('page.edit', $page) }}" class="btn btn-info" data-toggle="tooltip"
                                                data-placement="top" title="{{ Lang::get("administrable::messages.default.edit") }}"><i class="fas fa-edit"></i></a>

                                            @role('super-' . config('administrable.guard'), config('administrable.guard'))
                                            <a href="{{ back_route('page.destroy', $page) }}" data-method="delete"
                                                data-confirm="{{ Lang::get("administrable::messages.view.page.destroy") }}"
                                                class="btn btn-danger" data-toggle="tooltip" data-placement="top"
                                                title="{{ Lang::get("administrable::messages.default.delete") }}"><i class="fas fa-trash"></i></a>
                                            @endrole
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

@role('super-' . config('administrable.guard'), config('administrable.guard'))
    @include(back_view_path('partials._deleteAll'))
@endrole
@endsection
