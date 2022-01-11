@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.view.user.plural'))

@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1></h1> --}}
                </div>
                <div class="col-sm-6">
                    <div class='float-sm-right'>
                         <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get("administrable::messages.default.dashboard") }}</a></li>
                            <li class="breadcrumb-item active">{{ Lang::get('administrable::messages.view.user.plural') }}</li>
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
                                        <h3 class="card-title">{{ Lang::get('administrable::messages.view.user.plural') }}</h3>
                                        <div class="btn-group float-right">
                                            <a href="{{ back_route('user.create') }}" class="btn  btn-primary"> <i
                                                    class="fa fa-plus"></i> {{ Lang::get('administrable::messages.default.add') }}</a>
                                            <a href="#" class="btn btn-danger d-none" data-model="\{{ AdminModule::getUserModel() }}"
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
                                                       <th>#</th>
                                                       <th>{{ Lang::get('administrable::messages.view.user.name') }}</th>
                                                       <th>{{ Lang::get('administrable::messages.view.user.pseudo') }}</th>
                                                       <th>{{ Lang::get('administrable::messages.view.user.email') }}</th>
                                                       <th>{{ Lang::get('administrable::messages.view.user.createdat') }}</th>
                                                       {{-- add fields here --}}

                                                       <th>{{ Lang::get('administrable::messages.view.user.actions') }}</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($users as $user)
                                                    <tr>
                                                        <td>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" data-check
                                                                    class="custom-control-input"
                                                                    data-id="{{ $user->getKey() }}"
                                                                    id="check-{{ $user->getKey() }}">
                                                                <label class="custom-control-label"
                                                                    for="check-{{ $user->getKey() }}"></label>
                                                            </div>
                                                        </td>
                                                         <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $user->name }}</td>
                                                        <td>{{ $user->pseudo }}</td>
                                                        <td>{{ $user->email }}</td>

                                                        <td>{{ $user->created_at->format('d/m/Y h:i') }}</td>
                                                        {{-- add values here --}}
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <a href="{{ back_route('user.show', $user->getKey()) }}"
                                                                    class="btn btn-primary" data-toggle="tooltip"
                                                                    data-placement="top" title="{{ Lang::get('administrable::messages.default.show') }}"><i
                                                                        class="fas fa-eye"></i></a>

                                                                    <!-- Button trigger modal -->
                                                                  <button type="button" title="{{ Lang::get('administrable::messages.view.user.changepassword') }}" class="btn btn-secondary" data-toggle="modal" data-target="#changePassword{{ $user->id }}">
                                                                      <i class="fas fa-lock"></i>
                                                                  </button>

                                                                       <a href="{{ back_route('model.clone', get_clone_model_params($user)) }}"
                                                                class="btn btn-secondary" data-toggle="tooltip"
                                                                data-placement="top" title="{{ Lang::get('administrable::messages.default.clone') }}"><i
                                                                    class="fas fa-clone"></i></a>

                                                                <a href="{{ back_route('user.edit', $user->getKey()) }}"
                                                                    class="btn btn-info" data-toggle="tooltip"
                                                                    data-placement="top" title="{{ Lang::get('administrable::messages.default.edit') }}"><i
                                                                        class="fas fa-edit"></i></a>
                                                                <a href="{{ back_route('user.destroy', $user->getKey()) }}"
                                                                    data-method="delete"
                                                                    data-confirm="{{ Lang::get('administrable::messages.view.user.destroy') }}"
                                                                    class="btn btn-danger" data-toggle="tooltip"
                                                                    data-placement="top" title="{{ Lang::get('administrable::messages.default.delete') }}"><i
                                                                        class="fas fa-trash"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                     <!-- Modal -->
                                                    <div class="modal fade" id="changePassword{{ $user->getKey() }}" tabindex="-1" role="dialog"
                                                        aria-labelledby="changePassword{{ $user->getKey() }}Title" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="changePassword{{ $user->getKey() }}Title">
                                                                        {{ Lang::get('administrable::messages.view.user.changepassword') }}
                                                                        <span class="font-weight-bold">({{ $user->name }})</span></h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <form action="{{ back_route('user.changepassword', $user) }}" method="POST">
                                                                <div class="modal-body">
                                                                        @method('put')
                                                                        @csrf
                                                                        <div class="form-group">
                                                                            <label for="">{{ Lang::get('administrable::messages.view.user.name') }} </label>
                                                                            <input type="text" class="form-control" value="{{ $user->name }}" disabled>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="">{{ Lang::get('administrable::messages.view.user.newpassword') }} <span class="text-danger">*</span></label>
                                                                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8">
                                                                            @error('password')
                                                                            <span class="invalid-feedback" role="alert">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                            @enderror
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="">{{ Lang::get('administrable::messages.view.user.newpasswordconfirmation') }} <span class="text-danger">*</span> </label>
                                                                            <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" required minlength="8">
                                                                            @error('password_confirmation')
                                                                            <span class="invalid-feedback" role="alert">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                            @enderror
                                                                        </div>

                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ Lang::get('administrable::messages.default.cancel') }}</button>
                                                                    <button type="submit" class="btn btn-primary">{{ Lang::get('administrable::messages.default.save') }}</button>
                                                                </div>
                                                            </form>
                                                            </div>
                                                        </div>
                                                    </div>
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




