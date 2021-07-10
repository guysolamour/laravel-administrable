@extends(back_view_path('layouts.base'))

@section('title', $user->name)


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
                            <li class="breadcrumb-item">
                                <a href="{{ back_route('user.index') }}">{{ Lang::get('administrable::messages.view.user.plural') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $user->name }}</li>
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
                            <a href="{{ back_route('user.edit', $user) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> {{ Lang::get('administrable::messages.default.edit') }}</a>
                            <a href="{{ back_route('user.destroy', $user) }}" class="btn btn-danger" data-method="delete"
                                data-confirm={{ Lang::get('administrable::messages.view.user.destroy') }}">
                                <i class="fas fa-trash"></i> {{ Lang::get('administrable::messages.default.delete') }}</a>
                        </div>
                    </div>

                    <div class="card-body row">
                        <div class="col-md-8">
                            {{-- add fields here --}}
                               <div class="pb-2">
                                    <p><span class="font-weight-bold">{{ Lang::get('administrable::messages.view.user.name') }}:</span></p>
                                    <p>
                                        {{ $user->name }}
                                    </p>
                                </div>

                                <div class="pb-2">
                                    <p><span class="font-weight-bold">{{ Lang::get('administrable::messages.view.user.pseudo') }}:</span></p>
                                    <p>
                                        {{ $user->pseudo }}
                                    </p>
                                </div>

                                <div class="pb-2">
                                    <p><span class="font-weight-bold">{{ Lang::get('administrable::messages.view.user.email') }}:</span></p>
                                    <p>
                                        {{ $user->email }}
                                    </p>
                                </div>

                                <div class="pb-2">
                                    <p><span class="font-weight-bold">{{ Lang::get('administrable::messages.view.user.createdat') }}:</span></p>
                                    <p>
                                        {{ $user->created_at->format('d/m/Y h:i') }}
                                    </p>
                                </div>
                        </div>
                        <div class="col-md-4">
                            @include(back_view_path('media._show'), [
                                'model' => $user,
                            ])
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
