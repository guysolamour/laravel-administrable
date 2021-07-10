@extends(back_view_path('layouts.base'))


@section('title', Lang::get('administrable::extensions.livenews.label'));


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
                        <li class="breadcrumb-item active" aria-current="page">{{ Lang::get('administrable::extensions.livenews.label') }}</li>
                    </ol>
                </nav>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">{{ Lang::get('administrable::extensions.livenews.label') }}</h3>
                    <div class="btn-group float-right">
                        <a href="{{ back_route('extensions.livenews.livenews.create') }}" class="btn  btn-primary"> <i
                                class="fa fa-plus"></i> {{ Lang::get('administrable::messages.default.add') }}</a>

                        <a href="#" class="btn btn-danger d-none" data-model="\{{ AdminExtension::model('livenews') }}" id="delete-all">
                            <i class="fa fa-trash"></i> {{ Lang::get('administrable::messages.default.deleteall') }}</a>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-vcenter card-table" id='list'>
                        <thead>
                            <th>
                                <div class="checkbox-fade fade-in-success ">
                                    <label for="check-all">
                                        <input type="checkbox" value=""  id="check-all">
                                        <span class="cr">
                                            <i class="cr-icon ik ik-check txt-success"></i>
                                        </span>
                                    </label>
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

                        </thead>
                        <tbody>
                            @foreach($livenews as $news)
                                <tr>
                                    <td>
                                        <div class="checkbox-fade fade-in-success ">
                                            <label for="check-{{ $news->getKey() }}">
                                                <input type="checkbox" data-check data-id="{{ $news->getKey() }}"
                                                    id="check-{{ $news->getKey() }}">
                                                <span class="cr">
                                                    <i class="cr-icon ik ik-check txt-success"></i>
                                                </span>
                                            </label>
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
    </div>
</div>

<x-administrable::datatable />

@include(back_view_path('partials._deleteAll'))
@endsection
