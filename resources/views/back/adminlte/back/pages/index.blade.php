@extends( back_view_path('layouts.base') )

@section('title',  Lang::get("administrable::messages.view.page.plural"))

@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">

                </div>
                <div class="col-sm-6">
                    <div class='float-sm-right'>
                         <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get("administrable::messages.default.dashboard") }}</a></li>
                            <li class="breadcrumb-item active">{{ Lang::get("administrable::messages.view.page.plural") }}</li>
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
                        <div class="card" >

                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                <div class="card">
                                    <div class="card-header">
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
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped" id="list">
                                                <thead>
                                                    <tr>
                                                        @role('super-' . config('administrable.guard'), config('administrable.guard'))
                                                        <th>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="check-all">
                                                                <label class="custom-control-label"
                                                                    for="check-all"></label>
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
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" data-check
                                                                    class="custom-control-input"
                                                                    data-id="{{ $page->getKey() }}"
                                                                    id="check-{{ $page->getKey() }}">
                                                                <label class="custom-control-label"
                                                                    for="check-{{ $page->getKey() }}"></label>
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
                                                                <a href="{{ back_route('page.show', $page) }}"
                                                                    class="btn btn-primary" data-toggle="tooltip"
                                                                    data-placement="top" title="{{ Lang::get("administrable::messages.default.show") }}"><i
                                                                        class="fas fa-eye"></i></a>
                                                                <a href="{{ back_route('model.clone', get_clone_model_params($page)) }}"
                                                                class="btn btn-secondary" data-toggle="tooltip"
                                                                data-placement="top" title="{{ Lang::get("administrable::messages.default.clone") }}"><i
                                                                    class="fas fa-clone"></i></a>

                                                                <a href="{{ back_route('page.edit', $page) }}"
                                                                    class="btn btn-primary" data-toggle="tooltip"
                                                                    data-placement="top" title="{{ Lang::get("administrable::messages.default.edit") }}"><i
                                                                        class="fas fa-edit"></i></a>

                                                                @role('super-' . config('administrable.guard'), config('administrable.guard'))
                                                                <a href="{{ back_route('page.destroy', $page) }}"
                                                                    data-method="delete"
                                                                    data-confirm="{{ Lang::get("administrable::messages.view.page.destroy") }}"
                                                                    class="btn btn-danger" data-toggle="tooltip"
                                                                    data-placement="top" title="{{ Lang::get("administrable::messages.default.delete") }}"><i
                                                                        class="fas fa-trash"></i></a>
                                                                @endrole
                                                            </div>
                                                        </td>
                                                    </tr>
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
    </section>
    <!-- /.content -->
</div>

<x-administrable::datatable />

@role('super-' . config('administrable.guard'), config('administrable.guard'))
@include(back_view_path('partials._deleteAll'))
@endrole
@endsection