<div class="card">
    <div class="card-body">
        @if($errors->has('commentable_type'))
        <div class="alert alert-danger" role="alert">
            {{ $errors->first('commentable_type') }}
        </div>
        @endif
        @if($errors->has('commentable_id'))
        <div class="alert alert-danger" role="alert">
            {{ $errors->first('commentable_id') }}
        </div>
        @endif
        @if(isset($admin) && $admin)
        <p class="card-title">
            Commentez en tant que <span class="font-weight-bold">admin</span> :
            <span class="font-weight-bold font-italic">
                <a href="{{ back_route('admin.profile', auth()->guard(config('administrable.guard'))->user()) }}" target="_blank">
                    {{ auth()->guard(config('administrable.guard'))->user()->pseudo ?: auth()->guard(config('administrable.guard'))->user()->name }}
                </a>
            </span>
        </p>
        @endif
        @if(isset($user) && $user)
        <p class="card-title">
            Commentez en tant que <span class="font-weight-bold">utilisateur</span> :
             <span class="font-weight-bold font-italic"><a href="#" >
                {{ $user->pseudo }}</a>
            </span>
        </p>
        @endif
        <form method="POST" action="{{ front_route('comments.store') }}">
            @csrf
            @honeypot
            <input type="hidden" name="commentable_type" value="\{{ get_class($model) }}" />
            <input type="hidden" name="commentable_id" value="{{ $model->getKey() }}" />

            {{-- Guest commenting --}}
            @if(isset($guest_commenting) and $guest_commenting == true)
                <div class="form-group">
                    <label for="message">Enter votre nom ici:</label>
                    <input type="text" class="form-control @if($errors->has('guest_name')) is-invalid @endif"
                        name="guest_name" />
                    @error('guest_name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="message">Enter votre email ici:</label>
                    <input type="email" class="form-control @if($errors->has('guest_email')) is-invalid @endif"
                        name="guest_email" />
                    @error('guest_email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            @endif

            <div class="form-group">
              <label for="content">Enter votre commentaire ici:</label>
                <textarea class="form-control @if($errors->has('content')) is-invalid @endif" name="content" rows="3"></textarea>
                @error('content')
                <div class="invalid-feedback">
                    Votre commentaire ne doit pas être vide.
                </div>
                @enderror
            </div>
            <div class="form-group">
               <div class="custom-control custom-switch">
                    <input type="hidden"  name="reply_notification" value="off">
                    <input type="checkbox" class="custom-control-input" id="reply_notification" name="reply_notification" checked>
                    <label class="custom-control-label" for="reply_notification">M'avertir par mail lors d'une réponse à ce commentaire.</label>
                </div>
           </div>
            <button type="submit" class="btn btn-sm btn-outline-success text-uppercase">Envoyer</button>
        </form>
    </div>
</div>
<br />
