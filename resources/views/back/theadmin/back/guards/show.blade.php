@extends(back_view_path('layouts.base'))

@section('title', $guard->name)

@section('content')
<!-- Main container -->
<main class="main-container">

    <header class="header bg-img">
        @include(back_view_path('guards._avatar'), [
        'model'       => $guard,
        ])

        <div class="header-action bg-white">
            @if (get_guard()->can('update-' . config('administrable.guard'), $guard))
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#profile-edition">{{ Lang::get('administrable::messages.default.edition') }}</a>
                </li>
                @if (get_guard()->can('update-' . config('administrable.guard') . '-password', $guard))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#password-edition">{{ Lang::get('administrable::messages.view.guard.password') }}</a>
                </li>
                @endif
            </ul>
            @endif
        </div>
    </header>


    <div class="main-content">
        <div class="row">
            <div class="col-md-8">
                <!-- Tab panes -->
                @if (get_guard()->can('update-' . config('administrable.guard'), $guard))
                <div class="tab-content">
                    <div class="tab-pane fade active show" id="profile-edition">
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
                                <button type='submit' class='btn btn-info btn-block'> <i class='fas fa-edit'></i> {{ Lang::get('administrable::messages.default.modify') }}</button>
                            </div>
                        </div>
                        {!! form_end($edit_form) !!}
                    </div>
                    @if (get_guard()->can('update-' . config('administrable.guard') . '-password', $guard))
                    <div class="tab-pane fade " id="password-edition">
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
                                <button type='submit' class='btn btn-info btn-block'> <i class='fas fa-edit'></i> Modifier</button>
                            </div>
                            {!! form_end($reset_form) !!}
                        </div>

                    </div>
                    @endif
                </div>
                @endif
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


                <div class="card">
                    <div class="text-dark card-body bg-img text-center py-50"
                    style="background-image: url(/vendor/theadmin/img/gallery/2.jpg);">
                    <a href="#">
                        <img data-avatar class="avatar avatar-xxl avatar-bordered" src="{{ $guard->getFrontImageUrl() }}" alt="{{ $guard->name }}">
                    </a>
                    <h5 class="mt-2 mb-0"><a class="hover-primary text-dark" href="#">{{ $guard->full_name }}</a></h5>
                    <span>{{ $guard->role }}</span>
                </div>
                <ul class="flexbox flex-justified text-center p-20">
                    <li class="br-1 border-light">
                        <a href="{{ $guard->facebook }}" class="text-facebook" target="_blank"><i class="fab fa-facebook fa-3x"></i></a>
                    </li>
                    <li class="br-1 border-light">
                        <a href="{{ $guard->twitter }}" class="text-twitter" target="_blank"><i class="fab fa-twitter fa-3x"></i></a>
                    </li>
                </ul>
            </div>

        </div>
    </div>

</div>
<!--/.main-content -->

</main>
@endsection
