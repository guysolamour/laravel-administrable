<div class="card" x-data="changeavatar" x-ref="root">
    <div class="card-body box-profile">
        <div class="text-center">
            <img id="holder" :src="avatar_url" class="profile-user-img img-fluid img-circle""
                alt="{{ $model->name }}" style="cursor: pointer;">
        </div>

        <h3 class="profile-username text-center">{{ $model->full_name }}</h3>

        <p class="text-muted text-center">{{ $model->role }}</p>

        @if (get_guard()->can('change-' . config('administrable.guard') . '-avatar', $guard))
        <div class="text-center">
            <button type="button" class="btn btn-primary text-white " id="changeavatar">
                <i class="fa fa-image"></i> {{ Lang::get('administrable::messages.view.guard.changeavatar') }}
            </button>
        </div>
        @endif
    </div>
    <!-- /.card-body -->
</div>

@guardavatar()
