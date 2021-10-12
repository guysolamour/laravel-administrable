@extends(back_view_path('layouts.base'))

@section('title', $guard->name)

@section('content')
<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get("administrable::messages.default.dashboard") }}</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route(config('administrable.guard') . '.index') }}">{{ Lang::get('administrable::messages.view.guard.plural') }}</a></li>
                <li class="breadcrumb-item active">{{ Lang::get('administrable::messages.view.guard.profil') }}</li>
            </ol>
            <div class="btn-group">
                @if (get_guard()->can('delete-' . config('administrable.guard'), $guard))
                <a href="{{ back_route(config('administrable.guard') . '.delete', $guard) }}" class="btn btn-danger" data-method="delete"
                data-confirm="{{ Lang::get('administrable::messages.view.guard.destroy') }}">
                <i class="fas fa-trash"></i>&nbsp; {{ Lang::get('administrable::messages.default.delete') }}</a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h3 class="title-5 m-b-35">
            {{ $guard->name }}
        </h3>
        <div class="row">
            <div class="col-md-8">
                <div class="default-tab">
                    @if (get_guard()->can('update-' . config('administrable.guard'), $guard))
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="profile-edition-tab" data-toggle="tab" href="#profile-edition"
                            role="tab" aria-controls="profile-edition" aria-selected="false">{{ Lang::get('administrable::messages.default.edition') }}</a>
                            @if (get_guard()->can('update-' . config('administrable.guard') . '-password', $guard))
                            <a class="nav-item nav-link" id="password-edition-tab" data-toggle="tab" href="#password-edition"
                            role="tab" aria-controls="password-edition" aria-selected="false">{{ Lang::get('administrable::messages.view.guard.password') }}</a>
                            @endif
                        </div>
                    </nav>
                    @endif
                    @if (get_guard()->can('update-' . config('administrable.guard'), $guard))
                    <div class="tab-content pl-3 pt-2" id="nav-tabContent">
                        <div class="tab-pane fade active show" id="profile-edition" role="tabpanel"
                        aria-labelledby="profile-edition-tab">
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
                            @foreach ($edit_form->getModel()->custom_form_fields ?? [] as $field)
                                @if(Arr::get($field, 'type') === 'boolean')
                                <div class="form-group">
                                    <label for="{{  Arr::get($field, 'name') }}">{{ Arr::get($field, 'label') }}</label>
                                    <select name="custom_fields[{{  Arr::get($field, 'name') }}]" id="{{  Arr::get($field, 'name') }}" class="custom-select">
                                        <option value="0" @if($edit_form->getModel()->getCustomField(Arr::get($field, 'name')) == 0) selected @endif>Non</option>
                                        <option value="1" @if($edit_form->getModel()->getCustomField(Arr::get($field, 'name')) == 1) selected @endif>Oui</option>
                                    </select>
                                </div>
                                @else
                                <div class="form-group">
                                    <label for="{{  Arr::get($field, 'name') }}">{{ Arr::get($field, 'label') }}</label>
                                    <input type="{{ Arr::get($field, 'type') }}" name="custom_fields[{{  Arr::get($field, 'name') }}]" class="form-control" value="{{ $edit_form->getModel()->getCustomField(Arr::get($field, 'name')) }}">
                                </div>
                                @endif
                            @endforeach
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
                                <button type='submit' class='btn btn-info btn-block'> <i class='fas fa-edit'></i>&nbsp;
                                    {{ Lang::get('administrable::messages.default.modify') }}</button>
                                </div>
                            </div>
                            {!! form_end($edit_form) !!}
                        </div>
                        @if (get_guard()->can('update-' . config('administrable.guard') . '-password', $guard))
                        <div class="tab-pane fade" id="password-edition" role="tabpanel" aria-labelledby="password-edition-tab">
                            {!! form_start($reset_form) !!}
                            <div class='row'>
                                {{-- <div class='col-md-12'>
                                    {!! form_row($reset_form->old_password) !!}
                                </div> --}}
                                <div class='col-md-12'>
                                    {!! form_row($reset_form->new_password) !!}
                                </div>
                                <div class='col-md-12'>
                                    {!! form_row($reset_form->new_password_confirmation) !!}
                                </div>
                                <div class='col-md-12'>
                                    <button type='submit' class='btn btn-info btn-block'> <i class='fas fa-edit'></i> &nbsp;
                                        {{ Lang::get('administrable::messages.default.modify') }}</button>
                                    </div>
                                </div>
                                {!! form_end($reset_form) !!}
                            </div>
                            @endif
                        </div>
                        @endif

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="flexbox align-items-baseline mb-20">
                                <h6 class="text-uppercase ls-2">{{ Lang::get('administrable::messages.view.guard.about') }}</h6>
                            </div>
                            <div class="gap-items-2 gap-y">
                                {{ Str::limit($guard->about, 200) }}
                            </div>
                        </div>
                    </div>

                    @include(back_view_path('guards._avatar'), [
                    'model'       => $guard,
                    ])
                </div>
            </div>
        </div>
    </div>


    @endsection
