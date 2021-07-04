@extends(back_view_path('layouts.base'))

@section('title', $guard->name)

@section('content')
    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ Lang::get('administrable::messages.view.guard.profil') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ __("administrable::messages.default.dashboard") }}</a></li>
              <li class="breadcrumb-item"><a href="{{ back_route(config('administrable.guard') . '.index') }}">{{ Lang::get('administrable::messages.view.guard.plural') }}</a></li>
              <li class="breadcrumb-item active">{{ Lang::get('administrable::messages.view.guard.profil') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">
            @include(back_view_path('guards._avatar'), [
                    'model'       => $guard,
                    'model_name'  => get_class($guard),
                    'front_image' => true,
                    'front_image_label' => Lang::get('administrable::messages.view.guard.avatar'),
                    'back_image'  => false,
                    'images'      => false,
                    'form_name'   => '',
            ])

            <div class="card">
                <div class="card-body">
                  <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                      <b>{{ Lang::get('administrable::messages.view.guard.facebook') }}</b> <a class="float-right" href="{{ $guard->facebook }}" target='_blank'>{{ $guard->facebook }}</a>
                    </li>
                    <li class="list-group-item">
                      <b>{{ Lang::get('administrable::messages.view.guard.twitter') }}</b> <a class="float-right" href="{{ $guard->twitter }}" target='_blank'>{{ $guard->twitter }}</a>
                    </li>
                    <li class="list-group-item">
                      <b>{{ Lang::get('administrable::messages.view.guard.telephone') }}</b> <a class="float-right" >{{ $guard->phone_number }}</a>
                    </li>
                  </ul>
                </div>
            </div>

            <!-- About Me Box -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">{{ Lang::get('administrable::messages.view.guard.about') }}</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <p class="text-muted">
                  {{ Str::limit($guard->about, 200) }}
                </p>
                <hr>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
              <div class="card">
                <div class="card-header p-2">
                @if (get_guard()->can('update-' . config('administrable.guard'), $guard))
                  <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#edition" data-toggle="tab">{{ Lang::get('administrable::messages.default.edition') }}</a></li>
                    @if (get_guard()->can('update-' . config('administrable.guard') . '-password', $guard))
                    <li class="nav-item"><a class="nav-link" href="#password" data-toggle="tab">{{ Lang::get('administrable::messages.view.guard.password') }}</a></li>
                    @endif
                </ul>
                @endif
                </div><!-- /.card-header -->
                <div class="card-body">
                @if (get_guard()->can('update-' . config('administrable.guard'), $guard))
                  <div class="tab-content">
                    <div class="active tab-pane" id="edition">
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
                                  <label for="role">Role</label>
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
                                  <button type='submit' class='btn btn-info btn-block'> <i class='fas fa-edit'></i> Modifier</button>
                              </div>

                          </div>
                        {!! form_end($edit_form) !!}
                    </div>
                    <!-- /.tab-pane -->
                    @if (get_guard()->can('update-' . config('administrable.guard') . '-password', $guard))
                    <div class="tab-pane" id="password">
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
                    @endif
                  </div>
                  <!-- /.tab-content -->
                </div><!-- /.card-body -->
                @endif
              </div>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@endsection

<x-administrable::select2 />
