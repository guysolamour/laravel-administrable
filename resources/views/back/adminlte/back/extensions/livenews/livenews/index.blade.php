@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::extensions.livenews.label'))

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
                            <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get('administrable::messages.default.dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ Lang::get('administrable::extensions.livenews.label') }}</li>
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
                        <div class="card" style='box-shadow: 0 0 1px rgba(0,0,0,0), 0 1px 3px rgba(0,0,0,0);'>

                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ Lang::get('administrable::extensions.livenews.label') }}</h3>
                                        <div class="btn-group float-right">
                                            <a href="{{ back_route('extensions.livenews.livenews.create') }}" class="btn  btn-primary"> <i
                                                    class="fa fa-plus"></i> {{ Lang::get("administrable::messages.default.add") }}</a>


                                            <a href="#" class="btn btn-danger d-none" data-model="{{ AdminExtension::model('livenews') }}"
                                                id="delete-all"> <i class="fa fa-trash"></i> {{ Lang::get('administrable::messages.default.deleteall') }}</a>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="table-responsive">
                              <table class="table table-hover table-striped" id="list">
                                  <thead>
                                      <tr>
                                          <th>
                                              <div class="custom-control custom-checkbox">
                                                  <input type="checkbox" class="custom-control-input"
                                                      id="check-all">
                                                  <label class="custom-control-label"
                                                      for="check-all"></label>
                                              </div>
                                          </th>
                                          <th>{{ Lang::get('administrable::extensions.livenews.view.text') }}</th>
                                          <th>{{ Lang::get('administrable::extensions.livenews.view.size') }}</th>
                                          <th>{{ Lang::get('administrable::extensions.livenews.view.text_color') }}</th>
                                          <th>{{ Lang::get('administrable::extensions.livenews.view.back_color') }}</th>
                                          <th>{{ Lang::get('administrable::extensions.livenews.view.started_at') }}</th>
                                          <th>{{ Lang::get('administrable::extensions.livenews.view.ended_at') }}</th>
                                          {{-- add fields here --}}
                                          <th>{{ Lang::get('administrable::extensions.livenews.view.actions') }}</th>

                                      </tr>
                                  </thead>
                                  <tbody>
                                      @foreach($livenews as $news)
                                      <tr>
                                          <td>

                                              <div class="custom-control custom-checkbox">
                                                  <input type="checkbox" data-check
                                                      class="custom-control-input"
                                                      data-id="{{ $news->getKey() }}"
                                                      id="check-{{ $news->getKey() }}">
                                                  <label class="custom-control-label"
                                                      for="check-{{ $news->getKey() }}"></label>
                                              </div>
                                          </td>
                                          <td>{{ $news->content }}</td>
                                          <td>{{ $news->size }}</td>
                                          <td>
                                              <p style="width: 100%; height: 30px; background-color: {{ $news->text_color }}"></p>
                                          </td>
                                          <td>
                                              <p style="width: 100%; height: 30px; background-color: {{ $news->background_color }}"></p>
                                          </td>
                                          <td>
                                              {{ format_date($news->started_at) }}
                                          </td>
                                          <td>
                                              {{ format_date($news->ended_at) }}
                                          </td>
                                          {{-- add values here --}}
                                          <td>
                                              <div class="btn-group" role="group">
                                                  <a href="{{ back_route('model.clone', get_clone_model_params($news)) }}"
                                                  class="btn btn-secondary" data-toggle="tooltip"
                                                  data-placement="top" title="{{ Lang::get('administrable::messages.default.clone') }}"><i
                                                      class="fas fa-clone"></i></a>

                                                  <a href="{{ back_route('extensions.livenews.livenews.edit', $news) }}"
                                                      class="btn btn-info" data-toggle="tooltip"
                                                      data-placement="top" title="{{ Lang::get('administrable::messages.default.edit') }}"><i
                                                          class="fas fa-edit"></i></a>

                                                  <a href="{{ back_route('extensions.livenews.livenews.destroy', $news) }}"
                                                      data-method="delete"
                                                      data-confirm="{{ Lang::get('administrable::extensions.livenews.view.destroy') }}"
                                                      class="btn btn-danger" data-toggle="tooltip"
                                                      data-placement="top" title="{{ Lang::get('administrable::messages.default.delete') }}"><i
                                                          class="fas fa-trash"></i></a>

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
        <!-- /.card-body -->

        <!-- /.card -->

    </section>
    <!-- /.content -->
</div>

<x-administrable::datatable />

@include(back_view_path('partials._deleteAll'))
@endsection
