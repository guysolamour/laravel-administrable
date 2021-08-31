<div class="card-body" x-data="changeavatar" x-ref="root">
    <div class="text-center">
        <img  :src="avatar_url" class="rounded-circle" width="150"
            alt="{{ $model->full_name }}">
        <h4 class="card-title mt-10">{{ $model->full_name }}</h4>
        <p class="card-subtitle">{{ $model->role }}</p>

        @if (get_guard()->can('change-' . config('administrable.guard') . '-avatar', $guard))
            <button type="button" class="btn btn-primary text-white " id="changeavatar">
                <i class="fa fa-image"></i> {{ Lang::get('administrable::messages.view.guard.changeavatar') }}
            </button>
        @endif
    </div>
</div>


@guardavatar()


