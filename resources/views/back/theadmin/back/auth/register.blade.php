@extends(back_view_path('layouts.app'))

@section('title', Lang::get('administrable::messages.view.auth.inscription'))

@section('content')
 <div class="row min-h-fullscreen center-vh p-20 m-0">
      <div class="col-12">
        <div class="card card-shadowed px-50 py-30 w-400px mx-auto" style="max-width: 100%">
          <h5 class="text-uppercase">{{ Lang::get('administrable::messages.view.auth.inscription') }}</h5>
          <br>

          <form class="form-type-material" action="{{ route(config('administrable.guard') .'.login') }}" method="post">
            @csrf
            @honeypot
            <div class="form-group">
              <input type="text" class="form-control" id="pseudo" {{ $errors->has('pseudo') ? ' is-invalid' : '' }} value="{{ old('pseudo') }}">
              <label for="pseudo">{{ Lang::get('administrable::messages.view.guard.pseudo') }}</label>
               @if ($errors->has('pseudo'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('pseudo') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
              <input type="text" class="form-control" id="last_name" {{ $errors->has('last_name') ? ' is-invalid' : '' }} value="{{ old('last_name') }}">
              <label for="last_name">{{ Lang::get('administrable::messages.view.guard.lastname') }}</label>
               @if ($errors->has('last_name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('last_name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
              <input type="text" class="form-control" id="first_name" {{ $errors->has('first_name') ? ' is-invalid' : '' }} value="{{ old('first_name') }}">
              <label for="first_name">{{ Lang::get('administrable::messages.view.guard.firstname') }}</label>
               @if ($errors->has('first_name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('first_name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
              <input type="text" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" id="email" name='email' value="{{ old('email') }}">
              <label for="email">{{ Lang::get('administrable::messages.view.guard.email') }}</label>
               @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
              <input type="password" class="form-control" id="password">
              <label for="password">{{ Lang::get('administrable::messages.view.guard.password') }}</label>
               @if ($errors->has('password'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
              <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
              <label for="password_confirmation">{{ Lang::get('administrable::messages.view.guard.passwordconfirmation') }}</label>
            </div>

            {{-- <div class="form-group">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input">
                <label class="custom-control-label">I agree to all <a class="text-primary" href="#">terms</a></label>
              </div>
            </div> --}}

            <br>
            <button class="btn btn-bold btn-block btn-primary" type="submit">
                {{ Lang::get('administrable::messages.view.auth.register') }}
            </button>
          </form>
        </div>
        <p class="text-center text-muted fs-13 mt-20"> <a class="text-primary fw-500" href="{{ route(config('administrable.guard') . '.login') }}">
            {{ Lang::get('administrable::messages.view.auth.startsession') }}</a>
        </p>
      </div>

    <footer class="col-12 align-self-end text-center fs-13">
        <p class="mb-0"><small>Copyright © {{ date('Y') }} <a href="https://aswebagency.com">AswebAgency</a>.
             {{ Lang::get("All rights reserved.") }}</small></p>
    </footer>
    </div>

@endsection
