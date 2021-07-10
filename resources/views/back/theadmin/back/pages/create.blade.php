@extends( back_view_path('layouts.base') )

@section('title',  Lang::get("administrable::messages.default.add"))

@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
             <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get("administrable::messages.default.dashboard") }}</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('page.index') }}">{{ Lang::get("administrable::messages.view.page.plural") }}</a></li>
                <li class="breadcrumb-item active">{{ Lang::get("administrable::messages.default.add") }}</li>
            </ol>
        </nav>
    </div>
    {!! form_start($form) !!}
    <div class="card">
        <h4 class="card-title">
            {{ Lang::get("administrable::messages.default.add") }}
        </h4>

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
        <button type="submit" class="btn btn-success"> <i class="fa fa-save"></i> {{ Lang::get("administrable::messages.default.save") }}</button>
    </div>
    {!! form_end($form) !!}
</div>

@endsection
