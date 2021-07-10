@extends(back_view_path('layouts.base'))


@section('title', Lang::get('administrable::extensions.testimonial.label'));


@section('content')
<div class="main-content">
    <div class="card ">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end" style="padding-top:10px;padding-right:20px;">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route( config('administrable.guard') . '.dashboard') }}">{{ Lang::get('administrable::messages.default.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ Lang::get('administrable::extensions.testimonial.label') }}</li>
            </ol>
        </nav>

    </div>

    <div class="card">
        {{-- <h4 class="card-title">
                {{ Lang::get('administrable::extensions.testimonial.label') }}
            </h4> --}}

        <div class="card-body">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title h3">
                        {{ Lang::get('administrable::extensions.testimonial.label') }}
                    </h5>
                    <div class="btn-group">
                        <a href="{{ back_route('extensions.testimonial.testimonial.create') }}" class="btn btn-sm btn-label btn-round btn-primary"><label><i
                                    class="ti-plus"></i></label> {{ Lang::get('administrable::messages.default.add') }}</a>
                        <a href="#" class="btn btn-sm btn-label btn-round btn-danger d-none" data-model="\{{ AdminExtension::model('testimonial') }}"
                            id="delete-all"><label><i class="fa fa-trash"></i></label> {{ Lang::get('administrable::messages.default.deleteall') }}</a>

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
                            <th></th>
                            <th>{{ Lang::get('administrable::extensions.testimonial.view.name') }}</th>
                            <th>{{ Lang::get('administrable::extensions.testimonial.view.email') }}</th>
                            <th>{{ Lang::get('administrable::extensions.testimonial.view.job') }}</th>
                            <th>{{ Lang::get('administrable::extensions.testimonial.view.status') }}</th>
                            <th>{{ Lang::get('administrable::extensions.testimonial.view.content') }}</th>
                            <th>{{ Lang::get('administrable::extensions.testimonial.view.createdat') }}</th>
                            {{-- add fields here --}}

                            <th>{{ Lang::get('administrable::extensions.testimonial.view.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($testimonials as $testimonial)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input type="checkbox" data-check class="form-check-input" data-id="{{ $testimonial->id }}"
                                        id="check-{{ $testimonial->id }}">
                                    <label class="form-check-label" for="check-{{ $testimonial->id }}"></label>
                                </div>
                            </td>
                            <td>{{ $loop->iteration }}</td>
                            <td><a class="text-dark" data-provide="tooltip" title="Apercu rapide"  href="#qv-{{extensionPluralSlug}}-details-{{ $testimonial->id }}" data-toggle="quickview">{{ $testimonial->name }}</a></td>
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


                            <td>{{ ${{extensionSingularSlug}}->created_at->format('d/m/Y h:i') }}</td>
                            {{-- add values here --}}
                            <td>
                                <nav class="nav no-gutters gap-2 fs-16">
                                    <a class="nav-link hover-primary" href="{{ back_route('extensions.testimonial.testimonial.show', $testimonial) }}" data-provide="tooltip"
                                        title="{{ Lang::get('administrable::messages.default.show') }}"><i class="ti-eye"></i></a>
                                    <a class="nav-link hover-primary" href="{{ back_route('model.clone', get_clone_model_params($testimonial)) }}" data-provide="tooltip"
                                        title="{{ Lang::get('administrable::messages.default.clone') }}"><i class="ti-layers"></i></a>
                                    <a class="nav-link hover-primary" href="{{ back_route('extensions.testimonial.testimonial.edit', $testimonial) }}" data-provide="tooltip"
                                        title="{{ Lang::get('administrable::messages.default.edit') }}"><i class="ti-pencil"></i></a>
                                    <a class="nav-link hover-danger" href="{{ back_route('extensions.testimonial.testimonial.destroy', $testimonial) }}" data-provide="tooltip" title="Supprimer"
                                        data-method="delete"
                                        data-confirm="{{ Lang::get('administrable::extensions.testimonial.view.destroy') }}"
                                        data-original-title="{{ Lang::get('administrable::messages.default.delete') }}"><i class="ti-close"></i></a>
                                </nav>
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
