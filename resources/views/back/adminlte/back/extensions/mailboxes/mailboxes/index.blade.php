@extends(back_view_path('layouts.base'))


@section('title', Lang::get('administrable::extensions.mailbox.label'))


@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">

          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') .'.dashboard') }}">{{ Lang::get('administrable::messages.default.dashboard') }}</a></li>
              <li class="breadcrumb-item active">{{ Lang::get('administrable::extensions.mailbox.label') }}</li>
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
          <div class='row'>
             <div class="col-md-12">
                      <div class="card" style='box-shadow: 0 0 1px rgba(0,0,0,0), 0 1px 3px rgba(0,0,0,0);'>
                        <div class="card-header">
                            <h3 class="card-title">
                               {{ Lang::get('administrable::extensions.mailbox.label') }}
                                @if ($unread != 0)
                                    (<small>{{ $unread }} {{ Lang::get('administrable::extensions.mailbox.view.unread') }}
                                @endif
                            </h3>
                            <div class="btn-group float-right">
                                <a href="#" class="btn btn-danger d-none" data-model="\{{ AdminExtension::model('mailbox') }}" id="delete-all"> <i
                                        class="fa fa-trash"></i> {{ Lang::get('administrable::messages.default.deleteall') }}</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                          <div class="table-responsive">
                            <table class="table table-hover table-striped" id="list">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="check-all">
                                            <label class="custom-control-label" for="check-all"></label>
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
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" data-check class="custom-control-input" data-id="{{ $mailbox->id }}"
                                                    id="check-{{ $mailbox->id }}">
                                                <label class="custom-control-label" for="check-{{ $mailbox->id }}"></label>
                                            </div>
                                        </td>
                                      <td>{{ $loop->iteration }}</td>
                                      <td><a href="Javascript:void(1)"><i class=" {{ ($mailbox->read) ? 'far fa-star' : 'fas fa-star'}} text-warning"></i></a></td>
                                      <td><a href="Javascript:void(1)">{{ $mailbox->name }}</a></td>
                                      <td>{{ Str::limit($mailbox->content, 25) }}</td>
                                      <td>{{ $mailbox->created_at->diffForHumans() }}</td>
                                      {{-- add values here --}}
                                      <td>
                                          <div class="btn-group" role="group">
                                              <a href="{{ back_route('extensions.mailbox.mailbox.show', $mailbox) }}"
                                              title="{{ Lang::get('administrable::messages.default.show') }}" class="btn btn-info"><i class="fas fa-eye"></i></a>
                                              <a href="{{ back_route('extensions.mailbox.mailbox.destroy', $mailbox) }}"
                                               data-method="delete"
                                               data-confirm="{{ Lang::get('administrable::extensions.mailbox.view.destroy') }}" class="btn btn-danger"><i class="fas fa-trash"></i></a>
                                          </div>
                                      </td>
                                  </tr>
                              @endforeach
                              </tbody>
                            </table>
                            <!-- /.table -->
                          </div>
                        </div>

                      </div>
                    </div>
          </div>
        </div>
      </div>
      <!-- /.card -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<x-administrable::datatable />
@include(back_view_path('partials._deleteAll'))

@endsection
