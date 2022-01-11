@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::messages.view.user.plural'))

@section('content')
<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get("administrable::messages.default.dashboard") }}</a></li>
                <li class="breadcrumb-item active">{{ Lang::get('administrable::messages.view.user.plural') }}</li>
            </ol>

            <a href="{{ back_route('user.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i>&nbsp; {{ Lang::get('administrable::messages.default.add') }}</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
       <div class="d-flex justify-content-between mb-3">
            <h3> {{ Lang::get('administrable::messages.view.user.plural') }} </h3>
            <a href="#" class="btn btn-danger d-none" data-model="\{{ AdminModule::getUserModel() }}"
                id="delete-all"> <i class="fa fa-trash"></i> {{ Lang::get('administrable::messages.default.deleteall') }}</a>
        </div>
        <table class="table table-vcenter card-table" id='list'>
            <thead>
                <th></th>
                <th>
                    <label class="au-checkbox" for="check-all">
                        <input type="checkbox" id="check-all">
                        <span class="au-checkmark"></span>
                    </label>
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
                        <label class="form-check" for="check-{{ $user->id }}">
                            <input class="form-check-input" type="checkbox" data-check data-id="{{ $user->id }}" id="check-{{ $user->id }}"
                                <span class="form-check-label"></span>
                        </label>
                    </td>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->pseudo }}</td>
                    <td>{{ $user->email }}</td>

                    <td>{{ $user->created_at->format('d/m/Y h:i') }}</td>
                    {{-- add values here --}}

                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ back_route('user.show', $user->getKey()) }}" class="btn btn-primary"
                                data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::messages.default.show') }}"><i
                                    class="fas fa-eye"></i></a>

                               <!-- Button trigger modal -->
                            <button type="button" title="{{ Lang::get('administrable::messages.view.user.changepassword') }}" class="btn btn-secondary" data-toggle="modal" data-target="#changePassword{{ $user->id }}">
                                <i class="fas fa-lock"></i>
                            </button>

                                <a href="{{ back_route('model.clone', get_clone_model_params($user)) }}" class="btn btn-secondary" data-toggle="tooltip"
                              data-placement="top" title="{{ Lang::get('administrable::messages.default.clone') }}"><i class="fas fa-clone"></i></a>

                            <a href="{{ back_route('user.edit', $user->getKey()) }}" class="btn btn-info"
                                data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::messages.default.edit') }}"><i
                                    class="fas fa-edit"></i></a>
                            <a href="{{ back_route('user.destroy', $user->getKey()) }}" data-method="delete"
                                data-confirm="{{ Lang::get('administrable::messages.view.user.destroy') }}"
                                class="btn btn-danger" data-toggle="tooltip" data-placement="top"
                                title="{{ Lang::get('administrable::messages.default.delete') }}"><i class="fas fa-trash"></i></a>
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

<x-administrable::datatable />
@include(back_view_path('partials._deleteAll'))

@endsection
