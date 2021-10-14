@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.view.user.plural'))

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
                        <li class="breadcrumb-item active" aria-current="page">{{ Lang::get('administrable::messages.view.user.plural') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">{{ Lang::get('administrable::messages.view.user.plural') }}</h3>
                    <div class="btn-group float-right">
                        <a href="{{ back_route('user.create') }}" class="btn  btn-primary"> <i
                                class="fa fa-plus"></i> {{ Lang::get('administrable::messages.default.add') }}</a>
                        <a href="#" class="btn btn-danger d-none" data-model="\{{ AdminModule::getUserModel() }}" id="delete-all">
                            <i class="fa fa-trash"></i> {{ Lang::get('administrable::messages.default.deleteall') }}</a>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-vcenter card-table" id='list'>
                        <thead>
                            <th></th>
                            <th>
                                <div class="checkbox-fade fade-in-success ">
                                    <label for="check-all">
                                        <input type="checkbox" value=""  id="check-all">
                                        <span class="cr">
                                            <i class="cr-icon ik ik-check txt-success"></i>
                                        </span>
                                    </label>
                                </div>
                            </th>
                            <th>#</th>
                            <th>{{ Lang::get('administrable::messages.view.user.name') }}</th>
                            <th>{{ Lang::get('administrable::messages.view.user.pseudo') }}</th>
                            <th>{{ Lang::get('administrable::messages.view.user.email') }}</th>
                            <th>{{ Lang::get('administrable::messages.view.user.createdat') }}</th>
                            {{-- add fields here --}}

                            <th>{{ Lang::get('administrable::messages.view.user.actions') }}</th>

                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr class="tr-shadow">

                                <td></td>
                                <td>
                                    <div class="checkbox-fade fade-in-success ">
                                        <label for="check-{{ $user->id }}">
                                            <input type="checkbox" data-check data-id="{{ $user->id }}"  id="check-{{ $user->id }}">
                                            <span class="cr">
                                                <i class="cr-icon ik ik-check txt-success"></i>
                                            </span>
                                        </label>
                                    </div>
                                </td>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->job }}</td>
                                {{-- add values here --}}
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ back_route('user.show', $user) }}" class="btn btn-primary"
                                            data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::messages.default.show') }}"><i class="fas fa-eye"></i></a>

                                         <a href="{{ back_route('model.clone', get_clone_model_params($user)) }}" class="btn btn-secondary"
                                            title="{{ Lang::get('administrable::messages.default.clone') }}"><i class="fas fa-clone"></i></a>


                                        <a href="{{ back_route('user.edit', $user) }}" class="btn btn-info"
                                            data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::messages.default.edit') }}"><i class="fas fa-edit"></i></a>

                                         <!-- Button trigger modal -->
                                          <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#changePassword{{ $user->id }}">
                                              <i class="fas fa-lock"></i>
                                          </button>

                                        <a href="{{ back_route('user.destroy',$user) }}" data-method="delete"
                                            data-confirm="{{ Lang::get('administrable::messages.view.user.destroy') }}" class="btn btn-danger"
                                            data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::messages.default.delete') }}"><i class="fas fa-trash"></i></a>
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
        </div>

    </div>
    </div>
</div>

<x-administrable::datatable />

@include(back_view_path('partials._deleteAll'))
@endsection
