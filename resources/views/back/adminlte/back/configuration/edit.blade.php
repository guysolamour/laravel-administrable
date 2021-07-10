@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.view.configuration.plural'))

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>{{ Lang::get('administrable::messages.view.configuration.plural') }}</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get("administrable::messages.default.dashboard") }}</a></li>
            <li class="breadcrumb-item active">{{ Lang::get('administrable::messages.view.configuration.plural') }}</li>
          </ol>
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
          <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="{{ Lang::get('administrable::messages.default.minus') }}">
            <i class="fas fa-minus"></i></button>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
              {!! form_start($form) !!}
                <div class="row">
                    <div class="col-md-6">
                        {!! form_row($form->email) !!}
                    </div>
                    <div class="col-md-6">
                        {!! form_row($form->postal) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        {!! form_row($form->area) !!}
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row pt-3">
                        <div class="col-md-3">
                            <img class="img-fluid" src="{{ $configuration->logo }}" alt="{{ config('app.name') }}">
                        </div>
                        <div class="col-md-9">
                            {!! form_widget($form->logo) !!}
                        </div>
                    </div>
                    </div>
                </div>
                  <div class="row">
                      <div class="col-md-4">
                          {!! form_row($form->cell) !!}
                      </div>
                      <div class="col-md-4">
                          {!! form_row($form->phone) !!}
                      </div>
                      <div class="col-md-4">
                          {!! form_row($form->whatsapp) !!}
                      </div>
                  </div>
                <div class="row">
                    <div class="col-md-6">
                        {!! form_row($form->facebook) !!}
                    </div>
                    <div class="col-md-6">
                        {!! form_row($form->twitter) !!}
                    </div>
                    <div class="col-md-6">
                        {!! form_row($form->linkedin) !!}
                    </div>
                    <div class="col-md-6">
                        {!! form_row($form->youtube) !!}
                    </div>
                </div>
                {!! form_rest($form) !!}
                {{-- add fields here --}}
                 <div class="col-md-12">
                  <div class="form-group">
                    <button class="btn btn-primary btn-block" type="submit"><i class="fa fa-edit"></i>
                      {{ Lang::get('administrable::messages.default.save') }}</button>
                  </div>
                </div>
                {!! form_end($form) !!}
          </div>

        </div>
      </div>

    </div>
  </section>
</div>
@endsection

<x-administrable::custominputfile selector="input[name=logo]" />
