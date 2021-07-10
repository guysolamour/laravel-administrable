@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.view.user.plural'))

@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get("administrable::messages.default.dashboard") }}</a></li>
                <li class="breadcrumb-item active">{{ Lang::get('administrable::messages.view.user.plural') }}</li>
            </ol>
        </nav>

    </div>

    <div class="card">
        {{-- <h4 class="card-title">
                 {{ Lang::get('administrable::messages.view.user.plural') }}
            </h4> --}}

        <div class="card-body">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title h3">
                         {{ Lang::get('administrable::messages.view.user.plural') }}
                    </h5>
                    <div class="btn-group">
                        <a href="{{ back_route('user.create') }}" class="btn btn-sm btn-label btn-round btn-primary"><label><i
                                    class="ti-plus"></i></label> {{ Lang::get('administrable::messages.default.add') }}</a>
                        <a href="#" class="btn btn-sm btn-label btn-round btn-danger d-none" data-model="\{{ AdminModule::getUserModel() }}"
                            id="delete-all"><label><i class="fa fa-trash"></i></label> {{ Lang::get('administrable::messages.default.deleteall') }}</a>

                    </div>
                </div>

                <table class="table table-hover table-has-action" id='list'>
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="check-all">
                                    <label class="form-check-label" for="check-all"></label>
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
                                <div class="form-check">
                                    <input type="checkbox" data-check class="form-check-input" data-id="{{ $user->id }}"
                                        id="check-{{ $user->id }}">
                                    <label class="form-check-label" for="check-{{ $user->id }}"></label>
                                </div>
                            </td>
                              <td>{{ $loop->iteration }}</td>
                              <td>{{ $user->name }}</td>
                              <td>{{ $user->pseudo }}</td>
                              <td>{{ $user->email }}</td>

                              <td>{{ $user->created_at->format('d/m/Y h:i') }}</td>
                              {{-- add values here --}}
                            <td>
                                <nav class="nav no-gutters gap-2 fs-16">
                                    <a class="nav-link hover-primary" href="{{ back_route('user.show', $user) }}" data-provide="tooltip"
                                        title="{{ Lang::get('administrable::messages.default.show') }}"><i class="ti-eye"></i></a>
                                    <a class="nav-link hover-primary" href="{{ back_route('model.clone', get_clone_model_params($user)) }}" data-provide="tooltip"
                                        title="{{ Lang::get('administrable::messages.default.clone') }}"><i class="ti-layers"></i></a>
                                    <a class="nav-link hover-primary" href="{{ back_route('user.edit', $user) }}" data-provide="tooltip"
                                        title="{{ Lang::get('administrable::messages.default.edit') }}"><i class="ti-pencil"></i></a>
                                    <!-- Button trigger modal -->
                                     <a type="button" title="{{ Lang::get('administrable::messages.view.user.changepassword') }}" class="nav-link hover-primary" data-toggle="modal" data-target="#changePassword{{ $user->id }}">
                                        <i class="ti-lock"></i>
                                    </a>
                                    <a class="nav-link hover-danger" href="#" data-provide="tooltip" title="{{ Lang::get('administrable::messages.default.delete') }}"
                                        data-method="delete"
                                        data-confirm="{{ Lang::get('administrable::messages.view.user.destroy') }}"
                                        ><i class="ti-close"></i></a>
                                </nav>
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
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">title="{{ Lang::get('administrable::messages.default.cancel') }}"</button>
                                        <button type="submit" class="btn btn-primary">title="{{ Lang::get('administrable::messages.default.save') }}"</button>
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

<x-administrable::datatable />
@include(back_view_path('partials._deleteAll'))

@endsection
