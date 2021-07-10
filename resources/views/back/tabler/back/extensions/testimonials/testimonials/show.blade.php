@extends(back_view_path('layouts.base'))

@section('title', $testimonial->name)


@section('content')

<div class="row mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                <li class="breadcrumb-item"><a href="{{ route(config('administrable.guard') . '.dashboard') }}">{{ Lang::get('administrable::messages.default.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ back_route('extensions.testimonial.testimonial.index') }}">{{ Lang::get('administrable::extensions.testimonial.label') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ $testimonial->name }}</a></li>
            </ol>

            <div class="btn-group">
                <a href="{{ back_route('extensions.testimonial.testimonial.edit', $testimonial) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i>&nbsp; {{ Lang::get('administrable::messages.default.edit') }}</a>
                <a href="{{ back_route('extensions.testimonial.testimonial.destroy', $testimonial) }}" class="btn btn-danger"
                    data-method="delete" data-confirm="{{ Lang::get('administrable::extensions.testimonial.view.destroy') }}">
                    <i class="fas fa-trash"></i>&nbsp; {{ Lang::get('administrable::messages.default.delete') }}</a>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <h3 class="title-5 m-b-35">
            {{ Lang::get('administrable::extensions.testimonial.label') }}
        </h3>
       <div class="row">
           <div class="col-md-8">
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
            <div class="col-md-4">
                @include(back_view_path('media._show'), [
                    'model' => $testimonial,
                ])
            </div>
       </div>
    </div>
</div>


@endsection
