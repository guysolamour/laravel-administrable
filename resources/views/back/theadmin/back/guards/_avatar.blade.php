<div class="header-info mb-0" x-data="changeavatar" x-ref="root">
    <div class="media align-items-end">
        <img class="avatar avatar-xl avatar-bordered" :src="avatar_url" alt="{{ $model->full_name }}">
        <div class="media-body">
            <p class="stext-white opacity-90"><strong>{{ $model->full_name }}</strong></p>
            <small class=" opacity-60">{{ $model->role }}</small>
        </div>
        @if (get_guard()->can('change-' . config('administrable.guard') . '-avatar', $guard))
        <button type="button" class="btn btn-primary text-white btn-sm" id="changeavatar">
            <i class="fa fa-image"></i>&nbsp; {{ Lang::get('administrable::messages.view.guard.changeavatar') }}
        </button>
        @endif
    </div>
</div>

@guardavatar()
