@extends(back_view_path('layouts.base'))


@section('title', Lang::get('administrable::extensions.testimonial.label'));


@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1>{{ Lang::get('administrable::extensions.testimonial.label') }}</h1> --}}
                </div>
                <div class="col-sm-6">
                    <div class='float-sm-right'>
                         <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get('administrable::messages.default.dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ Lang::get('administrable::extensions.testimonial.label') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip"
                        title="{{ Lang::get('administrable::extensions.testimonial.minus') }}">
                        <i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class='row'>
                    <div class="col-md-12">
                        <div class="card" style='box-shadow: 0 0 1px rgba(0,0,0,0), 0 1px 3px rgba(0,0,0,0);'>

                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ Lang::get('administrable::extensions.testimonial.label') }}</h3>
                                        <div class="btn-group float-right">
                                            <a href="{{ back_route('extensions.testimonial.testimonial.create') }}" class="btn  btn-primary"> <i
                                                    class="fa fa-plus"></i> {{ Lang::get('administrable::messages.default.add') }}</a>
                                            <a href="#" class="btn btn-danger d-none" data-model="\{{ AdminExtension::model('testimonial') }}"
                                                id="delete-all"> <i class="fa fa-trash"></i> {{ Lang::get('administrable::messages.default.deleteall') }}</a>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped" id="list">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="check-all">
                                                                <label class="custom-control-label"
                                                                    for="check-all"></label>
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
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" data-check
                                                                    class="custom-control-input"
                                                                    data-id="{{ $testimonial->id }}"
                                                                    id="check-{{ $testimonial->id }}">
                                                                <label class="custom-control-label"
                                                                    for="check-{{ $testimonial->id }}"></label>
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
                                                                <a href="{{ back_route('extensions.testimonial.testimonial.show', $testimonial) }}"
                                                                    class="btn btn-primary" data-toggle="tooltip"
                                                                    data-placement="top" title="{{ Lang::get('administrable::messages.default.show') }}"><i
                                                                        class="fas fa-eye"></i></a>

                                                                     <a href="{{ back_route('model.clone', get_clone_model_params($testimonial)) }}"
                                                                class="btn btn-secondary" data-toggle="tooltip"
                                                                data-placement="top" title="{{ Lang::get('administrable::messages.default.clone') }}"><i
                                                                    class="fas fa-clone"></i></a>

                                                                <a href="{{ back_route('extensions.testimonial.testimonial.edit', $testimonial) }}"
                                                                    class="btn btn-info" data-toggle="tooltip"
                                                                    data-placement="top" title="{{ Lang::get('administrable::messages.default.edit') }}"><i
                                                                        class="fas fa-edit"></i></a>
                                                                <a href="{{ back_route('extensions.testimonial.testimonial.destroy', $testimonial) }}"
                                                                    data-method="delete"
                                                                    data-confirm="{{ Lang::get('administrable::extensions.testimonial.view.destroy') }}"
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
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.mail-box-messages -->
                            </div>

                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>

        </div>
        <!-- /.card-body -->

        <!-- /.card -->

    </section>
    <!-- /.content -->
</div>


<x-administrable::datatable />
@include(back_view_path('partials._deleteAll'))
@endsection




