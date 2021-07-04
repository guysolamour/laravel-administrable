@extends( back_view_path('layouts.base') )

@section('title', Lang::get("administrable::messages.default.edit"))

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
                            <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get("administrable::messages.default.dashboard") }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route('page.index') }}">{{ Lang::get("administrable::messages.view.page.plural") }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route('page.show', $page) }}">{{ $page->name }}</a></li>
                            <li class="breadcrumb-item active">{{ Lang::get("administrable::messages.default.edit") }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        {!! form_start($form) !!}
        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ Lang::get("administrable::messages.default.edit") }}: {{ $page->name }}</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip"
                        title="Réduire">
                        <i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class='row'>
                    <div class='col-md-12'>
                        {!! form_row($form->code, ['attr' => ['readonly']]) !!}
                        @role('super-' . config('administrable.guard'), config('administrable.guard'))
                            {!! form_row($form->name) !!}
                            {!! form_row($form->route) !!}
                        @elserole
                            {!! form_row($form->name,  ['attr' => ['readonly']]) !!}
                            {!! form_row($form->route, ['attr' => ['readonly']]) !!}
                        @endrole
                    </div>
                </div>
            </div>

        </div>
         <x-administrable::seoform :model="$form->getModel()" />

         <x-administrable::select2 />

        <div class="form-group">
            <button type="submit" class="btn btn-success"> <i class="fa fa-edit"></i> {{ Lang::get("administrable::messages.default.modify") }}</button>
        </div>
        {!! form_end($form) !!}

    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
@endsection
