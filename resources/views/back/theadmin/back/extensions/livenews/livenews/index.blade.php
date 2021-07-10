@extends(back_view_path('layouts.base'))

@section('title', Lang::get('administrable::extensions.livenews.label'))


@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get('administrable::messages.default.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ Lang::get('administrable::extensions.livenews.label') }}</li>
            </ol>
        </nav>

    </div>

    <div class="card">
        {{-- <h4 class="card-title">
                {{ Lang::get('administrable::extensions.livenews.label') }}
            </h4> --}}

        <div class="card-body">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title h3">
                        {{ Lang::get('administrable::extensions.livenews.label') }}
                    </h5>
                    <div class="btn-group">
                        <a href="{{ back_route('extensions.livenews.livenews.create') }}"
                            class="btn btn-sm btn-label btn-round btn-primary"><label><i class="fa fa-plus"></i></label>
                            {{ Lang::get('administrable::messages.default.add') }}</a>
                        <a href="#" data-model="\{{ AdminExtension::model('livenews') }}" id="delete-all" class="btn btn-sm btn-label btn-round btn-danger d-none"><label><i
                                    class="fa fa-trash"></i></label> {{ Lang::get('administrable::messages.default.deleteall') }}</a>

                    </div>
                </div>

                <table class="table table-hover table-has-action" id='list'>
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="check-all">
                                    <label class="form-check-label" for="check-all"></label>
                                </div>
                            </th>
                            <th>{{ Lang::get('administrable::extensions.livenews.view.text') }}</th>
                            <th>{{ Lang::get('administrable::extensions.livenews.view.size') }}</th>
                            <th>{{ Lang::get('administrable::extensions.livenews.view.text_color') }}</th>
                            <th>{{ Lang::get('administrable::extensions.livenews.view.back_color') }}</th>
                            <th>{{ Lang::get('administrable::extensions.livenews.view.started_at') }}</th>
                            <th>{{ Lang::get('administrable::extensions.livenews.view.ended_at') }}</th>
                            {{-- add fields here --}}
                            <th>{{ Lang::get('administrable::extensions.livenews.view.actions') }}</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($livenews as $news)
                        <tr>
                            <td>
                                 <div class="form-check">
                                    <input type="checkbox" data-check class="form-check-input" data-id="{{ $news->id }}"
                                        id="check-{{ $news->id }}">
                                    <label class="form-check-label" for="check-{{ $news->id }}"></label>
                                </div>
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
    </div>


</div>

<x-administrable::datatable />

@include(back_view_path('partials._deleteAll'))
@endsection
