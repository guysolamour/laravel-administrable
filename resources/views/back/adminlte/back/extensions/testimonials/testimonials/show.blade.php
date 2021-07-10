@extends(back_view_path('layouts.base'))

@section('title', $testimonial->name)


@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1></h1> --}}
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                         <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get('administrable::messages.default.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ back_route('extensions.testimonial.testimonial.index') }}">{{ Lang::get('administrable::extensions.testimonial.label') }}</a></li>
                            <li class="breadcrumb-item active">{{ $testimonial->name }}</li>
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
                        title="{{ Lang::get('administrable::messages.default.minus') }}">
                        <i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class='row'>
                    <div class='col-md-8'>
                        <section style="margin-bottom: 2rem;">

                            <div class="btn-group-horizontal">
                                <a href="{{ back_route('extensions.testimonial.testimonial.edit', $testimonial) }}" class="btn btn-info" data-toggle="tooltip"
                                    data-placement="top" title="{{ Lang::get('administrable::messages.default.edit') }}"><i class="fas fa-edit"></i>{{ Lang::get('administrable::messages.default.edit') }}</a>
                                <a href="{{ back_route('extensions.testimonial.testimonial.destroy', $testimonial) }}" data-method="delete" data-toggle="tooltip"
                                    data-placement="top" title="{{ Lang::get('administrable::messages.default.delete') }}"
                                    data-confirm="{{ Lang::get('administrable::extensions.testimonial.view.destroy') }}" class="btn btn-danger"><i
                                        class="fa fa-trash"></i> {{ Lang::get('administrable::messages.default.delete') }}</a>
                            </div>
                        </section>
                        {{-- add fields here --}}
                        <div>
                            <p><span class="font-weight-bold">{{ Lang::get('administrable::extensions.testimonial.view.name') }}:</span></p>
                            <p>
                                {{ $testimonial->name }}
                            </p>
                        </div>

                        <div>
                            <p><span class="font-weight-bold">{{ Lang::get('administrable::extensions.testimonial.view.email') }}:</span></p>
                            <p>
                                {{ $testimonial->email }}
                            </p>
                        </div>

                        <div>
                            <p><span class="font-weight-bold">{{ Lang::get('administrable::extensions.testimonial.view.job') }}:</span></p>
                            <p>
                                {{ $testimonial->job }}
                            </p>
                        </div>
                         <div>
                            <p><span class="font-weight-bold">{{ Lang::get('administrable::extensions.testimonial.view.content') }}:</span></p>
                            <p>
                                {!! $testimonial->content !!}
                            </p>
                        </div>
                        <div>
                            <p><span class="font-weight-bold">{{ Lang::get('administrable::extensions.testimonial.view.createdat') }}:</span></p>
                            <p>
                                {{ $testimonial->created_at->format('d/m/Y h:i') }}
                            </p>
                        </div>


                    </div>
                    <div class='col-md-4'>
                        @include(back_view_path('media._show'), [
                            'model' => $testimonial,
                        ])
                    </div>

                </div>
            </div>

        </div>
        <!-- /.card-body -->

        <!-- /.card -->

    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

@endsection
