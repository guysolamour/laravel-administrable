@extends(back_view_path('layouts.app'))

@section('title', Lang::get('administrable::messages.view.auth.passwordupdate'))

@section('content')
 <div class="row min-h-fullscreen center-vh p-20 m-0">
      <div class="col-12">
        <div class="card card-shadowed px-50 py-30 w-400px mx-auto" style="max-width: 100%">
          <h5 class="text-uppercase">{{ Lang::get('administrable::messages.view.auth.passwordupdate') }}</h5>
          <br>
          @if (session('status'))
              <div class="alert alert-success" role="alert">
                  {{ session('status') }}
              </div>
          @endif
          <form class="form-type-material" action="{{ route(config('administrable.guard') . '.password.update') }}" method="post">
            <input type="hidden" name="token" value="{{ $token }}">
            @csrf
            @honeypot
            <div class="form-group">
              <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email">
              <label for="email">{{ Lang::get('administrable::messages.view.auth.email') }}</label>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
              <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" required name="password"  required>
              <label for="email">{{ Lang::get('administrable::messages.view.auth.password') }}</label>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
              <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" required name="password_confirmation"  required>
              <label for="email">{{ Lang::get('administrable::messages.view.auth.passwordconfirmation') }}</label>
            </div>

            <br>
            <button class="btn btn-bold btn-block btn-primary" type="submit">{{ Lang::get('administrable::messages.view.auth.reset') }}</button>
          </form>
        </div>
      </div>


      <footer class="col-12 align-self-end text-center fs-13">
        <p class="mb-0"><small>Copyright © {{ date('Y') }} <a href="https://aswebagency.com">AswebAgency</a>. {{ Lang::get("All rights reserved.") }}</small></p>
     </footer>
    </div>
@endsection

