@extends(back_view_path('layouts.base'))

@section('title', $guard->name)

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
                            <li class="breadcrumb-item"><a href="{{ back_route(config('administrable.guard') . '.index') }}">{{ Lang::get('administrable::messages.view.guard.plural') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ $guard->full_name }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">{{ $guard->full_name }}</h3>
                        <div class="btn-group float-right">
                            @if (get_guard()->can('delete-' . config('administrable.guard'), $guard))
                            <a href="{{ back_route( config('administrable.guard') . '.delete', $guard) }}" class="btn btn-danger" data-method="delete"
                                data-confirm="{{ Lang::get('administrable::messages.view.guard.destroy') }}">
                                <i class="fas fa-trash"></i>&nbsp; {{ Lang::get('administrable::messages.default.delete') }}</a>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 col-md-5">
                                <div class="card">
                                    @include(back_view_path('guards._avatar'), [
                                        'model'       => $guard,
                                        'model_name'  => get_class($guard),
                                        'front_image' => true,
                                        'front_image_label' => Lang::get('administrable::messages.view.guard.avatar'),
                                        'back_image'  => false,
                                        'images'      => false,
                                        'form_name'   => '',
                                    ])

                                    <hr class="mb-0">
                                    <div class="card-body">
                                        <small class="text-muted d-block">{{ Lang::get('administrable::messages.view.guard.email') }} </small>
                                        <h6>{{ ${{singularSlug}}->email }}</h6>
                                        <small class="text-muted d-block pt-10">{{ Lang::get('administrable::messages.view.guard.telephone') }}</small>
                                        <h6>{{ ${{singularSlug}}->phone_number }}</h6>

                                        <br>
                                        @if($guard->facebook)
                                        <a href="{{ $guard->facebook }}" target="_blank" class="btn btn-icon btn-facebook"><i class="fab fa-facebook-f"></i></a>
                                        @endif
                                        @if($guard->twitter)
                                        <a href="{{ $guard->twitter }}" target="_blank" class="btn btn-icon btn-twitter"><i class="fab fa-twitter"></i></a>
                                        @endif
                                        @if($guard->linkedin)
                                        <a href="{{ $guard->linkedin }}" target="_blank" class="btn btn-icon btn-linkedin"><i class="fab fa-linkedin"></i></a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-7">
                                <div class="card">
                                    @if (get_guard()->can('update-' . config('administrable.guard'), $guard))
                                    <ul class="nav nav-pills custom-pills" id="pills-tab" role="tablist">
                                        <li class="nav-item active">
                                            <a class="nav-link" id="profile-edition-tab" data-toggle="pill" href="#profile-edition" role="tab"
                                                aria-controls="profile-edition" aria-selected="true">{{ Lang::get('administrable::messages.default.edition') }}</a>
                                        </li>
                                        @if (get_guard()->can('update-' . config('administrable.guard') . '-password', $guard))
                                        <li class="nav-item">
                                            <a class="nav-link" id="password-edition-tab" data-toggle="pill" href="#password-edition" role="tab"
                                                aria-controls="password-edition" aria-selected="false">{{ Lang::get('administrable::messages.view.guard.password') }}</a>
                                        </li>
                                        @endif
                                    </ul>
                                    @endif
                                    @if (get_guard()->can('update-' . config('administrable.guard'), $guard))
                                    <div class="tab-content" id="pills-tabContent">
                                        <div class="tab-pane fade show active" id="profile-edition" role="tabpanel" aria-labelledby="profile-edition-tab">
                                            <div class="card-body">
                                                {!! form_start($edit_form) !!}
                                                <div class='row'>
                                                    <div class='col-md-12'>
                                                        {!! form_row($edit_form->pseudo) !!}
                                                    </div>
                                                    <div class='col-md-6'>
                                                        {!! form_row($edit_form->first_name) !!}
                                                    </div>
                                                    <div class='col-md-6'>
                                                        {!! form_row($edit_form->last_name) !!}
                                                    </div>
                                                </div>
                                                <div class='row'>
                                                    <div class='col-md-6'>
                                                        {!! form_row($edit_form->email) !!}
                                                    </div>
                                                    @role(config('administrable.guard') . '|super-' . config('administrable.guard'), config('administrable.guard'))
                                                    <div class='col-md-6'>
                                                        <label for="role">{{ Lang::get('administrable::messages.view.guard.role') }}</label>
                                                        <select name="role" id="role" class="form-control select2" required>
                                                            @php
                                                                $roles = config('permission.models.role')::where('guard_name', config('administrable.guard'))->get();
                                                            @endphp
                                                            @foreach($roles  as $role)
                                                                <option value="{{ $role->name }}" {{ $guard->hasRole($role, config('administrable.guard')) ? 'selected="selected"' : '' }}  >{{ $role->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    @endrole
                                                </div>
                                                <div class="row">
                                                    <div class='col-md-6'>
                                                        {!! form_row($edit_form->website) !!}
                                                    </div>
                                                    <div class='col-md-6'>
                                                        {!! form_row($edit_form->phone_number) !!}
                                                    </div>
                                                </div>
                                                <div class='row'>

                                                    <div class='col-md-4'>
                                                        {!! form_row($edit_form->facebook) !!}
                                                    </div>
                                                    <div class='col-md-4'>
                                                        {!! form_row($edit_form->twitter) !!}
                                                    </div>
                                                     <div class='col-md-4'>
                                                        {!! form_row($edit_form->linkedin) !!}
                                                    </div>

                                                </div>
                                                <div class='row'>
                                                    <div class='col-md-12'>
                                                        {!! form_row($edit_form->about) !!}
                                                    </div>
                                                </div>
                                                <div class='row'>
                                                    <div class='col-md-12'>
                                                        <button type='submit' class='btn btn-info btn-block'> <i class='fas fa-edit'></i>
                                                            {{ Lang::get('administrable::messages.default.modify') }}</button>
                                                    </div>
                                                </div>
                                                {!! form_end($edit_form) !!}
                                            </div>
                                        </div>
                                        @if (get_guard()->can('update-' . config('administrable.guard') . '-password', $guard))
                                        <div class="tab-pane fade" id="password-edition" role="tabpanel" aria-labelledby="password-edition-tab">
                                            <div class="card-body">
                                                {!! form_start($reset_form) !!}
                                                <div class='row'>
                                                    <div class='col-md-12'>
                                                        {!! form_row($reset_form->new_password) !!}
                                                    </div>
                                                    <div class='col-md-12'>
                                                        {!! form_row($reset_form->new_password_confirmation) !!}
                                                    </div>
                                                    <div class='col-md-12'>
                                                        <button type='submit' class='btn btn-info btn-block'> <i class='fas fa-edit'></i>
                                                             {{ Lang::get('administrable::messages.default.modify') }}</button>
                                                    </div>
                                                </div>
                                                {!! form_end($reset_form) !!}
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

<x-administrable::select2 />
