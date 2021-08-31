<div class="card" x-data="changeavatar" x-ref="root">
    <div class="card-body">
        <div class="mx-auto d-block">
            <img  class="rounded-circle mx-auto d-block" :src="avatar_url"
                alt="{{ $model->full_name }}">
            <h5 class="text-sm-center mt-2 mb-1">
                <a href="{{ back_route( config('administrable.guard') . '.profile', $model) }}" title="Profil">{{ $model->full_name }}</a>
            </h5>
            <div class="location text-sm-center">
                {{ $model->role }}</div>
        </div>
        <div class="card-text text-sm-center">
            @if($model->facebook)
            <a href="{{ $model->facebook }}" target="_blank">
                <i class="fab fa-facebook pr-1 fa-2x"></i>
            </a>
            @endif
            @if($model->twitter)
            <a href="{{ $model->twitter }}" target="_blank">
                <i class="fab fa-twitter pr-1 fa-2x"></i>
            </a>
            @endif
            @if($model->linkedin)
            <a href="{{ $model->linkedin }}" target="_blank">
                <i class="fab fa-linkedin pr-1 fa-2x"></i>
            </a>
            @endif
        </div>
    </div>
    <div class="card-footer text-center">
        @if (get_guard()->can('change-' . config('administrable.guard') . '-avatar', $guard))
        <button type="button" class="btn btn-primary text-white btn-sm" id="changeavatar">
            <i class="fa fa-image"></i>&nbsp; {{ Lang::get('administrable::messages.view.guard.changeavatar') }}
        </button>
        @endif
    </div>
</div>


@guardavatar()

