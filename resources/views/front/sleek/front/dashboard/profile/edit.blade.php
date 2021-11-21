@extends(front_view_path('layouts.dashboard'))

<x-administrable::seotags :force="true" title="Edition de profil" />

@section('content')

<div class="content-wrapper">
    <div class="content">
        <div class="bg-white border rounded">
            <div class="row no-gutters">
                <div class="col-lg-4 col-xl-3">
                    <div class="profile-content-left pt-5 pb-3 px-3 px-xl-5">
                        <div class="card text-center widget-profile px-0 border-0">
                            <div class="card-img mx-auto rounded-circle">
                                <img src="{{ $user->avatar }}" alt="{{ $user->name }}">
                            </div>
                            <div class="card-body">
                                <h4 class="py-2 text-dark">{{ $user->name }}</h4>
                                <p>{{ $user->email }}</p>
                            </div>
                        </div>

                        <hr class="w-100">
                        <div class="contact-info pt-4">
                            <p class="text-dark font-weight-medium pt-4 mb-2">Email</p>
                            <p>{{ $user->email }}</p>
                            @if($user->phone_number)
                            <p class="text-dark font-weight-medium pt-4 mb-2">Numéro de téléphone</p>
                            @endif
                            <p>{{ $user->phone_number }}</p>
                            <p class="text-dark font-weight-medium pt-4 mb-2">Inscription</p>
                            <p>{{ $user->created_at->format('d/m/Y H:i') }}</p>


                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-xl-9">
                    <div class="profile-content-right py-5">
                        <ul class="nav nav-tabs px-3 px-xl-5 nav-style-border" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab"
                                    aria-controls="profile" aria-selected="true">Modification de profil</a>
                            </li>


                        </ul>
                        <div class="tab-content px-3 px-xl-5" id="myTabContent">
                            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <div class="mt-5">
                                    @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                       {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    @endif
                                    <form action="{{ route('front.dashboard.profile.edit') }}" method="POST">
                                            @csrf
                                            @method('PUT')


                                        <div class="form-group mb-4">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" value="{{ $user->email }}" disabled>
                                            <span class="mt-2 d-block">Votre email ne peut pas être modifié.</span>
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="name">Nom</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                                value="{{ old('name', $user->name) }}" name="name">
                                            @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="pseudo">Pseudo</label>
                                            <input type="text" class="form-control @error('pseudo') is-invalid @enderror" id="pseudo"
                                                value="{{ old('pseudo', $user->pseudo) }}" name="pseudo">
                                            @error('pseudo')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="phone_number">Numéro de téléphone</label>
                                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number"
                                                value="{{ old('phone_number', $user->phone_number) }}" name="phone_number">
                                            @error('phone_number')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        @if($user->custom_form_fields)

                                        @foreach ($user->custom_form_fields as $field)
                                        @if(Arr::get($field, 'type') === 'boolean')
                                        <div class="form-group mb-4">
                                            <label for="{{  Arr::get($field, 'name') }}">{{ Arr::get($field, 'label')
                                                }}</label>
                                            <select name="custom_fields[{{  Arr::get($field, 'name') }}]" id="{{  Arr::get($field, 'name') }}"
                                                class="form-control">
                                                <option value="0" @if($user->getCustomField(Arr::get($field, 'name')) ==
                                                    0) selected @endif>Non</option>
                                                <option value="1" @if($user->getCustomField(Arr::get($field, 'name')) ==
                                                    1) selected @endif>Oui</option>
                                            </select>
                                        </div>
                                        @else
                                        <div class="form-group mb-4">
                                            <label for="{{  Arr::get($field, 'name') }}">{{ Arr::get($field, 'label')
                                                }}</label>
                                            <input type="{{ Arr::get($field, 'type') }}" name="custom_fields[{{  Arr::get($field, 'name') }}]"
                                                class="form-control" value="{{ $user->getCustomField(Arr::get($field, 'name')) }}">
                                        </div>
                                        @endif
                                        @endforeach

                                        @endif

                                        <div class="form-group mb-4">
                                            <label for="avatar">Photo de profil</label>
                                            <div class="custom-file mb-1">
                                                <input type="file" class="custom-file-input" id="coverImage">
                                                <label class="custom-file-label" for="coverImage">Choose
                                                    file...</label>
                                                <div class="invalid-feedback">Example invalid custom file feedback
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end mt-5">
                                            <button type="submit" class="btn btn-primary mb-2 btn-pill">Enregistrer</button>
                                        </div>

                                    </form>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="right-sidebar-2">
        <div class="right-sidebar-container-2">
            <div class="slim-scroll-right-sidebar-2">

                <div class="right-sidebar-2-header">
                    <h2>Layout Settings</h2>
                    <p>User Interface Settings</p>
                    <div class="btn-close-right-sidebar-2">
                        <i class="mdi mdi-window-close"></i>
                    </div>
                </div>

                <div class="right-sidebar-2-body">
                    <span class="right-sidebar-2-subtitle">Header Layout</span>
                    <div class="no-col-space">
                        <a href="javascript:void(0);"
                            class="btn-right-sidebar-2 header-fixed-to btn-right-sidebar-2-active">Fixed</a>
                        <a href="javascript:void(0);" class="btn-right-sidebar-2 header-static-to">Static</a>
                    </div>

                    <span class="right-sidebar-2-subtitle">Sidebar Layout</span>
                    <div class="no-col-space">
                        <select class="right-sidebar-2-select" id="sidebar-option-select">
                            <option value="sidebar-fixed">Fixed Default</option>
                            <option value="sidebar-fixed-minified">Fixed Minified</option>
                            <option value="sidebar-fixed-offcanvas">Fixed Offcanvas</option>
                            <option value="sidebar-static">Static Default</option>
                            <option value="sidebar-static-minified">Static Minified</option>
                            <option value="sidebar-static-offcanvas">Static Offcanvas</option>
                        </select>
                    </div>

                    <span class="right-sidebar-2-subtitle">Header Background</span>
                    <div class="no-col-space">
                        <a href="javascript:void(0);"
                            class="btn-right-sidebar-2 btn-right-sidebar-2-active header-light-to">Light</a>
                        <a href="javascript:void(0);" class="btn-right-sidebar-2 header-dark-to">Dark</a>
                    </div>

                    <span class="right-sidebar-2-subtitle">Navigation Background</span>
                    <div class="no-col-space">
                        <a href="javascript:void(0);"
                            class="btn-right-sidebar-2 btn-right-sidebar-2-active sidebar-dark-to">Dark</a>
                        <a href="javascript:void(0);" class="btn-right-sidebar-2 sidebar-light-to">Light</a>
                    </div>

                    <span class="right-sidebar-2-subtitle">Direction</span>
                    <div class="no-col-space">
                        <a href="javascript:void(0);"
                            class="btn-right-sidebar-2 btn-right-sidebar-2-active ltr-to">LTR</a>
                        <a href="javascript:void(0);" class="btn-right-sidebar-2 rtl-to">RTL</a>
                    </div>

                    <div class="d-flex justify-content-center" style="padding-top: 30px">
                        <div id="reset-options" style="width: auto; cursor: pointer"
                            class="btn-right-sidebar-2 btn-reset">
                            Reset
                            Settings</div>
                    </div>

                </div>

            </div>
        </div>

    </div>

</div>

@endsection
