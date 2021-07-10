@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::extensions.livenews.label'))


@section('content')


<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get('administrable::messages.default.dashboard') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ Lang::get('administrable::extensions.livenews.label') }}</a></li>
            </ol>

            <a href="{{ back_route('extensions.livenews.livenews.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i>&nbsp; {{ Lang::get('administrable::messages.default.add') }}</a>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between mb-3">
            <h3> {{ Lang::get('administrable::extensions.livenews.label') }} </h3>
            <a href="#" class="btn btn-danger d-none" data-model="\{{ AdminExtension::model('livenews') }}"
                id="delete-all"><i class="fa fa-trash"></i> &nbsp; {{ Lang::get('administrable::messages.default.deleteall') }}</a>
        </div>

        <table class="table table-vcenter card-table" id='list'>
            <thead>
                <th></th>
                <th>
                    <label class="form-check" for="check-all">
                        <input class="form-check-input" type="checkbox" id="check-all">
                        <span class="form-check-label"></span>
                    </label>
                </th>

                <th>{{ Lang::get('administrable::extensions.livenews.view.text') }}</th>
                <th>{{ Lang::get('administrable::extensions.livenews.view.size') }}</th>
                <th>{{ Lang::get('administrable::extensions.livenews.view.text_color') }}</th>
                <th>{{ Lang::get('administrable::extensions.livenews.view.back_color') }}</th>
                <th>{{ Lang::get('administrable::extensions.livenews.view.started_at') }}</th>
                <th>{{ Lang::get('administrable::extensions.livenews.view.ended_at') }}</th>
                {{-- add fields here --}}
                <th>{{ Lang::get('administrable::extensions.livenews.view.actions') }}</th>

            </thead>
            <tbody>
                @foreach($livenews as $news)
                <tr class="tr-shadow">
                    <td>
                        <label class="form-check" for="check-{{ $news->id }}">
                            <input class="form-check-input" type="checkbox" data-check data-id="{{ $news->id }}"
                                id="check-{{ $news->id }}" <span class="form-check-label"></span>
                        </label>
                    </td>
                    <td>{{ $news->content }}</td>
                    <td>{{ $news->size }}</td>
                    <td>
                        <p style="width: 100%; height: 30px; background-color: {{ $news->text_color }}"></p>
                    </td>
                    <td>
                        <p style="width: 100%; height: 30px; background-color: {{ $news->background_color }}"></p>
                    </td>
                    <td>
                        {{ format_date($news->started_at) }}
                    </td>
                    <td>
                        {{ format_date($news->ended_at) }}
                    </td>
                    {{-- add values here --}}
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ back_route('model.clone', get_clone_model_params($news)) }}"
                            class="btn btn-secondary" data-toggle="tooltip"
                            data-placement="top" title="{{ Lang::get('administrable::messages.default.clone') }}"><i
                                class="fas fa-clone"></i></a>

                            <a href="{{ back_route('extensions.livenews.livenews.edit', $news) }}"
                                class="btn btn-info" data-toggle="tooltip"
                                data-placement="top" title="{{ Lang::get('administrable::messages.default.edit') }}"><i
                                    class="fas fa-edit"></i></a>

                            <a href="{{ back_route('extensions.livenews.livenews.destroy', $news) }}"
                                data-method="delete"
                                data-confirm="{{ Lang::get('administrable::extensions.livenews.view.destroy') }}"
                                class="btn btn-danger" data-toggle="tooltip"
                                data-placement="top" title="{{ Lang::get('administrable::messages.default.delete') }}"><i
                                    class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</div>

<x-administrable::datatable />

@include(back_view_path('partials._deleteAll'))

@endsection
