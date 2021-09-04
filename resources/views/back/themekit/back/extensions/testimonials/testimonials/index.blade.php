@extends(back_view_path('layouts.base'))


@section('title', Lang::get('administrable::extensions.testimonial.label'))


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
                        <li class="breadcrumb-item active" aria-current="page">{{ Lang::get('administrable::extensions.testimonial.label') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">{{ Lang::get('administrable::extensions.testimonial.label') }}</h3>
                    <div class="btn-group float-right">
                        <a href="{{ back_route('extensions.testimonial.testimonial.create') }}" class="btn  btn-primary"> <i
                                class="fa fa-plus"></i> {{ Lang::get('administrable::messages.default.add') }}</a>
                        <a href="#" class="btn btn-danger d-none" data-model="\{{ AdminExtension::model('testimonial') }}" id="delete-all">
                            <i class="fa fa-trash"></i> {{ Lang::get('administrable::messages.default.deleteall') }}</a>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-vcenter card-table" id='list'>
                        <thead>
                            <th></th>
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

                            <th>#</th>
                            <th>{{ Lang::get('administrable::extensions.testimonial.view.name') }}</th>
                            <th>{{ Lang::get('administrable::extensions.testimonial.view.email') }}</th>
                            <th>{{ Lang::get('administrable::extensions.testimonial.view.job') }}</th>
                            <th>{{ Lang::get('administrable::extensions.testimonial.view.status') }}</th>
                            <th>{{ Lang::get('administrable::extensions.testimonial.view.content') }}</th>
                            <th>{{ Lang::get('administrable::extensions.testimonial.view.createdat') }}</th>
                            {{-- add fields here --}}

                            <th>{{ Lang::get('administrable::extensions.testimonial.view.actions') }}</th>
                        </thead>
                        <tbody>
                            @foreach($testimonials as $testimonial)
                            <tr class="tr-shadow">

                                <td></td>
                                <td>
                                    <div class="checkbox-fade fade-in-success ">
                                        <label for="check-{{ $testimonial->id }}">
                                            <input type="checkbox" data-check data-id="{{ $testimonial->id }}"  id="check-{{ $testimonial->id }}">
                                            <span class="cr">
                                                <i class="cr-icon ik ik-check txt-success"></i>
                                            </span>
                                        </label>
                                    </div>
                                </td>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $testimonial->name }}</td>
                                <td>{{ $testimonial->email }}</td>
                                <td>{{ $testimonial->job }}</td>

                                <td>
                                    @if ($testimonial->isOnline())
                                    <a data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::extensions.testimonial.view.online') }}"><i
                                            class="fas fa-circle text-success"></i></a>
                                    @else
                                    <a data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::extensions.testimonial.view.offline') }}"><i
                                            class="fas fa-circle text-secondary"></i></a>
                                    @endif
                                </td>

                                <td>{!! Str::limit(strip_tags($testimonial->content),50) !!}</td>

                                <td>{{ $testimonial->created_at->format('d/m/Y h:i') }}</td>
                                {{-- add values here --}}

                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ back_route('extensions.testimonial.testimonial.show', $testimonial) }}" class="btn btn-primary"
                                            data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::messages.default.show') }}"><i class="fas fa-eye"></i></a>

                                         <a href="{{ back_route('model.clone', get_clone_model_params($testimonial)) }}" class="btn btn-secondary"
                                            title="{{ Lang::get('administrable::messages.default.clone') }}"><i class="fas fa-clone"></i></a>

                                        <a href="{{ back_route('extensions.testimonial.testimonial.edit', $testimonial) }}" class="btn btn-info"
                                            data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::messages.default.edit') }}"><i class="fas fa-edit"></i></a>
                                        <a href="{{ back_route('extensions.testimonial.testimonial.destroy', $testimonial) }}" data-method="delete"
                                            data-confirm="{{ Lang::get('administrable::extensions.testimonial.view.destroy') }}" class="btn btn-danger"
                                            data-toggle="tooltip" data-placement="top" title="{{ Lang::get('administrable::messages.default.delete') }}"><i class="fas fa-trash"></i></a>
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
