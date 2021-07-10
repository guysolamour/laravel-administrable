@extends(back_view_path('layouts.base'))


@section('title', Lang::get('administrable::messages.default.edition'))

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
                                <a href="{{ back_route('extensions.testimonial.testimonial.index') }}">{{ Lang::get('administrable::extensions.testimonial.label') }}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ back_route('extensions.testimonial.testimonial.show', $testimonial) }}">{{ $testimonial->name }}
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ Lang::get('administrable::messages.default.edition') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">{{ Lang::get('administrable::messages.default.edition') }}</h3>
                        <div class="btn-group float-right">
                            <a href="{{ back_route('extensions.testimonial.testimonial.destroy', $testimonial) }}" class="btn btn-danger" data-method="delete"
                                data-confirm="{{ Lang::get('administrable::extensions.testimonial.view.destroy') }}">
                                <i class="fas fa-trash"></i>&nbsp; {{ Lang::get('administrable::messages.default.delete') }}
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                @include(back_view_path('extensions.testimonials.testimonials._form'), ['edit' => true])
                            </div>
                            <div class="col-md-4">
                                @include(back_view_path('media._imagemanager'), [
                                  'front_image_label' => 'Photo',
                                  'model'             => $form->getModel(),
                                  'front_image'       => true,
                                  'back_image'        => false,
                                  'images'            => false,
                                ])
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection





