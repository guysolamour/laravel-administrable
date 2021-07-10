@extends( back_view_path('layouts.base') )

@section('title',  Lang::get("administrable::messages.default.add"))

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1>{{ Lang::get("administrable::messages.default.add") }}</h1> --}}
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get("administrable::messages.default.dashboard") }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route('page.index') }}">{{ Lang::get("administrable::messages.view.page.plural") }}</a></li>
                            <li class="breadcrumb-item active">{{ Lang::get("administrable::messages.default.add") }}</li>
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
                <h3 class="card-title">{{ Lang::get("administrable::messages.default.add") }}</h3>
            </div>
            <div class="card-body">
                <div class='row'>
                    <div class='col-md-12'>
                        {!! form_rest($form) !!}
                    </div>
                </div>
            </div>

        </div>
         <x-administrable::seoform  :model="$form->getModel()" />

         <x-administrable::select2 />
        <div class="form-group">
            <button type="submit" class="btn btn-success"> <i class="fa fa-save"></i> {{ Lang::get("administrable::messages.default.save") }}</button>
        </div>
        {!! form_end($form) !!}
    </section>
</div>
@endsection
