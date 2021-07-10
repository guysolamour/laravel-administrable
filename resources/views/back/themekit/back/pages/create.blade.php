@extends( back_view_path('layouts.base') )

@section('title',  Lang::get("administrable::messages.default.add"))

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
                            <li class="breadcrumb-item"><a href="{{ back_route('page.index') }}">{{ Lang::get("administrable::messages.view.page.plural") }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ Lang::get("administrable::messages.default.add") }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                {!! form_start($form) !!}
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">{{ Lang::get("administrable::messages.default.add") }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                              {!! form_rest($form) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <x-administrable::seoform  :model="$form->getModel()" />

                <x-administrable::select2 />

                <div class="form-group">
                    <button type="submit" class="btn btn-success"> <i class="fa fa-save"></i>
                        {{ Lang::get("administrable::messages.default.save") }}
                    </button>
                </div>
                {!! form_end($form) !!}
            </div>
        </div>
    </div>
</div>
@endsection
